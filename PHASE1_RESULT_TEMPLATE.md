# フェーズ1検証結果

- 開始日時（JST）: 2026-08-08 14:00
- 終了日時（JST）: 2026-08-08 21:30
- 実作業時間: 約8時間
- 判定: 合格

## 環境

| 項目 | 値 |
|---|---|
| W11-BUILD | Microsoft Windows 11 Home 10.0.26200 |
| W11-CLEAN | 同一PC（PHP/Composer未導入、旧Node.js v10.24.1残存） |
| CPUアーキテクチャ | AMD64 |
| PHP | 8.4.24 |
| Laravel | 12.65.0 |
| NativePHP Desktop | 2.2.1 |
| Node.js | v22.16.0 |
| npm | 10.9.2 |
| Composer | 2.10.2 |
| Electron | 38.5.0 |
| electron-builder | 26.8.1 |
| APP_NAME | Offline Work Order Manager |

## 結果

| 確認項目 | 結果 | 証跡 |
|---|---|---|
| Windows配布物生成 | 合格 | `evidence/windows-build-log-success-phase1.txt` |
| `native:run` 起動 | 合格 | ウィンドウタイトル「Offline Work Order Manager」確認 |
| SQLite書き込み | 合格 | 顧客1件・受注1件登録成功 |
| 再起動後のデータ保持 | 合格 | 再起動後データ保持確認 |
| SQLite保存場所の確認 | 合格 | 開発: `database\nativephp.sqlite` / インストール版: `%APPDATA%\offline-work-order-manager\database\database.sqlite` |
| クリーン環境へのインストール | 合格 | PHP未導入PCでインストーラーから起動成功 |
| PHP未導入・システムNode非依存で起動 | 合格 | PHP/Composerなし、旧Node.js v10.24.1残存環境で正常起動。システムNodeには依存せず |
| ネットワーク切断状態で起動 | 合格 | Wi-Fi OFF状態で起動・表示・操作すべて正常 |
| オフラインSQLite書き込み | 合格 | オフラインで顧客・受注登録、再起動後保持確認 |
| SmartScreen・発行元警告記録 | 合格 | 開発PC: 警告なし |
| ウイルス対策ソフトの挙動 | 合格 | Defender: 問題なし |

### Node.js検証条件の補足

実測条件ではシステムに旧Node.js v10.24.1が残存していたが、ビルド用Node.js v22は実行環境では使用していない。アプリはシステムNodeに依存せず正常動作することを確認済み。完全Node未導入VM試験は任意の証跡強化項目とし、Phase1合格の必須条件とはしない。

## 発生した問題

- `native:build` 初回実行時に `composer` がPATHにないエラー → `composer.bat` 作成で解決
- `native:build` で `cross-env` が見つからないエラー → electron側に追加して解決
- ネットワーク回線が遅く、PHP / Node.js / Electron等のダウンロードに時間を要した

## ビルド情報

| 項目 | 値 |
|---|---|
| 成果物パス | `nativephp\electron\dist` |
| インストーラー名 | `Offline Work Order Manager-0.1.0-setup.exe` |
| インストーラーサイズ | 約119MB |
| SHA-256 | `567600437E817ABCB9087474526D0AAC87F07A0E4B1ED2F9DE98F757BC9D1DEE` |
| bundled PHP | `php-8.4-x64` のダウンロード・同梱をビルドログで確認 |

## SQLite保存場所

| 環境 | パス |
|---|---|
| 開発 (`native:run`) | `database\nativephp.sqlite` |
| インストール版 | `%APPDATA%\offline-work-order-manager\database\database.sqlite` |
| 開発版AppData | `%APPDATA%\offline-work-order-manager-dev\database\database.sqlite` |

## 判定

- Phase1: 合格・クローズ
- Phase2: 合格
- v0.1→v0.2更新試験: 合格
- PC間バックアップ復元: 追加検証として延期
- 対象外: コード署名、自動更新本番化、GitHub Releases配信
