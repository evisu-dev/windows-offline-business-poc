<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->runNativeMigrations();
    }

    /**
     * NativePHP本番環境でアプリ起動時にmigrationを自動適用する。
     *
     * NativePHPはdev mode + 新規DB作成時のみ自動migrateするため、
     * 本番環境でのアプリ更新時（v0.1→v0.2等）に新しいmigrationが
     * 適用されない問題を解決する。
     */
    protected function runNativeMigrations(): void
    {
        if (!config('nativephp-internal.running')) {
            return;
        }

        try {
            Artisan::call('native:migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            // migration失敗時はアプリ起動を妨げない
            // ログに記録して続行
            logger()->error('Auto-migration failed: ' . $e->getMessage());
        }
    }
}
