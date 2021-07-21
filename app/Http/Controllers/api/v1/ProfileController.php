<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use http\Client\Curl\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
/**
 * Profile Management
 * @group Profile
 * Class ProfileController
 * @package App\Http\Controllers\api\v1
 */
class ProfileController extends Controller
{
    /**
     * Display User Profile
     * @param Request $request
     * @return JsonResponse
     * @authenticated
     */
    public function profile(Request $request): JsonResponse
    {
        try{
            $user = $request->user();
            return $this->commonResponse(true,'User Profile', new UserResource($user), Response::HTTP_OK);
        }catch (QueryException $queryException){
            return $this->commonResponse(false,$queryException->errorInfo[2],'', Response::HTTP_EXPECTATION_FAILED);
        }catch (Exception $exception){
            Log::critical('Could not fetch user profile. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Profile
     * @param UserUpdateRequest $request
     * @bodyParam name string required User Name
     * @bodyParam email email required User Email
     * @return JsonResponse
     * @authenticated
     */
    public function update(UserUpdateRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(),$request->rules());
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')),'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $user = $request->user();
            if($user->update($request->validated())){
                return $this->commonResponse(true,'Profile Updated successfully',new UserResource($user), Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Failed to update profile','',Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $queryException){
            return $this->commonResponse(false,$queryException->errorInfo[2],'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Failed to update profile. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false, $exception->getMessage(),'', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
