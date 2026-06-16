<?php

namespace App\Http\Middleware;

use Inertia\Inertia;
use Inertia\Middleware;
use Illuminate\Http\Request;
use App\Models\TopBarSetting;
use Illuminate\Support\Facades\Cache;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $one_week_in_seconds = 604800;
        $settings = Cache::remember('top_bar_settings', $one_week_in_seconds, function () {
            return TopBarSetting::first();
        });

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'base_url' => config('app.url') . '/',
            'settings' => $settings,
            'flash' => [
                'order_conversion_data' => fn () => $request->session()->get('order_conversion_data'),
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }
}
