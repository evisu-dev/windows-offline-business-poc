# Mac実装準備 — Windows業務アプリPoC

作成日: 2026-08-06（JST）

## 今回のMac側完了条件

Macでは次だけを完了する。

1. PHP 8.4、Node.js 22、Composer、GitがCLIで利用できる
2. Laravel 12とNativePHP Desktop 2.2.1を固定したプロジェクトを生成する
3. ブラウザで最小画面とSQLite書き込みを確認する
4. `php artisan native:run`でmacOS上の開発ウィンドウを起動する
5. `composer.lock`と生成コードをGit管理できる状態にする

Windows向け配布物はMacで生成しない。Windows 11 x64で同じリポジトリをcloneし、`native:install`と`native:build`を再実行する。

## 採用構成

- Laravel 12.x
- PHP 8.4.x
- NativePHP Desktop 2.2.1
- Node.js 22.x
- SQLite
- Blade + 素のCSS

NativePHP Desktop v2の公式要件はPHP 8.3以上、Laravel 11以上、Node.js 22以上、macOS 12以上。公式サポート表ではNativePHP 2.xのLaravel対応範囲は11.xと12.xである。

## 0. 作業時間を開始する

12時間上限のうち、Mac準備に使った実時間を記録する。

```bash
export POC_STARTED_AT="$(TZ=Asia/Tokyo date '+%Y-%m-%dT%H:%M:%S%z')"
echo "$POC_STARTED_AT"
```

## 1. プリフライト

ZIPを展開し、展開先で実行する。

```bash
chmod +x scripts/*.sh
./scripts/preflight-macos.sh
```

合格基準:

- macOS 12以上
- PHP 8.4.x
- Node.js 22.x
- Composer、npm、Gitが利用可能
- PHP拡張 `zip`、`pdo_sqlite`、`sqlite3`、`mbstring`、`openssl`
- Xcode Command Line Toolsが導入済み

## 2. 不足環境の導入

### 推奨: Laravel Herdを既に使っている場合

HerdでCLIのPHPを8.4へ切り替える。Node.jsは手元のバージョン管理ツールで22へ切り替える。

確認:

```bash
php -v
composer --version
node --version
npm --version
```

### Homebrewを使う場合

```bash
xcode-select --install
brew install php@8.4 node@22 git
```

シェルのPATHへ追加する。Apple SiliconとIntelの差は`brew --prefix`で吸収する。

```bash
cat <<'SHELL' >> ~/.zshrc
export PATH="$(brew --prefix php@8.4)/bin:$(brew --prefix php@8.4)/sbin:$PATH"
export PATH="$(brew --prefix node@22)/bin:$PATH"
SHELL
exec zsh
```

Composerが未導入の場合は、Composer公式インストーラーを使い、現在有効なPHP 8.4で導入する。既存のComposerが動作する場合は入れ直さない。

## 3. プロジェクト生成

デフォルトでは次へ作成する。

```text
~/Development/windows-offline-business-poc
```

実行:

```bash
./scripts/bootstrap-phase1-macos.sh
```

別パスを使う場合:

```bash
./scripts/bootstrap-phase1-macos.sh "$HOME/src/windows-offline-business-poc"
```

スクリプトは空でないディレクトリを上書きしない。

## 4. ブラウザ確認

```bash
cd ~/Development/windows-offline-business-poc
php artisan serve
```

確認:

- 画面に`Offline Work Order Manager PoC`が表示される
- ボタンを押すとSQLite件数が増える
- 外部CDNや外部APIを呼んでいない

確認後、`Ctrl+C`で終了する。

## 5. NativePHP開発起動

初回:

```bash
php artisan native:run
```

確認:

- macOSのデスクトップウィンドウが開く
- SQLiteへ書き込める
- ウィンドウを閉じて再起動しても件数が保持される

初回起動後にmigrationを変更した場合:

```bash
php artisan native:migrate
```

## 6. Gitへ保存

`.env`や秘密情報はcommitしない。`composer.lock`は必ずcommitする。

```bash
git status
git add .
git commit -m "feat: add NativePHP phase 1 minimum app"
```

リモートリポジトリ作成後:

```bash
git remote add origin git@github.com:evisu-dev/windows-offline-business-poc.git
git push -u origin main
```

## 7. Mac側の停止条件

次のいずれかが発生し、Mac準備枠内で原因を特定できない場合は、CRUDへ進まない。

- NativePHP 2.2.1をComposerで解決できない
- `native:install`が完了しない
- ブラウザ上でSQLite書き込みが動かない
- `native:run`が起動しない
- `composer.lock`をWindows側で再現できない可能性が高い

## 今は実装しない

- 顧客・受注CRUD
- Tailwind CSS / Alpine.js
- CSV / PDF
- バックアップ・復元
- Windowsビルド
- 自動更新
- コード署名
