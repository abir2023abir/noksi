<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings.index', [
            'settings' => $settings,
            'all' => Setting::all_cached(),
        ]);
    }

    public function update(Request $request, ImageService $images)
    {
        $payload = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'image', 'max:4096'],
            'groups' => ['nullable', 'array'],
            'types' => ['nullable', 'array'],
        ]);

        foreach ($payload['settings'] as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $group = $payload['groups'][$key] ?? 'general';
            $type = $payload['types'][$key] ?? 'text';
            Setting::set($key, $value, $group, $type);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                if (! $file) {
                    continue;
                }
                $old = Setting::get($key);
                if (is_string($old)) {
                    $images->delete($old);
                }
                $path = $images->store($file, 'settings', 1600);
                Setting::set($key, $path, $payload['groups'][$key] ?? 'general', 'image');
            }
        }

        return back()->with('success', __('Settings saved.'));
    }

    public function clearCache()
    {
        Cache::forget('settings.all');
        if ($first = Setting::query()->first()) {
            $first->touch();
        }
        Artisan::call('optimize:clear');

        return back()->with('success', __('Cache cleared.'));
    }
}
