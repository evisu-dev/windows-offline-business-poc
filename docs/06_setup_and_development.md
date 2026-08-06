# 環境構築・開発手順

## 前提条件

| 項目 | 要件 |
|---|---|
| macOS | 12以上 |
| PHP | 8.4.x |
| Node.js | 22.x |
| Composer | 2.x |
| Git | インストール済み |
| PHP拡張 | zip, pdo_sqlite, sqlite3, mbstring, openssl |

## Mac環境構築

### 1. プリフライトチェック

```bash
chmod +x scripts/*.sh
./scripts/preflight-macos.sh
```

### 2. 不足パッケージの導入（Homebrewの場合）

```bash
brew install php@8.4 node@22
echo 'export PATH="$(brew --prefix node@22)/bin:$PATH"' >> ~/.zshrc
exec zsh
```

### 3. プロジェクトセットアップ（既存リポジトリから）

```bash
git clone <repository-url>
cd windows-offline-business-poc
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
```

### 4. 開発サーバー起動

```bash
php artisan serve
# http://127.0.0.1:8000 でアクセス
```

### 5. NativePHPデスクトップ起動

```bash
php artisan native:run
```

### 6. PDF用フォントセットアップ（任意）

```bash
php artisan pdf:setup-font
```

## テスト実行

```bash
php artisan test
```

テスト環境はSQLite `:memory:` を使用（`.env.testing` で切り替え可能）。

## 開発フロー

1. 機能実装
2. `php artisan test` で全テスト合格を確認
3. `php artisan serve` でブラウザ確認
4. Git コミット

## Windows環境構築（ビルド用）

Windows 11 x64環境で同じリポジトリをcloneし、以下を実行:

```powershell
composer install
php artisan native:install
php artisan native:build
```

※ Mac環境ではWindowsビルドは生成不可。
