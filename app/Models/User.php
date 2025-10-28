<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'is_admin',
        'level',
        'company_name',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest();
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Count books uploaded in the last 30 days (rolling month)
     */
    public function booksUploadedThisMonth(): int
    {
        return $this->books()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }

    /**
     * Validate if user can upload a book with given page count
     *
     * @param int $pageCount Number of pages to translate
     * @return array ['allowed' => bool, 'message' => string|null, 'remaining_books' => int|null]
     */
    public function validatePlanLimits(int $pageCount): array
    {
        if ($this->is_admin) {
            return ['allowed' => true, 'message' => null, 'remaining_books' => null];
        }

        $subscription = $this->activeSubscription;

        if (!$subscription || !$subscription->isActive()) {
            return [
                'allowed' => false,
                'message' => __('messages.plan_required'),
                'remaining_books' => 0,
            ];
        }

        $plan = $subscription->plan;

        if (!$plan) {
            return [
                'allowed' => false,
                'message' => __('messages.plan_required'),
                'remaining_books' => 0,
            ];
        }

        // Check monthly book limit (rolling 30 days)
        if ($plan->max_books_per_month > 0) {
            $booksThisMonth = $this->booksUploadedThisMonth();

            if ($booksThisMonth >= $plan->max_books_per_month) {
                return [
                    'allowed' => false,
                    'message' => __('messages.plan_book_limit', [
                        'max' => number_format($plan->max_books_per_month, 0, ',', '.'),
                        'current' => $booksThisMonth,
                    ]),
                    'remaining_books' => 0,
                ];
            }
        }

        // Check pages per book limit
        // If unlimited (0), allow any page count
        if (!$plan->isUnlimited() && $plan->max_pages > 0 && $pageCount > $plan->max_pages) {
            $nextPage = $plan->max_pages + 1;
            return [
                'allowed' => false,
                'message' => __('messages.plan_page_limit', [
                    'max' => number_format($plan->max_pages, 0, ',', '.'),
                    'requested' => number_format($pageCount, 0, ',', '.'),
                    'next' => number_format($nextPage, 0, ',', '.'),
                ]),
                'remaining_books' => $plan->max_books_per_month - $this->booksUploadedThisMonth(),
            ];
        }

        $remainingBooks = $plan->max_books_per_month > 0
            ? $plan->max_books_per_month - $this->booksUploadedThisMonth()
            : null;

        return [
            'allowed' => true,
            'message' => null,
            'remaining_books' => $remainingBooks,
        ];
    }

    public function canUploadBook(int $pageCount): bool
    {
        return $this->validatePlanLimits($pageCount)['allowed'];
    }
}
