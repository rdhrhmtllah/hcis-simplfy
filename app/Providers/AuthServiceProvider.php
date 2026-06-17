<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('akses-svp', fn (User $user) => true);
        Gate::define('akses-spesial', fn (User $user, string $page) => true);
        Gate::define('open-kpi', fn (User $user) => true);
    }
}
