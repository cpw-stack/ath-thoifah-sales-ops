<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\VisitScheduleRequest;
use App\Models\Order;
use App\Models\Collection;
use App\Models\Task;
use App\Models\CustomerStockDiscount;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer untuk Sidebar Admin
        View::composer('layouts.app', function ($view) {
            if (Auth::check() && !Auth::user()->hasRole('salesman')) {
                
                $pendingSchedules = VisitScheduleRequest::where('status', 'pending')->count();
                $pendingOrders = Order::where('status', 'pending')->count();
                $pendingCollections = Collection::where('status', 'pending')->count();
                $pendingTasks = Task::where('status', 'pending')->count();
                $pendingDiscounts = CustomerStockDiscount::where('is_approved', false)
                    ->where('is_active', true)
                    ->count();

                $view->with(compact(
                    'pendingSchedules', 
                    'pendingOrders', 
                    'pendingCollections', 
                    'pendingTasks', 
                    'pendingDiscounts'
                ));
            }
        });
    }
}