<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tenant\Salon;
use App\Models\Tenant\SalonSurveyAnswer;
use App\Models\Tenant\SurveyPeriod;
use App\Models\Tenant\SurveyQuestion;
use App\Models\Tenant\SurveySlotTime;
use App\Models\Tenant\TaskCategory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TaskController extends BaseApiController
{
    /**
     * Daily Tasks & Checklist (with shift slots, categories, and today's answers)
     */
    public function dailyTasks(Request $request): JsonResponse
    {
        $storeId = $request->input('store_id') ?? $request->input('salon_id');
        $date = $request->input('date', now()->toDateString());

        // Get Daily period ID
        $dailyPeriod = SurveyPeriod::where('period', 'like', '%Daily%')->first();
        $periodId = $dailyPeriod ? $dailyPeriod->id : 1;

        // Fetch all active daily questions
        $questionsQuery = SurveyQuestion::with(['category', 'slots', 'options'])
            ->where('survey_periods_id', $periodId)
            ->where('status', 1);

        if (!empty($storeId)) {
            $questionsQuery->where(function ($q) use ($storeId) {
                $q->whereNull('salon_id')->orWhere('salon_id', $storeId);
            });
        }

        $questions = $questionsQuery->get();

        // Fetch existing answers for this store and date
        $existingAnswers = collect();
        if (!empty($storeId)) {
            $existingAnswers = SalonSurveyAnswer::where('salon_id', $storeId)
                ->where('ans_date', $date)
                ->get()
                ->keyBy(fn($item) => $item->survey_questions_id . '_' . ($item->slot_times_id ?? 0));
        }

        // Get all active shift slot times
        $slotTimes = SurveySlotTime::where('status', 1)->get()->map(function ($slot) {
            $nowTime = Carbon::now()->format('H:i:s');
            $isActiveNow = ($nowTime >= $slot->slot_start_time && $nowTime <= $slot->slot_end_time);

            return [
                'slot_id' => $slot->id,
                'slot_name' => $slot->slot_name,
                'start_time' => Carbon::parse($slot->slot_start_time)->format('h:i A'),
                'end_time' => Carbon::parse($slot->slot_end_time)->format('h:i A'),
                'is_active_now' => $isActiveNow,
            ];
        });

        // Group questions by Slot and Category
        $categorizedSlots = [];
        foreach ($slotTimes as $slot) {
            $slotQuestions = $questions->filter(function ($q) use ($slot) {
                return $q->slots->isEmpty() || $q->slots->contains('id', $slot['slot_id']);
            });

            $categoriesMap = [];
            foreach ($slotQuestions as $q) {
                $catName = $q->category->task_category_name ?? 'General';
                $catId = $q->task_categorys_id ?? 0;

                $ansKey = $q->id . '_' . $slot['slot_id'];
                $answer = $existingAnswers->get($ansKey);

                $item = [
                    'question_id' => $q->id,
                    'question' => $q->question,
                    'sub_title' => $q->sub_title,
                    'control_type' => $q->control_type ?? 'Radio',
                    'is_mandatory' => (bool)$q->is_question_mandatory,
                    'options' => $q->options->pluck('options_text'),
                    'answered' => $answer !== null,
                    'answer_data' => $answer ? [
                        'ans_text' => $answer->ans_text,
                        'submitted_at' => $answer->created_at?->format('Y-m-d H:i:s'),
                    ] : null,
                ];

                if (!isset($categoriesMap[$catId])) {
                    $categoriesMap[$catId] = [
                        'category_id' => $catId,
                        'category_name' => $catName,
                        'questions' => [],
                    ];
                }
                $categoriesMap[$catId]['questions'][] = $item;
            }

            $categorizedSlots[] = [
                'slot_id' => $slot['slot_id'],
                'slot_name' => $slot['slot_name'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'is_active_now' => $slot['is_active_now'],
                'categories' => array_values($categoriesMap),
            ];
        }

        return $this->sendResponse([
            'date' => $date,
            'store_id' => $storeId ? (int)$storeId : null,
            'slots' => $categorizedSlots,
        ], 'Daily checklist tasks fetched.');
    }

    /**
     * Weekly Tasks Checklist
     */
    public function weeklyTasks(Request $request): JsonResponse
    {
        $storeId = $request->input('store_id') ?? $request->input('salon_id');
        $weeklyPeriod = SurveyPeriod::where('period', 'like', '%Weekly%')->first();
        $periodId = $weeklyPeriod ? $weeklyPeriod->id : 2;

        $questions = SurveyQuestion::with(['category', 'options'])
            ->where('survey_periods_id', $periodId)
            ->where('status', 1)
            ->get();

        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();

        $existingAnswers = collect();
        if (!empty($storeId)) {
            $existingAnswers = SalonSurveyAnswer::where('salon_id', $storeId)
                ->whereBetween('ans_date', [$startOfWeek, $endOfWeek])
                ->get()
                ->keyBy('survey_questions_id');
        }

        $items = $questions->map(function ($q) use ($existingAnswers) {
            $answer = $existingAnswers->get($q->id);
            return [
                'question_id' => $q->id,
                'question' => $q->question,
                'category_name' => $q->category->task_category_name ?? 'General',
                'control_type' => $q->control_type ?? 'Radio',
                'options' => $q->options->pluck('options_text'),
                'answered' => $answer !== null,
                'answer_data' => $answer ? [
                    'ans_text' => $answer->ans_text,
                    'ans_date' => $answer->ans_date,
                ] : null,
            ];
        });

        return $this->sendResponse([
            'week_range' => "{$startOfWeek} to {$endOfWeek}",
            'store_id' => $storeId ? (int)$storeId : null,
            'tasks' => $items,
        ], 'Weekly tasks fetched.');
    }

    /**
     * Monthly Tasks Checklist
     */
    public function monthlyTasks(Request $request): JsonResponse
    {
        $storeId = $request->input('store_id') ?? $request->input('salon_id');
        $monthlyPeriod = SurveyPeriod::where('period', 'like', '%Monthly%')->first();
        $periodId = $monthlyPeriod ? $monthlyPeriod->id : 3;

        $questions = SurveyQuestion::with(['category', 'options'])
            ->where('survey_periods_id', $periodId)
            ->where('status', 1)
            ->get();

        $existingAnswers = collect();
        if (!empty($storeId)) {
            $existingAnswers = SalonSurveyAnswer::where('salon_id', $storeId)
                ->whereMonth('ans_date', now()->month)
                ->whereYear('ans_date', now()->year)
                ->get()
                ->keyBy('survey_questions_id');
        }

        $items = $questions->map(function ($q) use ($existingAnswers) {
            $answer = $existingAnswers->get($q->id);
            return [
                'question_id' => $q->id,
                'question' => $q->question,
                'category_name' => $q->category->task_category_name ?? 'General',
                'control_type' => $q->control_type ?? 'Radio',
                'options' => $q->options->pluck('options_text'),
                'answered' => $answer !== null,
                'answer_data' => $answer ? [
                    'ans_text' => $answer->ans_text,
                    'ans_date' => $answer->ans_date,
                ] : null,
            ];
        });

        return $this->sendResponse([
            'month' => now()->format('F Y'),
            'store_id' => $storeId ? (int)$storeId : null,
            'tasks' => $items,
        ], 'Monthly tasks fetched.');
    }

    /**
     * Submit Survey Answers (Single or Batch)
     */
    public function submitAnswers(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $storeId = $request->input('store_id') ?? $request->input('salon_id');
        $date = $request->input('date', now()->toDateString());

        if (empty($storeId)) {
            return $this->sendError('Store ID is required.', [], 422);
        }

        // Check if batch answers array or single answer
        $answers = $request->input('answers');
        if (!is_array($answers)) {
            // Single submission payload
            $answers = [[
                'question_id' => $request->input('question_id') ?? $request->input('survey_questions_id'),
                'period_id' => $request->input('period_id') ?? $request->input('survey_periods_id') ?? 1,
                'slot_id' => $request->input('slot_id') ?? $request->input('slot_times_id'),
                'ans_text' => $request->input('ans_text') ?? $request->input('answer'),
                'option_id' => $request->input('option_id') ?? $request->input('survey_ques_options_id'),
                'location' => $request->input('location') ?? $request->input('answer_location'),
            ]];
        }

        if (empty($answers)) {
            return $this->sendError('No answer items found in request payload.', [], 422);
        }

        DB::beginTransaction();
        try {
            $insertedCount = 0;
            $uniqueId = Str::uuid()->toString();

            foreach ($answers as $ans) {
                if (empty($ans['question_id'])) continue;

                $qId = (int)$ans['question_id'];
                $periodId = (int)($ans['period_id'] ?? 1);
                $slotId = !empty($ans['slot_id']) ? (int)$ans['slot_id'] : null;
                $ansText = is_array($ans['ans_text'] ?? null) ? implode(', ', $ans['ans_text']) : ($ans['ans_text'] ?? 'Yes');
                $location = $ans['location'] ?? null;

                // Update or create answer
                SalonSurveyAnswer::updateOrCreate(
                    [
                        'salon_id' => $storeId,
                        'survey_questions_id' => $qId,
                        'slot_times_id' => $slotId,
                        'ans_date' => $date,
                    ],
                    [
                        'ans_unique_id' => $uniqueId,
                        'user_id' => $userId,
                        'survey_periods_id' => $periodId,
                        'ans_text' => $ansText,
                        'answer_location' => $location,
                    ]
                );
                $insertedCount++;
            }

            DB::commit();

            return $this->sendResponse([
                'submitted_count' => $insertedCount,
                'submission_id' => $uniqueId,
                'date' => $date,
                'store_id' => (int)$storeId,
            ], "Successfully recorded {$insertedCount} checklist responses.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error recording survey responses: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Submission History & Audit Log
     */
    public function history(Request $request): JsonResponse
    {
        $storeId = $request->input('store_id');
        $startDate = $request->input('start_date', now()->subDays(7)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $query = SalonSurveyAnswer::with(['question.category', 'slotTime', 'user'])
            ->whereBetween('ans_date', [$startDate, $endDate])
            ->latest('id');

        if (!empty($storeId)) {
            $query->where('salon_id', $storeId);
        }

        $records = $query->paginate(30);

        return $this->sendResponse([
            'items' => $records->items(),
            'current_page' => $records->currentPage(),
            'total' => $records->total(),
            'per_page' => $records->perPage(),
        ], 'Submission history fetched.');
    }
}