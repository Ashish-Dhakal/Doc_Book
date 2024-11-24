<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Notifications\VerifyEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

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
        // dd($request->toArray());
        $request->validate([

            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        // $role = 'patient';

        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->notify(new VerifyEmail($user));

        Patient::create([
            'user_id' => $user->id,
        ]);

        event(new Registered($user));

        // Auth::login($user);

        return redirect(route('dashboard', absolute: true));
    }

    public function emailVerify($id)
    {
        $date = Carbon::now();
        $user = User::findOrFail($id);

        if ($user->email_verified_at == null) {
            $user->update([
                'email_verified_at' => $date,
            ]);
            return redirect()->route('login')->with('success', 'Your email is verified and activated successfully.');
        } else {
            return redirect()->route('login')->with('error', 'Your email is already active.');
        }
    }
}
