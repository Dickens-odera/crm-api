<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

/**
 * Permission Management
 * @group Permissions
 * Class PermissionController
 * @package App\Http\Controllers\api\v1
 */
class PermissionController extends Controller
{
    /**
     * List All Permissions
     *
     * @return JsonResponse
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try{
            $permissions = Permission::latest()->paginate(10);
            if($permissions->isEmpty()){
                return $this->commonResponse(false,'Permissions Not Found','', Response::HTTP_NOT_FOUND);
            }
            return $this->commonResponse(true,'User Permissions', PermissionResource::collection($permissions)->response()->getData(true), Response::HTTP_OK);
        }catch (QueryException $queryException){
            return $this->commonResponse(false,$queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('');
            return $this->commonResponse(false,$exception->getMessage(),'', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create New Permission
     *
     * @param PermissionRequest $request
     * @bodyParam name string required The Permission Name
     * @return JsonResponse
     * @authenticated
     */
    public function store(PermissionRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $request->rules());
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')), '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $newPermission = Permission::create(array_merge($request->validated(),['guard_name' => 'api']));
            if($newPermission){
                return $this->commonResponse(true,'Permission Created Successfully',new PermissionResource($newPermission),Response::HTTP_CREATED);
            }
            return $this->commonResponse(false,'Failed to create user permission','',Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $exception){
            return $this->commonResponse(false, $exception->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Failed To Create New Permission: ERROR: '. $exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display Permission Details
     *
     * @param int $id
     * @urlParam id integer required The Permission ID
     * @return JsonResponse
     * @authenticated
     */
    public function show(int $id): JsonResponse
    {
        try{
            $permission = Permission::findById($id);
            if(!$permission){
                return $this->commonResponse(false,'Permission Not Found','', Response::HTTP_NOT_FOUND);
            }
            return $this->commonResponse(true,'User Permission',new PermissionResource($permission), Response::HTTP_OK);
        }catch (QueryException $queryException){
            return $this->commonResponse(false, $queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Failed To Fetch Permission Details: ERROR: '. $exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Permission
     *
     * @param PermissionUpdateRequest $request
     * @param int $id
     * @bodyParam name string required The Permission Name
     * @urlParam id integer required The Permission ID
     * @return JsonResponse
     * @authenticated
     */
    public function update(PermissionUpdateRequest $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), $request->rules());
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')), '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $permission = Permission::findById($id);
            if(!$permission){
                return $this->commonResponse(false,'Permission Not Found','', Response::HTTP_NOT_FOUND);
            }
            if($permission->update($request->validated())){
                return $this->commonResponse(true,'Permission Updated Successfully',new PermissionResource($permission),Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Failed To Update Permission','', Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $queryException){
            return $this->commonResponse(false, $queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Failed To Update Permission Details: ERROR: '. $exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Permission
     *
     * @param int $id
     * @urlParam id integer required The Permission ID.
     * @return JsonResponse
     * @authenticated
     */
    public function destroy(int $id): JsonResponse
    {
        try{
            $permission = Permission::findById($id);
            if(!$permission){
                return $this->commonResponse(false,'Permission Not Found','', Response::HTTP_NOT_FOUND);
            }
            if($permission->delete()){
                return $this->commonResponse(true, 'Permission Deleted Successfully','', Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Failed To Delete Permission','', Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $queryException){
            return $this->commonResponse(false, $queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Failed To Delete Permission: ERROR: '. $exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
