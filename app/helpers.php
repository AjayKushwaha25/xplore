<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('storage_disk')) {
    function storage_disk()
    {
        if (config('app.env') == 'production' || config('app.env') == 'staging') {
            return \Storage::disk('gcs');
        } else {
            return \Storage::disk('public');
        }
    }
}
