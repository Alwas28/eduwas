<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.verifikasi.index');
    }

    /**
     * AJAX: return paginated user list based on filter.
     */
    public function data(Request $request)
    {
        $filter  = $request->query('filter', 'unverified');
        $search  = $request->query('search', '');
        $perPage = (int) $request->query('per_page', 15);

        $query = User::with('mahasiswa')
            ->when($filter === 'unverified', fn($q) => $q->whereNull('email_verified_at'))
            ->when($filter === 'verified',   fn($q) => $q->whereNotNull('email_verified_at'))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('name',  'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $query->map(function (User $u) {
                return [
                    'id'          => $u->id,
                    'name'        => $u->name,
                    'email'       => $u->email,
                    'nim'         => $u->mahasiswa?->nim,
                    'verified'    => (bool) $u->email_verified_at,
                    'verified_at' => $u->email_verified_at?->format('d M Y, H:i'),
                    'pin'         => $u->verification_pin,
                    'pin_expires' => $u->verification_pin_expires_at
                        ? $u->verification_pin_expires_at->format('H:i:s')
                        : null,
                    'pin_expired' => $u->verification_pin_expires_at
                        ? $u->verification_pin_expires_at->isPast()
                        : true,
                    'registered'  => $u->created_at->format('d M Y, H:i'),
                ];
            }),
            'total'        => $query->total(),
            'current_page' => $query->currentPage(),
            'last_page'    => $query->lastPage(),
        ]);
    }

    /**
     * Admin manually verifies a user.
     */
    public function verify(Request $request, User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Akun sudah terverifikasi.'], 422);
        }

        $user->markEmailAsVerified();
        $user->forceFill([
            'verification_pin'            => null,
            'verification_pin_expires_at' => null,
        ])->save();

        return response()->json(['message' => 'Akun berhasil diverifikasi.']);
    }

    /**
     * Admin resends PIN to a user.
     */
    public function resend(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Akun sudah terverifikasi.'], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'PIN baru telah dikirim ke ' . $user->email]);
    }
}
