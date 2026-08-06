<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function index(): View
    {
        $dbPath = DB::connection()->getDatabaseName();

        return view('system.index', [
            'appVersion' => config('nativephp.version', '不明'),
            'appId' => config('nativephp.app_id', '不明'),
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'dbDriver' => config('database.default'),
            'dbPath' => $dbPath,
            'dbSize' => file_exists($dbPath) ? format_bytes(filesize($dbPath)) : '-',
            'os' => PHP_OS_FAMILY . ' ' . php_uname('r'),
            'updaterEnabled' => config('nativephp.updater.enabled') ? 'はい' : 'いいえ',
            'updaterProvider' => config('nativephp.updater.default', '-'),
        ]);
    }
}
