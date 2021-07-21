<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Brick\Math\Exception\IntegerOverflowException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Arr;
/**
 * Class AuthController
 * @package App\Http\Controllers\api\v1
 * @group Auth
 * User Authentication
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     * @param UserRequest $request
     * @bodyParam  name string required The name of the user.
     * @bodyParam  email string required The email address of the user.
     * @bodyParam  password password required  Password.
     * @bodyParam  confirm_password password required Password confirmation.
     * @return JsonResponse
     */
    public function register(UserRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $request->rules(), $request->messages());
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')),'', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $user = User::create(
                array_merge($request->validated(),['password' => Hash::make($request->password)])
            );
            if(!$user){
                return $this->commonResponse(false,'Registration Unsuccessful, please try again', '', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }catch(QueryException $exception){
            return $this->commonResponse(false, $exception->errorInfo[2], '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch(Exception $exception){
            Log::critical('Something went wrong registering a new user. ERROR:'. $exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->commonResponse(true,'Registration successful', new UserResource($user), Response::HTTP_CREATED);
    }
}
