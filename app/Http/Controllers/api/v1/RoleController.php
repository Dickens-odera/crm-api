<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RoleRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource;


/** Role Management
 * @group Roles
 * Class RoleController
 * @package App\Http\Controllers\api\v1
 */
class RoleController extends Controller
{
    /**
     * List All Roles
     * @return JsonResponse
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try{
            $roles = Role::latest()->paginate(10);
            if($roles->isEmpty()){
                return $this->commonResponse(false,'User Roles Not Found','', Response::HTTP_NOT_FOUND);
            }
            return $this->commonResponse(true,'User Roles', RoleResource::collection($roles)->response()->getData(true),Response::HTTP_OK);
        }catch (QueryException $queryException){
            return $this->commonResponse(false,$queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not fetch the list of roles. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create New Role
     *
     * @param RoleRequest $request
     * @bodyParam name string required Role Name.
     * @return JsonResponse
     * @authenticated
     */
    public function store(RoleRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $request->rules());
        if($validator->fails()){
            return $this->commonResponse(false, Arr::flatten($validator->messages()->get('*')),'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $newRole = Role::create(array_merge($request->validated(),['guard_name' => 'api']));
            if($newRole){
                return $this->commonResponse(true,'Role Created Successfully',new RoleResource($newRole), Response::HTTP_CREATED);
            }
            return $this->commonResponse(false,'Role Not Created','', Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $queryException){
            return $this->commonResponse(false, $queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could Not Create Role. ERROR: '. $exception->getTraceAsString());
            return $this->commonResponse(false, $exception->getMessage(),'', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display Role Details
     *
     * @param int $id
     * @urlParam id integer required The Role ID
     * @return JsonResponse
     * @authenticated
     */
    public function show(int $id): JsonResponse
    {
        try{
            $role = Role::findById($id,'api');
            if(!$role){
                return $this->commonResponse(false,'Role Not Found','', Response::HTTP_NOT_FOUND);
            }
            return $this->commonResponse(true,'Role Details', new RoleResource($role),Response::HTTP_OK);
        }catch (QueryException $queryException){
            return $this->commonResponse(false,$queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not fetch role details. ERROR: '. $exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Role
     *
     * @param RoleUpdateRequest $request
     * @param int $id
     * @bodyParam name string required Role Name
     * @urlParam id integer required The Role ID
     * @return JsonResponse
     * @authenticated
     */
    public function update(RoleUpdateRequest $request,int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), $request->rules());
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')), '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $role = Role::findById($id,'api');
            if(!$role){
                return $this->commonResponse(false,'Role Not Found','', Response::HTTP_NOT_FOUND);
            }
            if($role->update($request->validated())){
                return $this->commonResponse(true,'Role Updated Successfully', new RoleResource($role), Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Role Not Updated','', Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $exception){
            return $this->commonResponse(false,$exception->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('');
            return $this->commonResponse(false,$exception->getMessage(),'', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Role
     * @param int $id
     * @urlParam id integer required Role ID
     * @return JsonResponse
     * @authenticated
     */
    public function destroy( int $id ): JsonResponse
    {
        try{
            $role = Role::findById($id,'api');
            if(!$role){
                return $this->commonResponse(false,'Role Not Found','', Response::HTTP_NOT_FOUND);
            }
            if($role->delete()){
                return $this->commonResponse(true,'Role Deleted Successfully','',Response::HTTP_OK);
            }
            return $this->commonResponse(false, 'Role Not Deleted','', Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $queryException){
            return $this->commonResponse(false, $queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('');
            return $this->commonResponse(false, $exception->getMessage(),'', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
