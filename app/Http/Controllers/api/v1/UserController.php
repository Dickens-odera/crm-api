<?php

namespace App\Http\Controllers\api\v1;

use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
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
                UserCreated::dispatch($newUser); //assign the user a user role
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
     * @urlParam id integer required User ID
     * @return JsonResponse
     * @authenticated
     */
    public function show(int $id): JsonResponse
    {
        try{
            $user = User::with('customers')->find($id);
            if(!$user){
                return $this->commonResponse(false,'User Not Found','',Response::HTTP_NOT_FOUND);
            }
            return $this->commonResponse(true,'User Details',new UserResource($user),Response::HTTP_OK);
        }catch (QueryException $exception){
            return $this->commonResponse(false,$exception->errorInfo[2],'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not fetch user details. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update User Details
     *
     * @param UserUpdateRequest $request
     * @param int $id
     * @bodyParam name string required The name of the user
     * @bodyParam email string required The email of the user
     * @urlParam id integer required The User ID.
     * @return JsonResponse
     * @authenticated
     */
    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), $request->rules());
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')),'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $user = User::with('customers')->find($id);
            if(!$user){
                return $this->commonResponse(false,'User Not Found','',Response::HTTP_NOT_FOUND);
            }
            if($user->update($request->validated())){
                return $this->commonResponse(true,'User Details Updated Successfully', new UserResource($user),Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Failed to update user details','',Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $queryException){
            return $this->commonResponse(false,$queryException->errorInfo[2],'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not update user details. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete User
     *
     * @param int $id
     * @urlParam id integer required The ID of the User
     * @return JsonResponse
     * @authenticated
     */
    public function destroy(int $id): JsonResponse
    {
        try{
            $user = User::with('customers')->find($id);
            if(!$user){
                return $this->commonResponse(false,'User Not Found','', Response::HTTP_NOT_FOUND);
            }
            if($user->delete()){
                return $this->commonResponse(true,'User Deleted Successfully','',Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Failed to delete user','',Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $queryException){
            return $this->commonResponse(false,$queryException->errorInfo[2],'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not delete user. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change Admin Status
     * @param int $id
     * @urlParam id integer required The User ID
     * @return JsonResponse
     * @authenticated
     */
    public function makeAdmin(int $id): JsonResponse
    {
        try{
            $user = User::with('customers')->find($id);
            if(!$user){
                return $this->commonResponse(false,'User Not Found','', Response::HTTP_NOT_FOUND);
            }
            $adminRole = Role::findOrCreate('admin','api');
            $userRole  = Role::findOrCreate('user','api');
            if($user->hasRole($adminRole)){
                return $this->commonResponse(false,'This user has an admin status already','', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            if($user->assignRole($adminRole)){
                return $this->commonResponse(true,'User admin status changed successfully', '', Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Failed to change admin status','', Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $queryException){
            return $this->commonResponse(false,$queryException->errorInfo[2],'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not change user to admin status. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
