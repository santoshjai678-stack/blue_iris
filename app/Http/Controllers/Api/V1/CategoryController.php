<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tenant\TaskCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    /**
     * Get Active Task Categories
     */
    public function index(Request $request): JsonResponse
    {
        $categories = TaskCategory::where('status', 1)
            ->orderBy('order_by_id')
            ->get(['id', 'task_category_name', 'order_by_id']);

        return $this->sendResponse($categories, 'Task categories fetched.');
    }
}