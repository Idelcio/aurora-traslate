<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBooks = Book::count();
        $totalPages = Book::sum('total_pages') ?? 0;

        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $totalRevenue = Subscription::where('status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price');

        $totalRevenue = (float) $totalRevenue;

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $usersSummary = User::select('id', 'name', 'email')
            ->with(['activeSubscription.plan'])
            ->withCount('books')
            ->withSum('books as books_pages_sum', 'total_pages')
            ->withCount(['books as books_this_month' => function ($query) use ($currentMonthStart, $currentMonthEnd) {
                $query->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $user->books_pages_sum = (int) ($user->books_pages_sum ?? 0);
                $user->books_this_month = (int) ($user->books_this_month ?? 0);
                return $user;
            });

        $plans = Plan::orderBy('price')->get();

        $activePlanCounts = Subscription::select('plan_id', DB::raw('COUNT(*) as total'))
            ->where('status', 'active')
            ->groupBy('plan_id')
            ->pluck('total', 'plan_id');

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalBooks' => $totalBooks,
            'totalPages' => (int) $totalPages,
            'activeSubscriptions' => $activeSubscriptions,
            'totalRevenue' => $totalRevenue,
            'usersSummary' => $usersSummary,
            'plans' => $plans,
            'activePlanCounts' => $activePlanCounts,
        ]);
    }
}
