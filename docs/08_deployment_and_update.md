# デプロイ・自動更新

## ビルド手順（Windows）

### 前提

- Windows 11 x64
- PHP 8.4.x
- Node.js 22.x
- Composer 2.x

### 手順

```powershell
git clone <repository-url>
cd windows-offline-business-poc
composer install
php artisan native:install
php artisan native:build
```

ビルド成果物は `dist/` ディレクトリに生成される。

## NativePHP設定

### 基本設定（.env）

```env
NATIVEPHP_APP_VERSION=0.1.0
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

### 設定

`config/nativephp.php` の `updater` セクションで設定。

### 本番有効化手順

1. `.env` で `NATIVEPHP_UPDATER_ENABLED=true` に変更
2. 更新プロバイダーの認証情報を設定
3. バージョン番号を `NATIVEPHP_APP_VERSION` でインクリメント
4. ビルドしてリリースをアップロード

### GitHub Releases を使う場合

```env
NATIVEPHP_UPDATER_ENABLED=true
NATIVEPHP_UPDATER_PROVIDER=github
GITHUB_REPO=windows-offline-business-poc
GITHUB_OWNER=evisu-dev
GITHUB_TOKEN=ghp_xxxxxxxxxxxxxxxxxxxx
GITHUB_V_PREFIXED_TAG_NAME=true
GITHUB_PRIVATE=true
GITHUB_CHANNEL=latest
GITHUB_RELEASE_TYPE=draft
```

### 更新フロー

1. 新バージョンをビルド
2. GitHub Releaseにアップロード（draft → publish）
3. クライアントアプリが起動時に更新チェック
4. 更新があればダウンロード・インストール

### 現状

- 開発環境では `NATIVEPHP_UPDATER_ENABLED=false`
- 本番ビルド時に有効化する設計

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
