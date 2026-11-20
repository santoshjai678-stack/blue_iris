<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Designation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Designation::withCount(['employees', 'questions'])->select('designations.*');

            if ($request->filled('status') && $request->get('status') !== '') {
                $query->where('status', (int)$request->get('status'));
            }

            return DataTables::of($query)
                ->addColumn('designation_info', function ($desig) {
                    $desc = $desig->description ? '<div style="font-size: 12px; color: var(--text-muted);">' . e($desig->description) . '</div>' : '';
                    return '<div style="font-weight: 700; color: var(--text-main);">💼 ' . e($desig->designation) . '</div>' . $desc;
                })
                ->addColumn('employees_count', function ($desig) {
                    return '<span class="badge" style="background: #eef2ff; color: #4f46e5; font-weight: 600;">👥 ' . $desig->employees_count . ' Staff</span>';
                })
                ->addColumn('tasks_count', function ($desig) {
                    return '<span class="badge" style="background: #ede9fe; color: #6d28d9; font-weight: 600;">📋 ' . $desig->questions_count . ' Tasks</span>';
                })
                ->addColumn('status_badge', function ($desig) {
                    return $desig->status == 1
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($desig) {
                    $editUrl = route('tenant.designations.edit', $desig->id);
                    $deleteUrl = route('tenant.designations.destroy', $desig->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div style="display: flex; gap: 6px; justify-content: flex-end;">'
                        . '<a href="' . $editUrl . '" class="btn-action" title="Edit Designation" style="color: #0284c7; background: #e0f2fe; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Edit</a>'
                        . '<form action="' . $deleteUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this designation?\');">'
                        . $csrf . $method
                        . '<button type="submit" class="btn-action" title="Delete" style="color: #ef4444; background: #fee2e2; padding: 6px 10px; border-radius: 6px; border: none; font-size: 12px; font-weight: 600; cursor: pointer;">Delete</button>'
                        . '</form>'
                        . '</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where('designation', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    }
                })
                ->rawColumns(['designation_info', 'employees_count', 'tasks_count', 'status_badge', 'actions'])
                ->make(true);
        }

        $stats = [
            'total' => Designation::count(),
            'active' => Designation::where('status', 1)->count(),
            'inactive' => Designation::where('status', 0)->count(),
        ];

        return view('tenant.designations.index', compact('stats'));
    }

    public function create()
    {
        return view('tenant.designations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1'
        ]);

        Designation::create($validated);

        return redirect()->route('tenant.designations.index')->with('success', 'Designation created successfully.');
    }

    public function edit(Designation $designation)
    {
        return view('tenant.designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1'
        ]);

        $designation->update($validated);

        return redirect()->route('tenant.designations.index')->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('tenant.designations.index')->with('success', 'Designation deleted successfully.');
    }
}