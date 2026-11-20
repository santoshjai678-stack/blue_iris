<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Salon;
use App\Models\Tenant\SalonTiming;
use App\Models\Tenant\Country;
use App\Models\Tenant\State;
use App\Http\Requests\Tenant\StoreSalonRequest;
use App\Http\Requests\Tenant\UpdateSalonRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalonController extends Controller
{
    protected array $daysOfWeek = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Salon::with(['country', 'stateRel', 'timings'])->withCount('employees')
                ->select('salons.*');

            if ($request->filled('status') && $request->get('status') !== '') {
                $query->where('salons.status', (int)$request->get('status'));
            }

            return DataTables::of($query)
                ->addColumn('store_details', function ($salon) {
                    $code = $salon->salon_code ? '<span class="badge" style="background:#f1f5f9; color:#475569; font-size:10px; margin-left:6px;">' . e($salon->salon_code) . '</span>' : '';
                    $addr = $salon->address_1 ? '<div style="font-size: 12px; color: var(--text-muted);">' . e($salon->address_1) . '</div>' : '';
                    return '<div><a href="' . route('tenant.salons.show', $salon->id) . '" style="font-weight: 700; color: var(--text-main); text-decoration: none;">🏪 ' . e($salon->salon_name) . '</a>' . $code . $addr . '</div>';
                })
                ->addColumn('location', function ($salon) {
                    $stateName = $salon->stateRel->name ?? $salon->state ?? '';
                    $loc = e($salon->city ?? '-');
                    if ($stateName) {
                        $loc .= ', ' . e($stateName);
                    }
                    if ($salon->zipcode) {
                        $loc .= ' (' . e($salon->zipcode) . ')';
                    }
                    return '<div style="font-size: 13px;">📍 ' . $loc . '</div>';
                })
                ->addColumn('contact', function ($salon) {
                    $phone = $salon->salon_phone ? '<div>📞 ' . e($salon->salon_phone) . '</div>' : '<span style="color: var(--text-muted);">-</span>';
                    $email = $salon->salon_email ? '<div style="font-size: 11px; color: var(--text-muted);">✉️ ' . e($salon->salon_email) . '</div>' : '';
                    return $phone . $email;
                })
                ->addColumn('staff_count', function ($salon) {
                    return '<span class="badge" style="background: #eef2ff; color: #4f46e5; font-weight: 600;">👥 ' . $salon->employees_count . ' Staff</span>';
                })
                ->addColumn('status_badge', function ($salon) {
                    return $salon->status == 1
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($salon) {
                    $showUrl = route('tenant.salons.show', $salon->id);
                    $editUrl = route('tenant.salons.edit', $salon->id);
                    $deleteUrl = route('tenant.salons.destroy', $salon->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div style="display: flex; gap: 6px; justify-content: flex-end;">'
                        . '<a href="' . $showUrl . '" class="btn-action" title="View Store" style="color: #4f46e5; background: #eef2ff; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">View</a>'
                        . '<a href="' . $editUrl . '" class="btn-action" title="Edit Store" style="color: #0284c7; background: #e0f2fe; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Edit</a>'
                        . '<form action="' . $deleteUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this store?\');">'
                        . $csrf . $method
                        . '<button type="submit" class="btn-action" title="Delete" style="color: #ef4444; background: #fee2e2; padding: 6px 10px; border-radius: 6px; border: none; font-size: 12px; font-weight: 600; cursor: pointer;">Delete</button>'
                        . '</form>'
                        . '</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('salon_name', 'like', "%{$search}%")
                                ->orWhere('salon_phone', 'like', "%{$search}%")
                                ->orWhere('salon_email', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%")
                                ->orWhere('zipcode', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['store_details', 'location', 'contact', 'staff_count', 'status_badge', 'actions'])
                ->make(true);
        }

        $stats = [
            'total' => Salon::count(),
            'active' => Salon::where('status', 1)->count(),
            'inactive' => Salon::where('status', 0)->count(),
        ];

        return view('tenant.salons.index', compact('stats'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $states = State::where('country_id', 231)->orderBy('name')->get();
        return view('tenant.salons.create', [
            'countries' => $countries,
            'states' => $states,
            'days' => $this->daysOfWeek,
            'daysOfWeek' => $this->daysOfWeek,
        ]);
    }

    public function store(StoreSalonRequest $request)
    {
        $data = $request->validated();
        $salon = Salon::create($data);

        if ($request->has('timings')) {
            foreach ($request->input('timings') as $day => $timing) {
                if (isset($timing['open_time']) && isset($timing['close_time'])) {
                    SalonTiming::create([
                        'salon_id' => $salon->id,
                        'day_of_week' => $day,
                        'open_time' => $timing['open_time'],
                        'close_time' => $timing['close_time'],
                        'is_closed' => isset($timing['is_closed']) ? 1 : 0,
                    ]);
                }
            }
        }

        return redirect()->route('tenant.salons.index')->with('success', 'Store branch created successfully.');
    }

    public function show(Salon $salon)
    {
        $salon->load(['country', 'stateRel', 'timings', 'employees.designations']);
        return view('tenant.salons.show', compact('salon'));
    }

    public function edit(Salon $salon)
    {
        $salon->load('timings');
        $existingTimings = $salon->timings->keyBy('day_of_week');
        $countries = Country::orderBy('name')->get();
        $states = State::where('country_id', $salon->country_id ?? 231)->orderBy('name')->get();
        return view('tenant.salons.edit', [
            'salon' => $salon,
            'existingTimings' => $existingTimings,
            'countries' => $countries,
            'states' => $states,
            'days' => $this->daysOfWeek,
            'daysOfWeek' => $this->daysOfWeek,
        ]);
    }

    public function update(UpdateSalonRequest $request, Salon $salon)
    {
        $data = $request->validated();
        $salon->update($data);

        if ($request->has('timings')) {
            SalonTiming::where('salon_id', $salon->id)->delete();
            foreach ($request->input('timings') as $day => $timing) {
                SalonTiming::create([
                    'salon_id' => $salon->id,
                    'day_of_week' => $day,
                    'open_time' => $timing['open_time'] ?? '09:00:00',
                    'close_time' => $timing['close_time'] ?? '18:00:00',
                    'is_closed' => isset($timing['is_closed']) ? 1 : 0,
                ]);
            }
        }

        return redirect()->route('tenant.salons.index')->with('success', 'Store branch updated successfully.');
    }

    public function destroy(Salon $salon)
    {
        $salon->delete();
        return redirect()->route('tenant.salons.index')->with('success', 'Store branch deleted successfully.');
    }

    public function getStates(Country $country)
    {
        $states = State::where('country_id', $country->id)->orderBy('name')->get(['id', 'name']);
        return response()->json($states);
    }
}
