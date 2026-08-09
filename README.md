# Offline Work Order Manager PoC

Windowsインストール型・オフライン業務アプリケーションの技術的成立を検証するPoCです。

> **Note**
> 本リポジトリは技術検証・ポートフォリオ用途のPoCです。本番運用、業務データ保全、サポート提供を保証するものではありません。

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

主要な技術検証は完了しています。

バックアップ・復元機能は実装し、Windows上で基本動作を確認済みです。一方、別Windows環境へのPC間移行は未検証です。

## 未実施の追加検証

以下はPoC合格の必須条件から外し、将来の証跡強化項目として延期しています。

- 完全クリーンWindows VM（PHP / Node.js / Composer未導入）での追加インストール試験
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

## セットアップ / 検証資料

- Mac開発環境: `MAC_SETUP.md`
- Windows開発環境: `scripts/setup-windows.ps1`
- Phase1結果: `PHASE1_RESULT_TEMPLATE.md`
- Phase2 Windows検証: `evidence/windows-phase2-validation.md`
- v0.1→v0.2更新検証: `evidence/windows-v0.1-to-v0.2-validation.md`
- クリーン試験: `evidence/windows-clean-install-checklist.md`
- ビルド成功ログ: `evidence/windows-build-log-success-phase1.txt`
- 仕様書: `docs/`

## Windowsビルド手順

前提:

- Windows 11 x64
- PHP 8.4.x
- Node.js 22.x
- Composer 2.x

```powershell
git clone https://github.com/evisu-dev/windows-offline-business-poc.git
cd windows-offline-business-poc

composer install
npm install

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    php artisan key:generate
}

php artisan migrate --force
php artisan test
npm run build
php artisan native:install --force --no-interaction
php artisan native:build win
```

成果物は `nativephp\electron\dist\` に生成されます。

## 検証用migration

`database/migrations/2026_08_09_100000_create_poc_schema_checks_table.php` は業務機能ではなく、アプリ更新時のmigration適用を確認するための検証用スキーマです。

## 既知の制約

- 別Windows環境へのPC間バックアップ復元は未検証です。
- 完全なNode.js未導入Windows VMでの追加試験は未実施です。
- コード署名、自動更新の本番運用、暗号化バックアップはPoC対象外です。
- Phase1ビルドではNativePHPのSecure app bundleを使用しておらず、ビルドログに `INSECURE BUILD` 警告が記録されています。ソース公開PoCとして扱い、配布用製品としての安全性を主張しません。

## License

MIT License. See `LICENSE`.
