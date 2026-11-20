<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Salon;
use App\Models\Tenant\SalonSurveyAnswer;
use App\Models\Tenant\SurveyPeriod;
use App\Models\Tenant\SurveyQuestion;
use App\Models\Tenant\SurveySlotTime;
use App\Models\Tenant\TaskCategory;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    /**
     * Reports Executive Dashboard & Key Analytics
     */
    public function index(Request $request): View
    {
        $salonsCount = Salon::where('status', 1)->count();
        $tasksCount = SurveyQuestion::where('status', 1)->count();
        $totalAnswersThisMonth = SalonSurveyAnswer::whereMonth('ans_date', now()->month)
            ->whereYear('ans_date', now()->year)
            ->count();

        // 7-day compliance trend
        $recentDays = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $answersCount = SalonSurveyAnswer::whereDate('ans_date', $date)->count();
            $recentDays->push([
                'date' => Carbon::parse($date)->format('M d'),
                'day' => Carbon::parse($date)->format('D'),
                'answers' => $answersCount,
            ]);
        }

        // Store-wise performance ranking
        $salons = Salon::where('status', 1)->get()->map(function ($salon) use ($tasksCount) {
            $answersThisWeek = SalonSurveyAnswer::where('salon_id', $salon->id)
                ->whereBetween('ans_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                ->count();

            $expectedPerWeek = max(1, $tasksCount * 7);
            $complianceRate = min(100, round(($answersThisWeek / $expectedPerWeek) * 100));

            return [
                'id' => $salon->id,
                'name' => $salon->salon_name,
                'city' => $salon->city ?? 'Location',
                'answers_week' => $answersThisWeek,
                'compliance_rate' => $complianceRate,
            ];
        })->sortByDesc('compliance_rate')->values();

        // Category breakdown
        $categories = TaskCategory::withCount(['questions' => function($q) {
            $q->where('status', 1);
        }])->get();

        return view('tenant.reports.index', compact(
            'salonsCount',
            'tasksCount',
            'totalAnswersThisMonth',
            'recentDays',
            'salons',
            'categories'
        ));
    }

    /**
     * Task Compliance Matrix Grid
     */
    public function taskCompliance(Request $request): View
    {
        $periods = SurveyPeriod::all();
        $periodType = $request->has('period_type') ? $request->query('period_type') : $request->query('period', '');
        
        $salonId = $request->query('salon_id');
        $categoryId = $request->query('category_id');

        $startDateStr = $request->query('start_date', now()->subDays(6)->toDateString());
        $endDateStr = $request->query('end_date', now()->toDateString());

        $period = CarbonPeriod::create($startDateStr, $endDateStr);
        $dateHeaders = [];
        foreach ($period as $dt) {
            $dateHeaders[] = [
                'date_str' => $dt->toDateString(),
                'formatted' => $dt->format('M d'),
                'day' => $dt->format('D'),
            ];
        }

        $allSalons = Salon::where('status', 1)->orderBy('salon_name')->get();
        $categories = TaskCategory::where('status', 1)->orderBy('order_by_id')->get();

        // Scope questions
        $questionsQuery = SurveyQuestion::where('status', 1);
        if (!empty($periodType)) {
            $questionsQuery->where('survey_periods_id', $periodType);
        }
        if (!empty($categoryId)) {
            $questionsQuery->where('task_categorys_id', $categoryId);
        }

        $totalQuestions = $questionsQuery->count();
        $questionIds = $questionsQuery->pluck('id');

        // Target Salons
        $salonsQuery = Salon::where('status', 1);
        if (!empty($salonId)) {
            $salonsQuery->where('id', $salonId);
        }
        $targetSalons = $salonsQuery->orderBy('salon_name')->get();

        // Answers aggregation
        $answers = SalonSurveyAnswer::whereIn('survey_questions_id', $questionIds)
            ->whereBetween('ans_date', [$startDateStr, $endDateStr])
            ->when(!empty($salonId), fn($q) => $q->where('salon_id', $salonId))
            ->select('salon_id', 'ans_date', DB::raw('count(distinct survey_questions_id) as answered_count'))
            ->groupBy('salon_id', 'ans_date')
            ->get()
            ->keyBy(fn($r) => $r->salon_id . '_' . $r->ans_date);

        // Build matrix
        $matrix = [];
        $totalDays = count($dateHeaders);
        $totalExpectedPerSalon = $totalQuestions * $totalDays;

        foreach ($targetSalons as $salon) {
            $rowColumns = [];
            $totalAnswered = 0;

            foreach ($dateHeaders as $header) {
                $dateKey = $salon->id . '_' . $header['date_str'];
                $answered = isset($answers[$dateKey]) ? (int)$answers[$dateKey]->answered_count : 0;
                $totalAnswered += $answered;
                $pct = $totalQuestions > 0 ? min(100, round(($answered / $totalQuestions) * 100)) : 0;

                $rowColumns[$header['date_str']] = [
                    'answered' => $answered,
                    'percentage' => $pct,
                ];
            }

            $overallPct = $totalExpectedPerSalon > 0 ? min(100, round(($totalAnswered / $totalExpectedPerSalon) * 100)) : 0;

            $matrix[] = [
                'salon_id' => $salon->id,
                'salon_name' => $salon->salon_name,
                'city' => $salon->city ?? '',
                'columns' => $rowColumns,
                'total_answered' => $totalAnswered,
                'total_expected' => $totalExpectedPerSalon,
                'overall_percentage' => $overallPct,
            ];
        }

        return view('tenant.reports.task_compliance', compact(
            'periods',
            'periodType',
            'allSalons',
            'salonId',
            'categories',
            'categoryId',
            'startDateStr',
            'endDateStr',
            'totalQuestions',
            'dateHeaders',
            'matrix'
        ));
    }

    /**
     * Survey Answers Audit & Detailed Response Log with Yajra DataTables Server-Side
     */
    public function surveyResponses(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = SalonSurveyAnswer::with(['salon', 'user', 'question.category', 'slotTime', 'option'])
                ->select('salon_survey_answers.*');

            if ($request->filled('salon_id')) {
                $query->where('salon_survey_answers.salon_id', $request->get('salon_id'));
            }

            if ($request->filled('category_id')) {
                $catId = $request->get('category_id');
                $query->whereHas('question', function ($q) use ($catId) {
                    $q->where('task_categorys_id', $catId);
                });
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('salon_survey_answers.ans_date', [$request->get('start_date'), $request->get('end_date')]);
            }

            return DataTables::of($query)
                ->addColumn('date_slot', function ($ans) {
                    $date = Carbon::parse($ans->ans_date)->format('M d, Y');
                    $time = $ans->created_at ? '<div style="font-size: 11px; color: var(--text-muted);">' . $ans->created_at->format('h:i A') . '</div>' : '';
                    $slot = $ans->slotTime ? '<span class="badge" style="background:#e0f2fe; color:#0284c7; font-size:10px; margin-top:2px;">⏰ ' . e($ans->slotTime->slot_name) . '</span>' : '';
                    return '<div><strong>' . $date . '</strong>' . $time . $slot . '</div>';
                })
                ->addColumn('salon_name', function ($ans) {
                    return '<div style="font-weight: 600; color: var(--text-main);">🏪 ' . e($ans->salon->salon_name ?? 'Store #' . $ans->salon_id) . '</div>'
                        . '<div style="font-size: 11px; color: var(--text-muted);">' . e($ans->salon->city ?? '') . '</div>';
                })
                ->addColumn('auditor', function ($ans) {
                    if ($ans->user) {
                        return '<div style="font-weight: 500;">👤 ' . e($ans->user->first_name . ' ' . $ans->user->last_name) . '</div>';
                    }
                    return '<span style="color: var(--text-muted); font-size: 12px;">Store Staff</span>';
                })
                ->addColumn('category_badge', function ($ans) {
                    $catName = $ans->question->category->task_category_name ?? 'General';
                    return '<span class="badge" style="background: #ede9fe; color: #6d28d9; font-weight: 600;">🏷️ ' . e($catName) . '</span>';
                })
                ->addColumn('question_text', function ($ans) {
                    return '<div style="font-size: 13px; font-weight: 500;">' . e($ans->question->question ?? 'Survey Question #' . $ans->survey_questions_id) . '</div>';
                })
                ->addColumn('answer_badge', function ($ans) {
                    $val = strtolower(trim($ans->ans_text ?? ''));
                    if (in_array($val, ['yes', '1', 'done', 'completed', 'pass', 'ok'])) {
                        return '<span class="badge badge-success">✓ ' . e($ans->ans_text ?? 'Yes') . '</span>';
                    } elseif (in_array($val, ['no', '0', 'fail', 'missed', 'incomplete'])) {
                        return '<span class="badge badge-danger">✗ ' . e($ans->ans_text ?? 'No') . '</span>';
                    } else {
                        return '<span class="badge" style="background: #f1f5f9; color: #334155; font-weight: 500;">' . e($ans->ans_text ?? '-') . '</span>';
                    }
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('ans_text', 'like', "%{$search}%")
                                ->orWhereHas('question', fn($sq) => $sq->where('question', 'like', "%{$search}%"))
                                ->orWhereHas('salon', fn($sal) => $sal->where('salon_name', 'like', "%{$search}%"))
                                ->orWhereHas('user', fn($uq) => $uq->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
                        });
                    }
                })
                ->rawColumns(['date_slot', 'salon_name', 'auditor', 'category_badge', 'question_text', 'answer_badge'])
                ->make(true);
        }

        $salons = Salon::where('status', 1)->orderBy('salon_name')->get();
        $categories = TaskCategory::where('status', 1)->orderBy('order_by_id')->get();
        $startDate = $request->query('start_date', now()->subDays(14)->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        return view('tenant.reports.survey_responses', compact('salons', 'categories', 'startDate', 'endDate'));
    }

    /**
     * Export compliance report as CSV stream
     */
    public function exportCompliance(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $salonId = $request->query('salon_id');

        $fileName = "compliance_report_{$startDate}_to_{$endDate}.csv";

        return response()->streamDownload(function () use ($startDate, $endDate, $salonId) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Submission Date', 'Store Name', 'City', 'Task Category', 'Survey Question', 'Response', 'Submitted By']);

            $query = SalonSurveyAnswer::with(['salon', 'user', 'question.category'])
                ->whereBetween('ans_date', [$startDate, $endDate]);

            if (!empty($salonId)) {
                $query->where('salon_id', $salonId);
            }

            $query->chunk(200, function ($answers) use ($handle) {
                foreach ($answers as $ans) {
                    fputcsv($handle, [
                        $ans->ans_date,
                        $ans->salon->salon_name ?? 'N/A',
                        $ans->salon->city ?? 'N/A',
                        $ans->question->category->task_category_name ?? 'N/A',
                        $ans->question->question ?? 'N/A',
                        $ans->ans_text,
                        $ans->user ? $ans->user->first_name . ' ' . $ans->user->last_name : 'Staff',
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
