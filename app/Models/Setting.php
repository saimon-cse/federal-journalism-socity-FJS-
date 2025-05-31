<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache; // Optional for caching

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group', 'description'];
    protected $primaryKey = 'key'; // If you use key as primary
    public $incrementing = false; // If key is string
    protected $keyType = 'string'; // If key is string

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // For now, just return default or a hardcoded value for site_name
        // Later, this will fetch from the database and cache
        if ($key === 'site_name') {
            return config('app.name', 'IOMS'); // Fallback to app name from config
        }
        if ($key === 'favicon_image') {
            return 'backend/assets/images/default-favicon.png'; // Provide a default path
        }
        // In a real implementation:
        // return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
        //     $setting = self::find($key);
        //     return $setting ? $setting->value : $default;
        // });
        return $default;
    }

    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $type
     * @param string|null $group
     * @param string|null $description
     * @return Setting|null
     */
    public static function set(string $key, $value, ?string $type = 'string', ?string $group = null, ?string $description = null): ?Setting
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]
        );
        // Cache::forget("setting_{$key}"); // Invalidate cache
        return $setting;
    }
}
