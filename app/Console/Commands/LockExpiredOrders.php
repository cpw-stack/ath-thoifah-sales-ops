<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class LockExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:lock-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengunci order yang sudah lewat dari hari ini agar tidak bisa diedit langsung oleh salesman';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cari order yang masih bisa diedit, tapi tanggal dibuatnya sudah lewat dari hari ini
        $expiredOrders = Order::where('is_editable', true)
            ->whereDate('created_at', '<', today())
            ->get();

        $count = $expiredOrders->count();

        foreach ($expiredOrders as $order) {
            $order->update([
                'is_editable' => false,
                'locked_at' => now()
            ]);
        }

        $this->info("Berhasil mengunci {$count} order yang kedaluwarsa.");
    }
}