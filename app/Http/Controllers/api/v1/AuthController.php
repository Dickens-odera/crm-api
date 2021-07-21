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
     * User Registration
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
            $newUser = User::create(
                array_merge($request->validated(),['password' => Hash::make($request->password)])
            );
            if(!$newUser){
                return $this->commonResponse(false,'Registration Unsuccessful, please try again', '', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return $this->commonResponse(true,'Registration successful', new UserResource($newUser), Response::HTTP_CREATED);
        }catch(QueryException $exception){
            return $this->commonResponse(false, $exception->errorInfo[2], '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch(Exception $exception){
            Log::critical('Something went wrong registering a new user. ERROR:'. $exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User Login
     * @param Request $request
     * @bodyParam email string required Email Address
     * @bodyParam password password required Password
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];
        $messages = [
            'email.required' => 'Please enter your email address',
            'password.required' => 'Please enter your password'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')),'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $user = User::firstWhere('email', $request->email);
            if (!$user) {
                return $this->commonResponse(false, 'A user with the provided credentials could not be found', '', Response::HTTP_EXPECTATION_FAILED);
            }
            if(!Hash::check($request->password, $user->password)) {
                return $this->commonResponse(false,'Invalid password','',Response::HTTP_EXPECTATION_FAILED);
            }
            $data = [
                'user' => new UserResource($user),
                'accessToken' => $user->createToken('crm-user')->plainTextToken //generate an access token for the user
            ];
            return $this->commonResponse(true,'Login Success', $data,Response::HTTP_OK);
        }catch (QueryException $exception){
            return $this->commonResponse(false, $exception->errorInfo[2], '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Something went wrong logging in the user. ERROR:'. $exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User Logout
     * @param Request $request
     * @return JsonResponse
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        try{
            $user = $request->user();
            if($user->tokens()->delete()){
                return $this->commonResponse(true,'Logout Successful','',Response::HTTP_OK);
            }
            return $this->commonResponse(true,'Failed to logout','',Response::HTTP_EXPECTATION_FAILED);
        }catch (Exception $exception){
            Log::critical('Failed to perform user logout. ERROR '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
