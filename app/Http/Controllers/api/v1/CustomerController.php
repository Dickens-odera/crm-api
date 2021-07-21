<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Customers
 * Class CustomerController
 * @package App\Http\Controllers\api\v1
 */
class CustomerController extends Controller
{
    /**
     * List All Customers
     * @return JsonResponse
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try{
            $customers = Customer::with('owner','updator')->paginate(10);
            if($customers->isEmpty()){
                return $this->commonResponse(false,'Customers Not Found','', Response::HTTP_NOT_FOUND);
            }
            return $this->commonResponse(true,'success', CustomerResource::collection($customers)->response()->getData(true),Response::HTTP_OK);
        }catch (QueryException $exception){
            return $this->commonResponse(false, $exception->errorInfo[2], '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not fetch customer list. ERROR '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create New Customer
     * @param CustomerRequest $request
     * @bodyParam name string required Customer's Name.
     * @bodyParam surname string required Customer's Surname.
     * @bodyParam photo_url file Customer's Photo
     * @return JsonResponse
     * @authenticated
     */
    public function store(CustomerRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $request->rules(), $request->messages());
        if($validator->fails()){
            return $this->commonResponse(false,Arr::flatten($validator->messages()->get('*')),'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try{
            $photo_url = null;
            if($request->hasFile('photo_url')){
                $photo_url = $request->file('photo_url')->store('customers/avatars/'.$request->name); //TODO update to s3
            }
            $customerData = [
                'name' => $request->name,
                'surname' => $request->surname,
                'photo_url' => $photo_url
            ];
            $newCustomer = Customer::create(array_merge($customerData,['added_by' => $request->user()->id]));
            if($newCustomer){
                return $this->commonResponse(true,'Customer Created Successfully',new CustomerResource($newCustomer),Response::HTTP_CREATED);
            }
            return $this->commonResponse(false,'Failed to create Customer','',Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $exception){
            return $this->commonResponse(false, $exception->errorInfo[2], '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not create a new customer. ERROR '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display Customer Details
     *
     * @param $id
     * @urlParam id integer required The Customer ID.
     * @return JsonResponse
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        $customer = Customer::with('owner','updator')->find($id);
        try{
            if($customer){
                return $this->commonResponse(true,'Customer Details', new CustomerResource($customer->load('owner','updator')),Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Customer Not Found','',Response::HTTP_NOT_FOUND);
        }catch (QueryException $exception){
            return $this->commonResponse(false, $exception->errorInfo[2], '', Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not fetch customer details. ERROR '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Customer
     *
     * @param Request $request
     * @param $id
     * @bodyParam name string required .The Customer's name
     * @bodyParam surname string required . The Customer's surname
     * @bodyParam photo_url file The new customer's photo
     * @urlParam id integer required Customer ID. Example 1
     * @return JsonResponse
     * @authenticated
     */
    public function update(Request $request,$id): JsonResponse
    {
        $customer = Customer::with('owner','updator')->find($id);
        try{
            if(!$customer){
                return $this->commonResponse(false,'Customer Not Found','',Response::HTTP_NOT_FOUND);
            }
            $photo_url = null;
            if($request->hasFile('photo_url')){
                $photo_url = $request->file('photo_url')->store('customers/avatars/'.$request->name); //TODO update to s3
            }
            $customerData = [
                'name' => $request->name,
                'surname' => $request->surname,
                'photo_url' => $photo_url ?? $customer->photo_url
            ];
            $customerUpdate = $customer->update(array_merge(
                $customerData,
                ['updated_by' => $request->user()->id]
            ));
            if($customerUpdate){
                return $this->commonResponse(true,'Customer Updated successfully',new CustomerResource($customer->load('owner','updator')),Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Failed to update customer details','',Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $exception){
            return $this->commonResponse(false,$exception->errorInfo[2],'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not update customer. ERROR '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Customer
     *
     * @param $id
     * @urlParam id integer required The Customer ID Example:1
     * @return JsonResponse
     * @authenticated
     */
    public function destroy($id): JsonResponse
    {
        try{
            $customer = Customer::with('owner','updator')->find($id);
            if(!$customer){
                return $this->commonResponse(false,'Customer Not Found','',Response::HTTP_NOT_FOUND);
            }
            if($customer->delete()){
                return $this->commonResponse(true,'Customer Deleted successfully','',Response::HTTP_OK);
            }
            return $this->commonResponse(false,'Failed to delete customer','',Response::HTTP_EXPECTATION_FAILED);
        }catch (QueryException $exception){
            return $this->commonResponse(false,$exception->errorInfo[2],'',Response::HTTP_UNPROCESSABLE_ENTITY);
        }catch (Exception $exception){
            Log::critical('Could not delete customer details. ERROR: '.$exception->getTraceAsString());
            return $this->commonResponse(false,$exception->getMessage(),'',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
