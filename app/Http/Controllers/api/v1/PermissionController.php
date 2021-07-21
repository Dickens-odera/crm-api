<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\PermissionUpdateRequest;
use Illuminate\Http\JsonResponse;

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
        //
    }

    /**
     * Create New Permission
     *
     * @param PermissionRequest $request
     * @return JsonResponse
     * @authenticated
     */
    public function store(PermissionRequest $request): JsonResponse
    {
        //
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
        //
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
        //
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
        //
    }
}
