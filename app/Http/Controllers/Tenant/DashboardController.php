<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Salon;
use App\Models\Tenant\Employee;
use App\Models\Tenant\SurveyQuestion;
use App\Models\Tenant\SalonSurveyAnswer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display Tenant Dashboard
     */
    public function index()
    {
        $totalSalons = Salon::count();
        $totalEmployees = Employee::count();
        $totalQuestions = SurveyQuestion::count();
        $todaySurveys = SalonSurveyAnswer::whereDate('ans_date', today())->count();

        $recentSalons = Salon::latest()->take(5)->get();
        $recentEmployees = Employee::with('activeSchedule.salon')->latest()->take(5)->get();
        $recentTasks = SurveyQuestion::with('category')->latest()->take(5)->get();

        return view('tenant.dashboard', [
            'totalSalons' => $totalSalons,
            'totalEmployees' => $totalEmployees,
            'totalQuestions' => $totalQuestions,
            'todayAttendance' => $todaySurveys,
            'recentSalons' => $recentSalons,
            'recentEmployees' => $recentEmployees,
            'recentTasks' => $recentTasks,
        ]);
    }
}