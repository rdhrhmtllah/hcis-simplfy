<?php

namespace App\Http\Middleware;

use App\Services\Shell\InertiaShellService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing(['karyawan']);
        }

        $karyawan = $user?->karyawan;
        $shellLayout = app(InertiaShellService::class)->build($request, $user);

        $displayName = (string) ($user?->Username ?? $user?->name ?? 'User');
        $department = 'Learning Sandbox';
        $jobTitle = 'BSC Kategori';

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user
                    ? [
                        'id' => $user->Id_Users,
                        'name' => $displayName,
                        'username' => $user->Username ?? $displayName,
                        'nik' => (string) ($karyawan?->Kode_Karyawan ?? $user->Kode_Users ?? ''),
                        'role' => (string) ($user->Role ?? 'employee'),
                        'department' => (string) $department,
                        'job_title' => (string) $jobTitle,
                        'initials' => $this->buildInitials($displayName),
                    ]
                    : null,
            ],
            'layout' => $shellLayout,
        ]);
    }

    private function buildInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr((string) $part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'US';
    }
}
