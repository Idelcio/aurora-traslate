<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;
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

        $usersSummary = User::select('id', 'name', 'email')
            ->withCount('books')
            ->withSum('books as books_pages_sum', 'total_pages')
            ->orderByDesc('books_count')
            ->get()
            ->map(function ($user) {
                $user->books_pages_sum = (int) ($user->books_pages_sum ?? 0);
                return $user;
            });

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalBooks' => $totalBooks,
            'totalPages' => (int) $totalPages,
            'activeSubscriptions' => $activeSubscriptions,
            'totalRevenue' => $totalRevenue,
            'usersSummary' => $usersSummary,
        ]);
    }
}
