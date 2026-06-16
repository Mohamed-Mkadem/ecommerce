<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\TopBarSetting;
use Illuminate\Support\Facades\Cache;

class TopBarSettingController extends Controller
{
    public function index()
    {
        $settings = TopBarSetting::first();
        return Inertia::render('Admin/TopBar/Index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'status' => ['required', "in:1,0"],
            'text' => ['required', 'string']
        ]);
        $settings = TopBarSetting::first();

        $settings->update([
            'is_visible' => (bool) $request->status,
            'text_content' => $request->text
        ]);
        Cache::forget('top_bar_settings');

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
