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
            'dbSize' => file_exists($dbPath) ? $this->formatBytes(filesize($dbPath)) : '-',
            'os' => PHP_OS_FAMILY . ' ' . php_uname('r'),
            'updaterEnabled' => config('nativephp.updater.enabled') ? 'はい' : 'いいえ',
            'updaterProvider' => config('nativephp.updater.default', '-'),
        ]);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / (1024 ** $i), 1) . ' ' . $units[$i];
    }
}
