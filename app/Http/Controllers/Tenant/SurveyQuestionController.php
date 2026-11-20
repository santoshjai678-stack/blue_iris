<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\SurveyQuestion;
use App\Models\Tenant\SurveyQuesOption;
use App\Models\Tenant\SurveyQuesSlotTiming;
use App\Models\Tenant\TaskCategory;
use App\Models\Tenant\Salon;
use App\Models\Tenant\Designation;
use App\Models\Tenant\SurveyPeriod;
use App\Models\Tenant\SurveySlotTime;
use App\Http\Requests\Tenant\StoreTaskRequest;
use App\Http\Requests\Tenant\UpdateTaskRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SurveyQuestionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = SurveyQuestion::with(['category', 'salon', 'designation', 'period', 'options', 'slots'])
                ->select('survey_questions.*');

            if ($request->filled('category_id')) {
                $query->where('task_categorys_id', $request->get('category_id'));
            }

            if ($request->filled('period_id')) {
                $query->where('survey_periods_id', $request->get('period_id'));
            }

            if ($request->filled('status') && $request->get('status') !== '') {
                $query->where('status', (int)$request->get('status'));
            }

            return DataTables::of($query)
                ->addColumn('task_id', function ($task) {
                    return '<code>#' . $task->id . '</code>';
                })
                ->addColumn('question_text', function ($task) {
                    $subtitle = $task->sub_title ? '<div style="font-size: 12px; color: var(--text-muted);">' . e($task->sub_title) . '</div>' : '';
                    $slotsInfo = '';
                    if ($task->slots->isNotEmpty()) {
                        $slotsInfo = '<div style="margin-top: 4px; display: flex; gap: 4px; flex-wrap: wrap;">';
                        foreach ($task->slots as $slot) {
                            $slotsInfo .= '<span class="badge" style="background:#e0f2fe; color:#0284c7; font-size:10px;">⏰ ' . e($slot->slot_name) . '</span>';
                        }
                        $slotsInfo .= '</div>';
                    }
                    return '<div style="font-weight: 600; color: var(--text-main);">' . e($task->question) . '</div>' . $subtitle . $slotsInfo;
                })
                ->addColumn('category_badge', function ($task) {
                    $catName = $task->category->task_category_name ?? 'Uncategorized';
                    return '<span class="badge" style="background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; font-weight: 600;">🏷️ ' . e($catName) . '</span>';
                })
                ->addColumn('period_badge', function ($task) {
                    $periodName = $task->period->period ?? 'Daily';
                    $bg = match(strtolower($periodName)) {
                        'weekly' => '#fef3c7',
                        'monthly' => '#dcfce7',
                        default => '#e0e7ff',
                    };
                    $color = match(strtolower($periodName)) {
                        'weekly' => '#92400e',
                        'monthly' => '#166534',
                        default => '#3730a3',
                    };
                    return '<span class="badge" style="background: ' . $bg . '; color: ' . $color . '; font-weight: 600;">🗓️ ' . e($periodName) . '</span>';
                })
                ->addColumn('control_type_badge', function ($task) {
                    $label = match((string)$task->control_type) {
                        '1', 'radio', 'Radio' => '🔘 Radio',
                        '2', 'checkbox', 'Checkbox' => '☑️ Checkbox',
                        '3', 'text', 'Text' => '✍️ Text',
                        default => ucfirst((string)$task->control_type),
                    };
                    return '<span class="badge" style="background: #f1f5f9; color: #334155; font-weight: 600;">' . e($label) . '</span>';
                })
                ->addColumn('status_badge', function ($task) {
                    return $task->status == 1
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($task) {
                    $editUrl = route('tenant.tasks.edit', $task->id);
                    $deleteUrl = route('tenant.tasks.destroy', $task->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div style="display: flex; gap: 6px; justify-content: flex-end;">'
                        . '<a href="' . $editUrl . '" class="btn-action" title="Edit Task" style="color: #0284c7; background: #e0f2fe; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Edit</a>'
                        . '<form action="' . $deleteUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this task question?\');">'
                        . $csrf . $method
                        . '<button type="submit" class="btn-action" title="Delete" style="color: #ef4444; background: #fee2e2; padding: 6px 10px; border-radius: 6px; border: none; font-size: 12px; font-weight: 600; cursor: pointer;">Delete</button>'
                        . '</form>'
                        . '</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('question', 'like', "%{$search}%")
                                ->orWhere('sub_title', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['task_id', 'question_text', 'category_badge', 'period_badge', 'control_type_badge', 'status_badge', 'actions'])
                ->make(true);
        }

        $categories = TaskCategory::where('status', 1)->orderBy('order_by_id')->get();
        $periods = SurveyPeriod::where('status', 1)->get();
        $stats = [
            'total' => SurveyQuestion::count(),
            'active' => SurveyQuestion::where('status', 1)->count(),
            'inactive' => SurveyQuestion::where('status', 0)->count(),
        ];

        return view('tenant.tasks.index', compact('categories', 'periods', 'stats'));
    }

    public function create()
    {
        $categories = TaskCategory::where('status', 1)->orderBy('order_by_id')->get();
        $salons = Salon::where('status', 1)->get();
        $designations = Designation::where('status', 1)->get();
        $periods = SurveyPeriod::where('status', 1)->get();
        $slotTimes = SurveySlotTime::where('status', 1)->get();

        return view('tenant.tasks.create', compact('categories', 'salons', 'designations', 'periods', 'slotTimes'));
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $task = SurveyQuestion::create($data);

        // Sync Slot Timings (handles slot_times[] and slot_ids[])
        $slotIds = $request->input('slot_times', $request->input('slot_ids', []));
        if (!empty($slotIds)) {
            foreach ($slotIds as $slotId) {
                SurveyQuesSlotTiming::create([
                    'survey_questions_id' => $task->id,
                    'survey_slot_times_id' => $slotId,
                ]);
            }
        }

        // Add Options if Radio (1) or Checkbox (2)
        $isChoiceType = in_array((string)$task->control_type, ['1', '2', 'Radio', 'Checkbox', 'radio', 'checkbox']);
        if ($isChoiceType && $request->has('options')) {
            $options = $request->input('options', []);
            $hasCustom = false;
            foreach ($options as $optionText) {
                if (!empty(trim((string)$optionText))) {
                    $hasCustom = true;
                    SurveyQuesOption::create([
                        'survey_ques_id' => $task->id,
                        'option' => trim((string)$optionText),
                        'status' => 1,
                        'created_by' => auth()->id() ?? 1,
                    ]);
                }
            }

            // If radio and no custom options provided, add default Yes / No
            if (!$hasCustom && in_array((string)$task->control_type, ['1', 'Radio', 'radio'])) {
                SurveyQuesOption::create([
                    'survey_ques_id' => $task->id,
                    'option' => 'Yes',
                    'status' => 1,
                    'created_by' => auth()->id() ?? 1,
                ]);
                SurveyQuesOption::create([
                    'survey_ques_id' => $task->id,
                    'option' => 'No',
                    'status' => 1,
                    'created_by' => auth()->id() ?? 1,
                ]);
            }
        }

        return redirect()->route('tenant.tasks.index')->with('success', 'Task question created successfully.');
    }

    public function edit(SurveyQuestion $task)
    {
        $task->load(['slots', 'options']);
        $categories = TaskCategory::where('status', 1)->orderBy('order_by_id')->get();
        $salons = Salon::where('status', 1)->get();
        $designations = Designation::where('status', 1)->get();
        $periods = SurveyPeriod::where('status', 1)->get();
        $slotTimes = SurveySlotTime::where('status', 1)->get();
        $selectedSlots = $task->slots->pluck('id')->toArray();

        return view('tenant.tasks.edit', compact('task', 'categories', 'salons', 'designations', 'periods', 'slotTimes', 'selectedSlots'));
    }

    public function update(UpdateTaskRequest $request, SurveyQuestion $task)
    {
        $data = $request->validated();
        $task->update($data);

        // Sync Slots
        SurveyQuesSlotTiming::where('survey_questions_id', $task->id)->delete();
        $slotIds = $request->input('slot_times', $request->input('slot_ids', []));
        if (!empty($slotIds)) {
            foreach ($slotIds as $slotId) {
                SurveyQuesSlotTiming::create([
                    'survey_questions_id' => $task->id,
                    'survey_slot_times_id' => $slotId,
                ]);
            }
        }

        // Sync Options
        $isChoiceType = in_array((string)$task->control_type, ['1', '2', 'Radio', 'Checkbox', 'radio', 'checkbox']);
        if ($isChoiceType && $request->has('options')) {
            SurveyQuesOption::where('survey_ques_id', $task->id)->delete();
            $options = $request->input('options', []);
            $hasCustom = false;
            foreach ($options as $optionText) {
                if (!empty(trim((string)$optionText))) {
                    $hasCustom = true;
                    SurveyQuesOption::create([
                        'survey_ques_id' => $task->id,
                        'option' => trim((string)$optionText),
                        'status' => 1,
                        'created_by' => auth()->id() ?? 1,
                    ]);
                }
            }

            // If radio and no custom options provided, add default Yes / No
            if (!$hasCustom && in_array((string)$task->control_type, ['1', 'Radio', 'radio'])) {
                SurveyQuesOption::create([
                    'survey_ques_id' => $task->id,
                    'option' => 'Yes',
                    'status' => 1,
                    'created_by' => auth()->id() ?? 1,
                ]);
                SurveyQuesOption::create([
                    'survey_ques_id' => $task->id,
                    'option' => 'No',
                    'status' => 1,
                    'created_by' => auth()->id() ?? 1,
                ]);
            }
        }

        return redirect()->route('tenant.tasks.index')->with('success', 'Task question updated successfully.');
    }

    public function destroy(SurveyQuestion $task)
    {
        $task->slots()->detach();
        $task->options()->delete();
        $task->delete();
        return redirect()->route('tenant.tasks.index')->with('success', 'Task question deleted successfully.');
    }
}
