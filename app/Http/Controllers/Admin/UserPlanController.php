<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPlanController extends Controller
{
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan_id' => ['nullable', 'exists:plans,id'],
        ]);

        $planId = $validated['plan_id'] ?? null;

        DB::transaction(function () use ($user, $planId) {
            $activeSubscription = $user->activeSubscription()->first();

            // Cancel any other active subscriptions
            Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->when($activeSubscription, function ($query) use ($activeSubscription) {
                    $query->where('id', '!=', $activeSubscription->id);
                })
                ->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                ]);

            if ($planId) {
                $plan = Plan::findOrFail($planId);

                if ($activeSubscription) {
                    $activeSubscription->update([
                        'plan_id' => $plan->id,
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => null,
                        'canceled_at' => null,
                    ]);
                } else {
                    Subscription::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'status' => 'active',
                        'starts_at' => now(),
                    ]);
                }
            } else {
                if ($activeSubscription) {
                    $activeSubscription->update([
                        'status' => 'canceled',
                        'canceled_at' => now(),
                    ]);
                }
            }
        });

        return back()->with('success', 'Plano atualizado com sucesso.');
    }
}

