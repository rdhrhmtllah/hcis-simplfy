<?php

namespace App\Services\Shell;

use App\Models\User;
use Illuminate\Http\Request;

class InertiaShellService
{
    public function build(Request $request, ?User $user): array
    {
        $brand = $this->buildBrand();
        $navigation = $this->buildNavigation($request);
        $shell = $this->buildShellMeta($request, $navigation);

        return [
            'brand' => $brand,
            'navigation' => $navigation,
            'shell' => $shell,
            'unreadNotificationsCount' => 0,
            'notifications' => [],
            'messages' => [],
            'help' => $this->buildHelpItems(),
        ];
    }

    protected function buildBrand(): array
    {
        return [
            'appName' => 'EVO Group Learning Sandbox',
            'appShortName' => 'EVO',
            'mainLogo' => asset('logo/EVOGROUP.png'),
            'mainLogoAlt' => 'EVO Group',
            'subsidiaries' => [
                [
                    'id' => 'emi',
                    'name' => 'PT Evo Manufacturing Indonesia',
                    'src' => asset('logo/EMI.png'),
                    'fallback' => 'EMI',
                ],
                [
                    'id' => 'enb',
                    'name' => 'PT Evo Nusa Bersaudara',
                    'src' => asset('logo/ENB.png'),
                    'fallback' => 'ENB',
                ],
                [
                    'id' => 'gmn',
                    'name' => 'PT Graha Maju Nusantara',
                    'src' => asset('logo/GMN.png'),
                    'fallback' => 'GMN',
                ],
            ],
        ];
    }

    protected function buildNavigation(Request $request): array
    {
        $currentPath = $this->normalizeRequestPath($request);
        $bscUrl = '/bsc-kategori';
        $isBscActive = $this->pathMatches($currentPath, $bscUrl);

        return [
            'home' => [
                'title' => 'Home',
                'subtitle' => 'BSC Kategori',
                'url' => $bscUrl,
                'icon' => 'bi bi-house-door-fill',
                'isActive' => $isBscActive,
            ],
            'modules' => [
                [
                    'id' => 'kpi',
                    'code' => 'KPI',
                    'name' => 'KPI',
                    'label' => 'Key Performance Indicator',
                    'icon' => 'bi bi-graph-up-arrow',
                    'landingUrl' => $bscUrl,
                    'isActive' => $isBscActive,
                    'activeGroupId' => 'master-data',
                    'groups' => [
                        [
                            'id' => 'master-data',
                            'title' => 'Master Data',
                            'items' => [
                                [
                                    'id' => 'bsc-kategori',
                                    'jenisPage' => 'MasterBscKategori',
                                    'title' => 'BSC Kategori',
                                    'url' => $bscUrl,
                                    'icon' => 'bi bi-collection-fill',
                                    'isActive' => $isBscActive,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function buildShellMeta(Request $request, array $navigation): array
    {
        $currentPath = $this->normalizeRequestPath($request);
        $home = $navigation['home'] ?? [];
        $module = $navigation['modules'][0] ?? null;
        $item = $module['groups'][0]['items'][0] ?? null;
        $isBscActive = $item && $this->pathMatches($currentPath, (string) $item['url']);

        return [
            'currentUrl' => $currentPath,
            'activeModule' => $isBscActive ? 'KPI' : 'HOME',
            'activeModuleLabel' => $isBscActive ? 'Key Performance Indicator' : 'BSC Kategori',
            'activeModuleUrl' => $item['url'] ?? $home['url'] ?? '/bsc-kategori',
            'activeSubMenu' => $isBscActive ? $item['jenisPage'] : null,
            'activeSubMenuLabel' => $isBscActive ? $item['title'] : null,
            'currentPageTitle' => $isBscActive ? $item['title'] : 'BSC Kategori',
            'breadcrumbs' => [
                [
                    'label' => 'HOME',
                    'url' => $home['url'] ?? '/bsc-kategori',
                    'active' => ! $isBscActive,
                ],
                [
                    'label' => 'KPI',
                    'url' => $item['url'] ?? '/bsc-kategori',
                    'active' => false,
                ],
                [
                    'label' => 'BSC Kategori',
                    'url' => $item['url'] ?? '/bsc-kategori',
                    'active' => true,
                ],
            ],
        ];
    }

    protected function buildHelpItems(): array
    {
        return [
            [
                'label' => 'Materi Pembelajaran',
                'description' => 'Contoh CRUD sederhana untuk BSC Kategori.',
            ],
        ];
    }

    protected function normalizeRequestPath(Request $request): string
    {
        $path = '/' . ltrim($request->path(), '/');
        $normalizedPath = rtrim($path, '/');

        return $normalizedPath !== '' ? $normalizedPath : '/';
    }

    protected function pathMatches(string $currentPath, string $targetUrl): bool
    {
        $targetPath = rtrim(parse_url($targetUrl, PHP_URL_PATH) ?: '', '/') ?: '/';
        $currentPath = rtrim($currentPath, '/') ?: '/';

        return $currentPath === $targetPath || str_starts_with($currentPath, $targetPath . '/');
    }
}
