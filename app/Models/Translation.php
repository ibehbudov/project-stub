<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Translation extends Model
{
    use HasFactory;
    use HasTranslations;

    public $timestamps = false;

    public static $translations = null;

    public $translatable = [
        'value'
    ];

    protected $fillable = [
        'key',
        'value',
    ];


    /**
     * @param $key
     * @return string
     */
    public static function getValue($key): string
    {
        if(is_null(self::$translations)) {
            self::$translations = self::all();
        }

        $translation = self::$translations
            ->where('key', $key)
            ->first();

        if(! $translation) {
            Translation::create([
                'key'   =>  $key,
                'value' =>  $key
            ]);

            self::$translations = self::all();
        }

        return $translation ? $translation->value : $key;
    }
}
