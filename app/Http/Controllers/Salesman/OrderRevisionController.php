<?php

namespace App\Http\Controllers\Salesman;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderRevisionController extends Controller
{
    /**
     * Menangani pengajuan revisi order oleh salesman
     */
    public function store(Request $request, Order $order)
    {
        // OPSIONAL: Pengamanan tambahan (Sesuaikan dengan business logic Anda)
        // Jika order sudah lunas total dan statusnya 'completed', mungkin tidak boleh direvisi sama sekali.
        // if ($order->payment_status === 'paid' && $order->status === 'completed') {
        //     return back()->with('error', 'Order ini sudah selesai dan lunas, tidak dapat diajukan revisi.');
        // }

        // 1. Validasi input
        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:500', // Max diperpanjang agar salesman bisa menjelaskan detail
        ]);

        // 2. Cek apakah sudah ada pengajuan pending sebelumnya untuk order ini
        $existingPending = OrderRevision::where('order_id', $order->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return back()->with('error', 'Masih ada pengajuan revisi untuk order ini yang menunggu persetujuan Admin.');
        }

        // 3. Buat record revisi baru
        OrderRevision::create([
            'order_id' => $order->id,
            'employee_id' => Auth::user()->employee->id ?? Auth::id(), // Fallback ke user_id jika employee tidak ada
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // 4. Redirect dengan pesan sukses
        return back()->with('success', 'Pengajuan revisi berhasil dikirim. Menunggu persetujuan Admin.');
    }
}