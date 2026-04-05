<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifyPinController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($request->user()->homeRoute());
        }

        return view('auth.verify-pin');
    }

    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($user->homeRoute());
        }

        $request->validate([
            'pin' => ['required', 'string', 'size:6'],
        ]);

        $pin = $request->input('pin');

        if (
            $user->verification_pin !== $pin ||
            ! $user->verification_pin_expires_at ||
            $user->verification_pin_expires_at->isPast()
        ) {
            return back()->withErrors(['pin' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.']);
        }

        $user->markEmailAsVerified();

        $user->forceFill([
            'verification_pin'            => null,
            'verification_pin_expires_at' => null,
        ])->save();

        return redirect()->intended($user->homeRoute());
    }

    /**
     * AJAX: check if current user's email is already verified.
     */
    public function check(Request $request)
    {
        return response()->json([
            'verified' => $request->user()->hasVerifiedEmail(),
            'redirect' => $request->user()->hasVerifiedEmail()
                ? $request->user()->homeRoute()
                : null,
        ]);
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($user->homeRoute());
        }

        $user->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
