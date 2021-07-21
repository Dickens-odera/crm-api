<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * User Management
 *
 * @group Users
 * Class UserController
 * @package App\Http\Controllers\api\v1
 */
class UserController extends Controller
{
    /**
     * List All Users
     *
     * @return JsonResponse
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try{
            $users = User::with('customers')->latest()->paginate(10);
            if($users->isEmpty()){
                return $this->commonResponse(false,'Users Not Found','',Response::HTTP_NOT_FOUND);
            }
            return $this->commonResponse(true,'Users List', UserResource::collection($users)->response()->getData(true),Response::HTTP_OK);
        }catch (QueryException $exception){
            return $this->commonResponse(false,$exception->errorInfo[2],'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Failed to fetch user data. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create New User
     *
     * @param UserRequest $request
     * @bodyParam name string required The User Name
     * @bodyParam email email required User Email
     * @bodyParam password password required User Password
     * @bodyParam password_confirmation password required Password Confirmation
     * @return JsonResponse
     * @authenticated
     */
    public function store(UserRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $request->rules(), $request->messages());
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')),'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $newUser = User::create(array_merge(
                $request->validated(),
                ['password' => Hash::make($request->password)]
            ));
            if($newUser){
                //TODO send the new user an invitation to set their reset their password
                return $this->commonResponse(true,'User Created successfully',new UserResource($newUser), Response::HTTP_CREATED);
            }
            return $this->commonResponse(false,'Failed to create user','',Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $exception){
            return $this->commonResponse(false,$exception->errorInfo[2],'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not create new user account. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display User Details
     *
     * @param int $id
     * @return JsonResponse
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        //
    }

    /**
     * Update User Details
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @authenticated
     */
    public function update(Request $request, $id): JsonResponse
    {
        //
    }

    /**
     * Delete User
     *
     * @param int $id
     * @return JsonResponse
     * @authenticated
     */
    public function destroy($id): JsonResponse
    {
        //
    }
}
