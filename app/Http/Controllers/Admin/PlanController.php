<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('price')->get();

        return view('admin.plans.index', [
            'plans' => $plans,
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'max_pages' => ['required', 'integer', 'min:0'],
            'max_books_per_month' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $plan->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'max_pages' => $data['max_pages'],
            'max_books_per_month' => $data['max_books_per_month'],
            'description' => $data['description'] ?? null,
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Plano atualizado com sucesso.');
    }
}
