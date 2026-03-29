<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    const CACHE_KEY = 'app_settings';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get ALL settings as a flat key→value map (cached).
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a single setting value. Returns $default if not found.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    /**
     * Save (upsert) one or many key→value pairs.
     * Clears the cache after saving.
     *
     * @param array $data  ['key' => 'value', ...]
     */
    public function save(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get settings for a group, structured for the admin panel.
     * Masks secret values (returns '••••••••' instead of real value).
     */
    public function getGroup(string $group): array
    {
        return Setting::where('group', $group)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($s) => [
                'id'        => $s->id,
                'key'       => $s->key,
                'label'     => $s->label,
                'value'     => ($s->is_secret && $s->value) ? '••••••••' : $s->value,
                'type'      => $s->type,
                'is_secret' => $s->is_secret,
                'group'     => $s->group,
            ])
            ->toArray();
    }

    /**
     * Get all groups with their settings.
     */
    public function getAllGroups(): array
    {
        $groups = Setting::select('group')->distinct()->orderBy('group')->pluck('group');
        $result = [];
        foreach ($groups as $group) {
            $result[$group] = $this->getGroup($group);
        }
        return $result;
    }

    /**
     * Flush cache (useful after any update).
     */
    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
