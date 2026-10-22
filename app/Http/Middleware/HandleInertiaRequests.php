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

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'base_url' => config('app.url') . '/',
            'tracking' => [
                'google_analytics_measurement_id' => config('services.google_analytics.measurement_id'),
                'facebook_enabled' => config('services.facebook.enabled'),
                'facebook_pixel_id' => config('services.facebook.pixel_id'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }
}
