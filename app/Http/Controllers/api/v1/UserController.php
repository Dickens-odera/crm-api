<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            $users = User::with('customers')->paginate(10);
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
     * @param Request $request
     * @return JsonResponse
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        //
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
