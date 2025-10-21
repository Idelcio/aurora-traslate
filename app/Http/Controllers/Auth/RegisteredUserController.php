<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Adicionando as validações para company_name, phone e cpf
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'cpf' => ['required', 'string', 'max:14', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Remover formatação do CPF (manter apenas números)
        $cpf = preg_replace('/[^0-9]/', '', $request->cpf);

        // Criando o usuário com os novos campos
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $cpf,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'phone' => $request->phone,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
