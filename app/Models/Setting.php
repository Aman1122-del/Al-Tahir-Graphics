<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function set($key, $value, $type = 'string', $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAll()
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Get boolean setting value
     */
    public static function getBoolean($key, $default = false)
    {
        $value = static::get($key, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get integer setting value
     */
    public static function getInteger($key, $default = 0)
    {
        $value = static::get($key, $default);
        return (int) $value;
    }

    /**
     * Get float setting value
     */
    public static function getFloat($key, $default = 0.0)
    {
        $value = static::get($key, $default);
        return (float) $value;
    }
}