<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /**
     * Daftar URI/Path yang DIBEBASKAN dari mode maintenance.
     * Anda bisa menggunakan wildcard (*)
     */
    protected $except = [
        'log-viewer',
        'test-db',
    ];

    public function handle(Request $request, Closure $next)
    {
        // dd($request->path());
        if (config('services.project_config.maintenance_mode') === true) {
            if ($request->is($this->except)) {
                return $next($request);
            }

            abort(503);
        }

        return $next($request);
    }
}
