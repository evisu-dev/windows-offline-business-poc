# Windows業務アプリPoC — Windowsフェーズ1実行手順

作成日: 2026-08-08（JST）  
対象リポジトリ: `evisu-dev/windows-offline-business-poc`

## 目的

Windows環境の準備完了後、以下の3点を確認する。

1. Windows上でLaravel / NativePHPプロジェクトを再現できる
2. NativePHPで起動し、SQLiteへの書き込みと再起動後のデータ保持を確認できる
3. Windows向け配布物を生成できる

フェーズ1が合格するまでは、CRUD、CSV、PDF、バックアップ、更新検証へ進まない。

---

# 1. Windowsでプロジェクトを再現する

PowerShellでプロジェクトディレクトリへ移動する。

```powershell
Set-Location C:\src\windows-offline-business-poc

git status
git pull
```

## PHP / Composer依存関係

`composer.lock`が存在するため、`composer update`ではなく`composer install`を使用する。

```powershell
composer install
```

## Node.js依存関係

```powershell
npm install
```

## `.env`の準備

`.env`が存在しない場合のみ、`.env.example`から作成する。

```powershell
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    php artisan key:generate
}
```

`.env.example`にはPoC用のNativePHP設定が含まれている。

主な設定:

```text
NATIVEPHP_APP_VERSION=0.1.0
NATIVEPHP_APP_ID=jp.evisuworks.offlineworkorderpoc
NATIVEPHP_APP_AUTHOR="Evisu Works"
NATIVEPHP_UPDATER_ENABLED=false
```

## NativePHPのWindows環境セットアップ

Windows側では新しい開発環境になるため、NativePHP installerを実行する。

```powershell
php artisan native:install --force --no-interaction
```

## Laravel側の確認

```powershell
php artisan optimize:clear
php artisan migrate --force
php artisan test
npm run build
```

ここまでエラーなしで完了した場合、Windows上でLaravel部分を再現できたと判断する。

---

# 2. Laravel単体で起動確認する

NativePHPを起動する前に、通常のLaravelとして正常に動作することを確認する。

```powershell
php artisan serve
```

ブラウザで画面が正常表示されることを確認する。

確認後、PowerShellで `Ctrl + C` を押して停止する。

---

# 3. NativePHPでWindows起動確認する

```powershell
php artisan native:run
```

NativePHPのウィンドウが起動したら、以下を確認する。

## 確認項目

- [ ] NativePHPのウィンドウが正常に開く
- [ ] PoC画面が正常に表示される
- [ ] 「SQLiteへテストデータを書き込む」を実行できる
- [ ] SQLite書き込み件数が増える
- [ ] アプリを終了できる

---

# 4. SQLiteの再起動後データ保持を確認する

一度NativePHPアプリを終了する。

その後、再度起動する。

```powershell
php artisan native:run
```

前回追加したSQLiteデータが保持されていることを確認する。

- [ ] 再起動後もSQLite書き込み件数が保持されている

## migration関連の問題が出た場合

開発中に追加されたmigrationがNativePHP側へ反映されていない場合のみ実行する。

```powershell
php artisan native:migrate
```

その後、再度:

```powershell
php artisan native:run
```

を実行する。

---

# 5. Windows向け配布物を生成する

SQLiteの書き込み・データ保持まで成功してからビルドへ進む。

## Viteビルド

```powershell
npm run build
```

## NativePHP Windowsビルド

```powershell
php artisan native:build
```

Windows上で実行することで、現在のWindowsプラットフォーム・アーキテクチャ向けの配布物を生成する。

---

# 6. ビルド成果物を確認する

NativePHP Desktop v2の成果物は以下の配下を確認する。

```text
nativephp\electron\dist
```

PowerShell:

```powershell
Get-ChildItem .\nativephp\electron\dist -Recurse |
    Select-Object FullName, Length
```

以下を確認する。

- [ ] Windows向けインストーラーまたは実行可能な配布物が生成されている
- [ ] ビルド時に致命的なエラーが発生していない
- [ ] 配布物のファイルサイズを記録した

---

# 7. ビルド証跡を収集する

リポジトリに含まれるスクリプトを使用する。

```powershell
.\scripts\collect-build-evidence.ps1
```

証跡として最低限、以下を残す。

- Windowsバージョン
- CPUアーキテクチャ
- PHPバージョン
- Node.jsバージョン
- Composerバージョン
- NativePHPバージョン
- Electron関連情報
- Windowsビルド成果物
- ビルド時の問題

---

# フェーズ1の今回の実行範囲

今回実行するのは以下の3件のみ。

## 1. Windows再現

```text
composer install
npm install
.env準備
native:install
migration
test
npm run build
```

## 2. NativePHP・SQLite確認

```text
native:run
SQLite書き込み
アプリ終了
native:run
データ保持確認
```

## 3. Windowsビルド

```text
npm run build
native:build
成果物確認
証跡収集
```

---

# この段階では実施しないもの

フェーズ1合格前は以下を実装しない。

- 顧客CRUD
- 受注CRUD
- CSV
- PDF
- バックアップ
- 復元
- PC間データ移行
- v0.1 → v0.2アップデート検証
- 自動アップデート
- コード署名

---

# 次のフェーズへ進む条件

以下が確認できた場合、クリーンWindows環境でのインストール試験へ進む。

- [ ] Windows上で依存関係を再現できる
- [ ] Laravelのテストが通る
- [ ] `native:run`で起動する
- [ ] SQLiteへ書き込める
- [ ] 再起動後もSQLiteデータが保持される
- [ ] `native:build`が成功する
- [ ] Windows向け配布物が生成される

次フェーズでは、PHP / Node.js未導入のWindows環境へ配布物をインストールし、以下を確認する。

- クリーン環境へのインストール
- PHP / Node.js未導入状態での起動
- ネットワーク切断状態での起動
- オフラインSQLite書き込み
- SmartScreen・発行元警告
- ウイルス対策ソフトの挙動

---

# 問題発生時の方針

エラーが出た場合は、追加機能の実装へ進まず、以下を記録する。

```text
実行したコマンド:
発生したエラー:
再現手順:
Windows環境:
対応に使った時間:
回避策:
実案件でのリスク:
```

12時間のPoC上限を守り、NativePHPへ固執せず、配布・保守コストが高い場合は代替構成への切り替えを判断する。
