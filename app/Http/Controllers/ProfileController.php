<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Employee;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $employee = $user->employee;
        
        $rank = null;
        $currentSales = 0;

        if ($employee) {
            $currentSales = Order::where('employee_id', $employee->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');

            if ($currentSales > 0) {
                // Ambil total sales per employee bulan ini secara langsung dari tabel orders
                $rankings = Order::selectRaw('employee_id, SUM(total_amount) as total_sales')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', '!=', 'cancelled')
                    ->groupBy('employee_id')
                    ->orderByDesc('total_sales')
                    ->pluck('total_sales', 'employee_id');

                // Cari posisi index salesman saat ini
                $position = $rankings->keys()->search($employee->id);
                $rank = $position !== false ? $position + 1 : null;
            }
        }

        $data = [
            'user' => $user,
            'rank' => $rank,
            'currentSales' => $currentSales
        ];

        if ($user->hasRole('salesman')) {
            return view('profile.mobile_edit', $data);
        }

        return view('profile.edit', $data);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Tambahkan update WA & Alamat
        $user->whatsapp = $request->input('whatsapp');
        $user->address = $request->input('address');

        // Upload Foto
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                \Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('user_photos', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}