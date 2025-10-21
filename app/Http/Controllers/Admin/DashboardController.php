<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'total_books' => Book::count(),
            'total_pages_translated' => Book::sum('total_pages'),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_revenue' => Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->where('subscriptions.status', 'active')
                ->sum('plans.price'),
        ];

        $recent_users = User::latest()->take(5)->get();
        $recent_books = Book::with('user')->latest()->take(10)->get();

        $subscriptions_by_plan = Subscription::select('plan_id', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('plan_id')
            ->with('plan')
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_books', 'subscriptions_by_plan'));
    }
}
