<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TaskCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaskCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = TaskCategory::withCount('questions')->select('task_categorys.*');

            if ($request->filled('status') && $request->get('status') !== '') {
                $query->where('status', (int)$request->get('status'));
            }

            return DataTables::of($query)
                ->addColumn('order_badge', function ($cat) {
                    return '<span class="badge" style="background: #f1f5f9; color: #475569; font-weight: 700;">#' . ($cat->order_by_id ?? 0) . '</span>';
                })
                ->addColumn('category_info', function ($cat) {
                    return '<div style="font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">'
                        . '<span>📂</span> ' . e($cat->task_category_name)
                        . '</div>';
                })
                ->addColumn('tasks_count', function ($cat) {
                    return '<span class="badge" style="background: #ede9fe; color: #6d28d9; font-weight: 600;">📋 ' . $cat->questions_count . ' Tasks</span>';
                })
                ->addColumn('status_badge', function ($cat) {
                    return $cat->status == 1
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($cat) {
                    $editUrl = route('tenant.task-categories.edit', $cat->id);
                    $deleteUrl = route('tenant.task-categories.destroy', $cat->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div style="display: flex; gap: 6px; justify-content: flex-end;">'
                        . '<a href="' . $editUrl . '" class="btn-action" title="Edit Category" style="color: #0284c7; background: #e0f2fe; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Edit</a>'
                        . '<form action="' . $deleteUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this category?\');">'
                        . $csrf . $method
                        . '<button type="submit" class="btn-action" title="Delete" style="color: #ef4444; background: #fee2e2; padding: 6px 10px; border-radius: 6px; border: none; font-size: 12px; font-weight: 600; cursor: pointer;">Delete</button>'
                        . '</form>'
                        . '</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where('task_category_name', 'like', "%{$search}%");
                    }
                })
                ->rawColumns(['order_badge', 'category_info', 'tasks_count', 'status_badge', 'actions'])
                ->make(true);
        }

        $stats = [
            'total' => TaskCategory::count(),
            'active' => TaskCategory::where('status', 1)->count(),
            'inactive' => TaskCategory::where('status', 0)->count(),
        ];

        return view('tenant.task_categories.index', compact('stats'));
    }

    public function create()
    {
        $maxOrder = TaskCategory::max('order_by_id') ?? 0;
        return view('tenant.task_categories.create', ['nextOrder' => $maxOrder + 1]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_category_name' => 'required|string|max:255',
            'order_by_id' => 'nullable|integer',
            'status' => 'required|in:0,1'
        ]);

        TaskCategory::create($validated);

        return redirect()->route('tenant.task-categories.index')->with('success', 'Task Category created successfully.');
    }

    public function edit(TaskCategory $taskCategory)
    {
        return view('tenant.task_categories.edit', compact('taskCategory'));
    }

    public function update(Request $request, TaskCategory $taskCategory)
    {
        $validated = $request->validate([
            'task_category_name' => 'required|string|max:255',
            'order_by_id' => 'nullable|integer',
            'status' => 'required|in:0,1'
        ]);

        $taskCategory->update($validated);

        return redirect()->route('tenant.task-categories.index')->with('success', 'Task Category updated successfully.');
    }

    public function destroy(TaskCategory $taskCategory)
    {
        $taskCategory->delete();
        return redirect()->route('tenant.task-categories.index')->with('success', 'Task Category deleted successfully.');
    }
}