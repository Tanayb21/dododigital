<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * GET /admin/settings
     * Return all settings grouped (secrets masked).
     */
    public function index()
    {
        return response()->json($this->settingService->getAllGroups());
    }

    /**
     * GET /admin/settings/{group}
     * Return settings for a specific group.
     */
    public function group(string $group)
    {
        return response()->json($this->settingService->getGroup($group));
    }

    /**
     * POST /admin/settings
     * Save one or many settings. Body: { "key": "value", ... }
     * Secret fields: if value is '••••••••' (placeholder), skip the update.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            '*' => 'nullable|string',
        ]);

        $toSave = [];
        foreach ($data as $key => $value) {
            // Skip if the client sent back the masked placeholder
            if ($value === '••••••••') continue;

            // Validate key exists in DB (security: don't allow arbitrary keys)
            if (Setting::where('key', $key)->exists()) {
                $toSave[$key] = $value;
            }
        }

        if (!empty($toSave)) {
            $this->settingService->save($toSave);
        }

        return response()->json([
            'message'  => 'Settings saved successfully.',
            'updated'  => array_keys($toSave),
        ]);
    }

    /**
     * GET /api/settings/public
     * Non-admin: return only public (non-secret) settings.
     * Used by frontend to load branding, app name, etc.
     */
    public function publicSettings()
    {
        $settings = Setting::where('is_secret', false)
            ->whereIn('group', ['general', 'branding', 'theme'])
            ->get()
            ->pluck('value', 'key');

        return response()->json($settings);
    }
}
