<?php

namespace App\Providers;

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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
        // Memberitahu Laravel bahwa route parameter {employee} menggunakan model ini
        Route::bind('employee', function ($value) {
            return Employee::findOrFail($value);
        });
    }
}