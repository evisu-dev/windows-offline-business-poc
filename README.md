# Offline Work Order Manager PoC

Windowsインストール型・オフライン業務アプリケーションの技術的成立を検証するPoC。

## 現在の状態

- **Phase1: 合格**
- **Phase2: 合格**
- Windows / NativePHP正式ビルド成功
- PHP未導入環境でのインストール・起動確認済み
- システムNode.jsへの非依存を確認済み
- オフライン起動・SQLite永続化確認済み
- インストール版SQLite: `%APPDATA%\offline-work-order-manager\database\database.sqlite`
- 顧客CRUD・検索・CSV取込/出力実装済み
- 受注CRUD・検索/絞り込み・CSV/PDF出力実装済み
- Windows Phase2実機検証: 合格
- v0.1.0 → v0.2.0上書き更新・既存データ保持: 合格
- Laravelテスト: 58 tests / 158 assertions

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
- クリーン試験: `evidence/windows-clean-install-checklist.md`
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
php artisan native:build   # win → x64
```

成果物: `nativephp\electron\dist\Offline Work Order Manager-0.2.0-setup.exe`

## PoC判定

主要な技術検証は完了。

## 未実施の追加検証

以下はPoC合格の必須条件から外し、追加証跡強化項目として延期している。

- 完全なPHP/Node/Composer未導入Windows VMでの追加試験
- 別Windows環境へのPC間バックアップ復元
