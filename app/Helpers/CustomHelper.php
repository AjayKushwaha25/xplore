<?php

namespace App\Helpers;

use DB;
use Str;
class CustomHelper
{
    public static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function getAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tables = array_map('current',$tables);
        return $tables;
    }

    public static function ignoredTables()
    {
        return [
            'cache',
            'cache_locks',
            'failed_jobs',
            'imported_files',
            'jobs',
            'migrations',
            'notifications',
            'password_resets',
            'personal_access_tokens',
            'roles',
            'users_roles',
            'users'
        ];
    }
    public static function camelCase2String($str)
    {
        if (empty($str)) {
            return $str;
        }
        // $str = str_replace('_', ' ', $str);
        // dd($str);
        $table = ucwords(str_replace('_', ' ', Str::snake(Str::singular($str))));
        return $table;
    }
}
