<?php

use App\Models\Translation;

if(! function_exists('translate')) {

    /**
     * Get translate by key
     *
     * @param $key
     * @return string
     */
    function translate($key, ...$attributes) {
        return sprintf(Translation::getValue($key), ...$attributes);
    }

}

