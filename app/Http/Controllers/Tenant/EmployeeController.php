<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreEmployeeRequest;
use App\Http\Requests\Tenant\UpdateEmployeeRequest;
use App\Models\Tenant\Employee;
use App\Models\Tenant\EmployeeHireRecord;
use App\Models\Tenant\EmployeeSchedule;
use App\Models\Tenant\Salon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Employee::with(['user', 'activeSchedule.salon', 'hireRecords'])
                ->select('employees.*');

            if ($request->filled('salon_id')) {
                $salonId = $request->get('salon_id');
                $query->whereHas('schedules', function ($q) use ($salonId) {
                    $q->where('salon_id', $salonId)->where('is_active', 1);
                });
            }

            if ($request->filled('status') && $request->get('status') !== '') {
                $query->where('employees.status', (int)$request->get('status'));
            }

            return DataTables::of($query)
                ->addColumn('employee_info', function ($emp) {
                    $initials = strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1));
                    $avatar = '<div style="width: 36px; height: 36px; border-radius: 8px; background: #e0e7ff; color: #4338ca; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; margin-right: 12px;">' . e($initials) . '</div>';
                    $name = '<div style="display: inline-block; vertical-align: middle;">'
                        . '<a href="' . route('tenant.employees.show', $emp->id) . '" style="font-weight: 600; color: var(--text-main); text-decoration: none;">' . e($emp->first_name . ' ' . $emp->last_name) . '</a>'
                        . '<div style="font-size: 12px; color: var(--text-muted);">' . e($emp->email ?? 'No email') . '</div>'
                        . '</div>';
                    return '<div style="display: flex; align-items: center;">' . $avatar . $name . '</div>';
                })
                ->addColumn('contact', function ($emp) {
                    $phone = $emp->phone_no ? '<div>📞 ' . e($emp->phone_no) . '</div>' : '<span style="color: var(--text-muted);">-</span>';
                    $city = $emp->city ? '<div style="font-size: 11px; color: var(--text-muted);">📍 ' . e($emp->city) . '</div>' : '';
                    return $phone . $city;
                })
                ->addColumn('salon', function ($emp) {
                    if ($emp->activeSchedule && $emp->activeSchedule->salon) {
                        return '<span class="badge" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; font-weight: 600;">🏪 ' . e($emp->activeSchedule->salon->salon_name) . '</span>';
                    }
                    return '<span class="badge" style="background: #f1f5f9; color: #64748b;">Unassigned</span>';
                })
                ->addColumn('shift', function ($emp) {
                    if ($emp->activeSchedule && $emp->activeSchedule->start_time) {
                        $start = Carbon::parse($emp->activeSchedule->start_time)->format('h:i A');
                        $end = Carbon::parse($emp->activeSchedule->end_time)->format('h:i A');
                        return '<span style="font-size: 12px; font-weight: 500;">⏰ ' . $start . ' - ' . $end . '</span>';
                    }
                    return '<span style="font-size: 12px; color: var(--text-muted);">Not Scheduled</span>';
                })
                ->addColumn('status_badge', function ($emp) {
                    if ($emp->status == 1) {
                        return '<span class="badge badge-success">Active</span>';
                    } elseif ($emp->status == 0) {
                        return '<span class="badge badge-danger">Inactive</span>';
                    } else {
                        return '<span class="badge" style="background: #fee2e2; color: #991b1b;">Terminated</span>';
                    }
                })
                ->addColumn('actions', function ($emp) {
                    $showUrl = route('tenant.employees.show', $emp->id);
                    $editUrl = route('tenant.employees.edit', $emp->id);
                    $deleteUrl = route('tenant.employees.destroy', $emp->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div style="display: flex; gap: 6px; justify-content: flex-end;">'
                        . '<a href="' . $showUrl . '" class="btn-action" title="View Profile" style="color: #4f46e5; background: #eef2ff; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">View</a>'
                        . '<a href="' . $editUrl . '" class="btn-action" title="Edit Employee" style="color: #0284c7; background: #e0f2fe; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Edit</a>'
                        . '<form action="' . $deleteUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this employee?\');">'
                        . $csrf . $method
                        . '<button type="submit" class="btn-action" title="Delete" style="color: #ef4444; background: #fee2e2; padding: 6px 10px; border-radius: 6px; border: none; font-size: 12px; font-weight: 600; cursor: pointer;">Delete</button>'
                        . '</form>'
                        . '</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone_no', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['employee_info', 'contact', 'salon', 'shift', 'status_badge', 'actions'])
                ->make(true);
        }

        $salons = Salon::where('status', 1)->orderBy('salon_name')->get();
        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', 1)->count(),
            'inactive' => Employee::where('status', 0)->count(),
            'terminated' => Employee::where('status', 2)->count(),
        ];

        return view('tenant.employees.index', compact('salons', 'stats'));
    }

    /**
     * Show form for creating a new employee.
     */
    public function create(): View
    {
        $salons = Salon::where('status', 1)->orderBy('salon_name')->get();
        return view('tenant.employees.create', compact('salons'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $employee = Employee::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'] ?? null,
                'phone_no' => $validated['phone_no'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'joining_date' => $validated['joining_date'] ?? now()->toDateString(),
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'zipcode' => $validated['zipcode'] ?? null,
                'status' => $validated['status'] ?? 1,
            ]);

            // Create initial hire record
            EmployeeHireRecord::create([
                'employee_id' => $employee->id,
                'hire_date' => $employee->joining_date ?? now()->toDateString(),
                'reason' => 'Initial salon onboarding',
            ]);

            // Assign store branch schedule if provided
            if (!empty($validated['salon_id'])) {
                $startTime = $validated['start_time'] ?? ($validated['shift_start_time'] ?? '09:00:00');
                $endTime = $validated['end_time'] ?? ($validated['shift_end_time'] ?? '18:00:00');

                EmployeeSchedule::create([
                    'employee_user_id' => $employee->id,
                    'salon_id' => $validated['salon_id'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_active' => 1,
                ]);
            }

            DB::commit();

            return redirect()->route('tenant.employees.index')
                ->with('success', "Employee {$employee->first_name} {$employee->last_name} onboarded successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error onboarding employee: ' . $e->getMessage());
        }
    }

    /**
     * Display employee profile & timeline.
     */
    public function show(Employee $employee): View
    {
        $employee->load(['user', 'schedules.salon', 'hireRecords']);
        return view('tenant.employees.show', compact('employee'));
    }

    /**
     * Show form for editing employee.
     */
    public function edit(Employee $employee): View
    {
        $employee->load(['activeSchedule.salon']);
        $salons = Salon::where('status', 1)->orderBy('salon_name')->get();

        return view('tenant.employees.edit', compact('employee', 'salons'));
    }

    /**
     * Update existing employee.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $prevStatus = $employee->status;
            $newStatus = (int)($validated['status'] ?? 1);

            $employee->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'] ?? null,
                'phone_no' => $validated['phone_no'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'joining_date' => $validated['joining_date'] ?? $employee->joining_date,
                'rejoin_date' => $validated['rejoin_date'] ?? $employee->rejoin_date,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'zipcode' => $validated['zipcode'] ?? null,
                'status' => $newStatus,
            ]);

            // Status transition hire record
            if ($prevStatus != 2 && $newStatus == 2) {
                EmployeeHireRecord::create([
                    'employee_id' => $employee->id,
                    'termination_date' => now()->toDateString(),
                    'reason' => $validated['termination_reason'] ?? 'Status changed to Terminated',
                ]);
            } elseif ($prevStatus == 2 && $newStatus == 1) {
                $employee->update(['rejoin_date' => now()->toDateString()]);
                EmployeeHireRecord::create([
                    'employee_id' => $employee->id,
                    'hire_date' => now()->toDateString(),
                    'reason' => 'Employee rehired/reactivated',
                ]);
            }

            // Update or create schedule
            if (!empty($validated['salon_id'])) {
                $startTime = $validated['start_time'] ?? ($validated['shift_start_time'] ?? '09:00:00');
                $endTime = $validated['end_time'] ?? ($validated['shift_end_time'] ?? '18:00:00');

                EmployeeSchedule::where('employee_user_id', $employee->id)->update(['is_active' => 0]);

                EmployeeSchedule::create([
                    'employee_user_id' => $employee->id,
                    'salon_id' => $validated['salon_id'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_active' => 1,
                ]);
            }

            DB::commit();

            return redirect()->route('tenant.employees.index')
                ->with('success', "Employee {$employee->first_name} updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    /**
     * Delete employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        try {
            $name = $employee->first_name . ' ' . $employee->last_name;
            $employee->delete();
            return redirect()->route('tenant.employees.index')
                ->with('success', "Employee {$name} deleted successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }
}
