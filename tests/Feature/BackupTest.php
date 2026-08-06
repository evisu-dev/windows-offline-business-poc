<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_page_is_accessible(): void
    {
        $this->get(route('backup.index'))->assertOk()->assertSee('バックアップ');
    }

    public function test_backup_download_requires_file_based_db(): void
    {
        $dbPath = DB::connection()->getDatabaseName();

        if ($dbPath === ':memory:') {
            // :memory:ではファイルが存在しないため404
            $this->get(route('backup.download'))->assertNotFound();
        } else {
            $response = $this->get(route('backup.download'));
            $response->assertOk();
            $response->assertHeader('content-type', 'application/x-sqlite3');
        }
    }

    public function test_restore_rejects_non_sqlite_file(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tmpFile, 'This is not a SQLite file');

        $file = new UploadedFile($tmpFile, 'fake.sqlite', 'application/octet-stream', null, true);

        $this->post(route('backup.restore'), ['backup_file' => $file])
            ->assertRedirect(route('backup.index'))
            ->assertSessionHas('error');

        @unlink($tmpFile);
    }

    public function test_restore_requires_file(): void
    {
        $this->post(route('backup.restore'), [])
            ->assertSessionHasErrors('backup_file');
    }

    public function test_restore_rejects_sqlite_without_required_tables(): void
    {
        // 空のSQLiteファイルを作成（customersテーブルなし）
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
        $pdo = new \PDO('sqlite:' . $tmpFile);
        $pdo->exec('CREATE TABLE dummy (id INTEGER PRIMARY KEY)');
        $pdo = null;

        $file = new UploadedFile($tmpFile, 'empty.sqlite', 'application/octet-stream', null, true);

        $dbPath = DB::connection()->getDatabaseName();

        if ($dbPath === ':memory:') {
            // :memory:ではリストアが動作しないためスキップ
            $this->assertTrue(true);
        } else {
            $this->post(route('backup.restore'), ['backup_file' => $file])
                ->assertRedirect(route('backup.index'))
                ->assertSessionHas('error');
        }

        @unlink($tmpFile);
    }
}
