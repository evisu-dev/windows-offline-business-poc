# Offline Work Order Manager PoC

Windowsインストール型・オフライン業務アプリケーションの技術的成立を検証するPoC。

## 現在の状態

- **Phase1: 合格** (2026-08-08)
- Laravel / NativePHP Windows正式ビルド成功
- Windowsインストーラー生成済み (`Offline Work Order Manager-0.1.0-setup.exe`)
- PHP未導入環境でのインストール・起動、およびシステムNode.jsへの非依存を確認済み
- オフライン起動・SQLite永続化確認済み
- SmartScreen / Windows Defender 問題なし
- Laravelテスト 58件 / 158アサーション 全成功
- CRUD / 受注CSV出力 / PDF / バックアップは先行実装済み
- 顧客名検索・受注検索/絞り込み実装済み
- 顧客CSV取込/出力実装済み

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

成果物: `nativephp\electron\dist\Offline Work Order Manager-0.1.0-setup.exe`

## 次のフェーズ

Phase2（検索・CSV）完了後に進める項目:

1. PC間バックアップ・復元
2. v0.1 → v0.2 更新試験
