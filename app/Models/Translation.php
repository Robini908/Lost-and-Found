<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Translation extends Model
{
    use HasTranslations;

    protected $fillable = [
        'group',
        'key',
        'text',
        'namespace',
    ];

    public $translatable = ['text'];

    protected $casts = [
        'text' => 'array',
    ];

    public static function getTranslation(string $key, string $locale = null, string $group = 'messages'): ?string
    {
        $locale = $locale ?? app()->getLocale();

        $translation = static::where('key', $key)
            ->where('group', $group)
            ->first();

        if (!$translation) {
            return null;
        }

        return $translation->getTranslation('text', $locale, false);
    }

    public static function setTranslation(string $key, string $value, string $locale, string $group = 'messages'): void
    {
        $translation = static::firstOrNew([
            'key' => $key,
            'group' => $group,
        ]);

        $texts = $translation->text ?? [];
        $texts[$locale] = $value;

        $translation->text = $texts;
        $translation->save();
    }

    public function getTranslationAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setTranslationAttribute($value)
    {
        $this->attributes['text'] = json_encode($value);
    }
}
