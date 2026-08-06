<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetupPdfFont extends Command
{
    protected $signature = 'pdf:setup-font';
    protected $description = 'Download IPAex Gothic font for PDF generation';

    private const FONT_URL = 'https://moji.or.jp/wp-content/ipafont/IPAexfont/ipaexg00401.zip';
    private const FONT_FILENAME = 'ipaexg.ttf';

    public function handle(): int
    {
        $fontDir = storage_path('fonts');
        $fontPath = $fontDir . '/' . self::FONT_FILENAME;

        if (file_exists($fontPath)) {
            $this->info('IPAexゴシックは既にインストール済みです: ' . $fontPath);
            return self::SUCCESS;
        }

        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        $this->info('IPAexゴシックをダウンロードしています...');

        $zipPath = $fontDir . '/ipaexg.zip';

        $response = Http::timeout(60)->get(self::FONT_URL);

        if (!$response->successful()) {
            $this->error('ダウンロードに失敗しました。手動で配置してください: ' . $fontPath);
            return self::FAILURE;
        }

        file_put_contents($zipPath, $response->body());

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_ends_with($name, self::FONT_FILENAME)) {
                    $contents = $zip->getFromIndex($i);
                    file_put_contents($fontPath, $contents);
                    break;
                }
            }
            $zip->close();
        }

        unlink($zipPath);

        if (!file_exists($fontPath)) {
            $this->error('フォントの展開に失敗しました。');
            return self::FAILURE;
        }

        $this->info('IPAexゴシックをインストールしました: ' . $fontPath);

        return self::SUCCESS;
    }
}
