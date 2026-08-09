# デプロイ・自動更新

## ビルド手順（Windows）

### 前提

- Windows 11 x64
- PHP 8.4.x
- Node.js 22.x
- Composer 2.x

### 手順

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

ビルド成果物は `nativephp/electron/dist/`（Windows表記: `nativephp\electron\dist`）に生成される。

### 証跡収集

ビルド成功後、以下のスクリプトで環境情報と配布物サマリーを収集する。

```powershell
.\scripts\collect-build-evidence.ps1 -Label current
```

証跡は `evidence/` ディレクトリに出力される。PC名やユーザープロファイルの絶対パスは記録しない。

## NativePHP設定

### 基本設定（.env）

```env
NATIVEPHP_APP_VERSION=0.2.0
NATIVEPHP_APP_ID=jp.evisuworks.offlineworkorderpoc
NATIVEPHP_APP_AUTHOR="Evisu Works"
NATIVEPHP_APP_DESCRIPTION="Offline Windows business application PoC"
```

### ウィンドウ設定

`app/Providers/NativeAppServiceProvider.php` で制御:

```php
public function boot(): void
{
    Window::open();
}
```

## 自動更新（Updater）

### 概要

NativePHP Desktop 2.x はElectronの自動更新機能を統合しており、GitHub Releases、S3、DigitalOcean Spacesからの更新配信に対応。

PoCではUpdaterを有効化しておらず、本番運用は検証対象外。

### GitHub Releases を使う場合の設定例

公開リポジトリを前提とした例:

```env
NATIVEPHP_UPDATER_ENABLED=true
NATIVEPHP_UPDATER_PROVIDER=github
GITHUB_REPO=windows-offline-business-poc
GITHUB_OWNER=evisu-dev
GITHUB_TOKEN=
GITHUB_V_PREFIXED_TAG_NAME=true
GITHUB_PRIVATE=false
GITHUB_CHANNEL=latest
GITHUB_RELEASE_TYPE=draft
```

トークン等の秘密値は `.env` にのみ設定し、Gitへcommitしない。

## NSIS インストーラー設定

Windows向けインストーラーは NSIS を使用:

```php
// config/nativephp.php
'nsis' => [
    'delete_app_data_on_uninstall' => env('NATIVEPHP_NSIS_DELETE_APP_DATA', false),
],
```

`false` の場合、アンインストール後もユーザーデータ（SQLite）を保持する。

## バージョン管理方針

- `NATIVEPHP_APP_VERSION` でセマンティックバージョニング
- 各リリースでインクリメント
- `config('nativephp.version')` でアプリ内から参照可能
- システム情報画面（`/system`）で確認可能

## アプリ更新時のデータ保持

### 設計方針

- NSISインストーラーの上書きインストールで更新する
- `delete_app_data_on_uninstall` が `false` のため、アンインストールしてもユーザーデータ（SQLite）は保持される
- 上書きインストール時はAppData配下のSQLiteファイルに影響しない

### マイグレーション

NativePHP Desktop v2では、本番環境でアプリのversionが変更された場合、ユーザーのAppData配下のSQLiteに対してmigrationが試行される。そのため、リリースごとに `NATIVEPHP_APP_VERSION` を更新する。

開発環境のNativePHP SQLiteを更新する場合は:

```bash
php artisan native:migrate
```

を使用する。

### 更新検証結果

2026-08-09にWindows 11 Homeホスト環境でv0.1.0 → v0.2.0の上書き更新試験を実施し、既存顧客・受注データ保持、新規migration適用、新規データ登録、再起動後保持、オフライン動作を確認した。

検証時点では一時的な独自migration hookを使用していた。その後、NativePHP標準のmigrationライフサイクルへ責務を統一するためhookを削除している。したがって、この証跡は**上書き更新とデータ保持の実測結果**を示すものであり、NativePHP標準migration機構のみを用いた更新試験を別途実施したものではない。

証跡: `evidence/windows-v0.1-to-v0.2-validation.md`
