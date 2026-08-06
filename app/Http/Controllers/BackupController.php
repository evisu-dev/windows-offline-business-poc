<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function index(): View
    {
        $dbPath = DB::connection()->getDatabaseName();
        $dbSize = file_exists($dbPath) ? format_bytes(filesize($dbPath)) : '0 B';

        return view('backup.index', [
            'dbPath' => $dbPath,
            'dbSize' => $dbSize,
        ]);
    }

    public function download(): BinaryFileResponse
    {
        $dbPath = DB::connection()->getDatabaseName();

        if (!file_exists($dbPath)) {
            abort(404, 'データベースファイルが見つかりません。');
        }

        $filename = 'backup_' . date('Ymd_His') . '.sqlite';

        return response()->download($dbPath, $filename, [
            'Content-Type' => 'application/x-sqlite3',
        ]);
    }

    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => 'required|file|max:51200', // 50MB上限
        ]);

        $uploaded = $request->file('backup_file');

        // SQLiteファイルの簡易検証（先頭16バイトのマジックナンバー）
        $handle = fopen($uploaded->getRealPath(), 'rb');
        $header = fread($handle, 16);
        fclose($handle);

        if (!str_starts_with($header, 'SQLite format 3')) {
            return redirect()->route('backup.index')
                ->with('error', 'アップロードされたファイルはSQLiteデータベースではありません。');
        }

        $dbPath = DB::connection()->getDatabaseName();

        if ($dbPath === ':memory:' || !file_exists($dbPath)) {
            return redirect()->route('backup.index')
                ->with('error', 'ファイルベースのデータベースでのみ復元できます。');
        }

        // 現在のDBを退避
        $backupPath = $dbPath . '.bak.' . date('YmdHis');
        copy($dbPath, $backupPath);

        // DB接続を切断してからファイルを置き換え
        DB::disconnect();

        copy($uploaded->getRealPath(), $dbPath);

        // 接続を再確立してテーブル存在確認
        try {
            DB::reconnect();
            DB::table('customers')->count();
            DB::table('work_orders')->count();

            // マイグレーションバージョンの整合性チェック
            $currentMigrations = collect(DB::select('SELECT migration FROM migrations'))
                ->pluck('migration')
                ->sort()
                ->values();

            if ($currentMigrations->isEmpty()) {
                throw new \RuntimeException('migrations table is empty or missing');
            }
        } catch (\Throwable $e) {
            // リストア失敗時はバックアップから復元
            DB::disconnect();
            copy($backupPath, $dbPath);
            DB::reconnect();
            unlink($backupPath);

            return redirect()->route('backup.index')
                ->with('error', 'リストアに失敗しました。必要なテーブルが含まれていません。');
        }

        // 退避ファイルを削除
        unlink($backupPath);

        return redirect()->route('backup.index')
            ->with('status', 'データベースを復元しました。');
    }
}
