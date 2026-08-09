# Offline Work Order Manager PoC

Windowsインストール型・オフライン業務アプリケーションの技術的成立を検証するPoC。

## 現在の状態

- **Phase1: 合格** (2026-08-08)
- **Phase2: 合格** (2026-08-09)
- Windows / NativePHP正式ビルド成功
- PHP未導入環境でのインストール・起動確認済み
- システムNode.jsへの非依存を確認済み
- オフライン起動・SQLite永続化確認済み
- 顧客CRUD・検索・CSV取込/出力実装済み
- 受注CRUD・検索/絞り込み・CSV/PDF出力実装済み
- Windows Phase2実機検証: 合格
- v0.1.0 → v0.2.0上書き更新・既存データ保持: 合格
- Laravelテスト: 58 tests / 158 assertions

## PoC判定

主要な技術検証は完了。

バックアップ・復元機能は実装・Windows上で基本動作確認済み。
別Windows環境へのPC間移行は未検証。

## 未実施の追加検証

以下はPoC合格の必須条件から外し、将来の証跡強化項目として延期している。

- 完全クリーンWindows VM（PHP/Node/Composer未導入）での追加インストール試験
- PC A → PC Bの実機/VM間バックアップ復元試験

## 技術スタック

| 項目 | 技術 |
|---|---|
| フレームワーク | Laravel 12.x |
| PHP | 8.4.x |
| デスクトップ化 | NativePHP Desktop 2.2.1 |
| Electron | 38.5.0 |
| データベース | SQLite |
| PDF | barryvdh/laravel-dompdf |
| ビュー | Blade + CSS |

## 入口

- Mac開発環境: `MAC_SETUP.md`
- Windows開発環境: `scripts/setup-windows.ps1`
- Phase1結果: `PHASE1_RESULT_TEMPLATE.md`
- v0.1→v0.2更新検証: `evidence/windows-v0.1-to-v0.2-validation.md`
- クリーン試験: `evidence/windows-clean-install-checklist.md`
- ビルド成功ログ: `evidence/windows-build-log-success-phase1.txt`
- 仕様書: `docs/`

## スクリプト

### macOS

- `scripts/preflight-macos.sh` — 前提条件チェック
- `scripts/collect-macos-evidence.sh` — 証跡収集

### Windows

- `scripts/setup-windows.ps1` — 環境セットアップ
- `scripts/collect-build-evidence.ps1` — ビルド証跡収集

## Windowsビルド手順

```powershell
$env:Path = "C:\node22\node-v22.16.0-win-x64;C:\php84;" + $env:Path
composer install
npm install
php artisan migrate --force
php artisan test
npm run build
php artisan native:build win
```

成果物: `nativephp\electron\dist\Offline Work Order Manager-0.2.0-setup.exe`

## 検証用migration

`database/migrations/2026_08_09_100000_create_poc_schema_checks_table.php` は業務機能ではなく、アプリ更新時のmigration適用を証明するための検証用スキーマ。
