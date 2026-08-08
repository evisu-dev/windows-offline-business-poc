# Windows業務アプリPoC — Windows側で対応すべき作業一覧

作成日: 2026-08-08（JST）  
対象リポジトリ: `evisu-dev/windows-offline-business-poc`

## 1. 目的

Mac側で以下の修正が反映された後、Windows側でPhase1の正式検証を完了する。

Mac側で反映済みの主な修正:

- `APP_NAME="Offline Work Order Manager"`
- NativePHP成果物パスを `nativephp\electron\dist` に統一
- `scripts/collect-build-evidence.ps1` の成果物パス修正
- SHA-256 / ファイル一覧 / bundled PHP証跡の追加
- `docs/08_deployment_and_update.md` の修正
- `PHASE1_RESULT_TEMPLATE.md` の改善
- 失敗ビルドログを成功証跡と分離

Windows側では、以下を確認する。

1. 最新コードでWindowsビルドが再現できる
2. 正しいアプリ名で配布物が生成される
3. PHP / Node.js未導入のクリーンWindowsへインストールできる
4. ネットワーク切断状態でも起動・SQLite操作できる
5. SmartScreen / Windows Defenderの挙動を確認する
6. Phase1結果を正式記録する

---

# 2. 最初にGitの状態を確認する

過去のコミットSHAが変わっているため、Windows側で作業を始める前にローカルと `origin/main` の状態を確認する。

PowerShell:

```powershell
Set-Location C:\src\windows-offline-business-poc

git status
git remote -v
git fetch origin
git log --oneline --decorate -10
git log --oneline HEAD..origin/main
git log --oneline origin/main..HEAD
```

## 判定

### ローカル変更がない場合

```powershell
git pull --ff-only
```

### ローカル変更がある場合

すぐに `git pull` しない。

まず確認:

```powershell
git status
git diff
```

必要な変更であればcommitまたはstashしてから最新化する。

不要な変更を安易に削除しない。

---

# 3. 最新設定を確認する

最新化後:

```powershell
git status

Select-String -Path .env.example -Pattern '^APP_NAME='
```

期待値:

```text
APP_NAME="Offline Work Order Manager"
```

証跡スクリプト内の成果物パスも確認する。

```powershell
Select-String -Path .\scripts\collect-build-evidence.ps1 -Pattern 'nativephp'
```

期待する成果物パス:

```text
nativephp\electron\dist
```

---

# 4. Windows開発環境を再確認する

PowerShell:

```powershell
php -v
composer --version
node --version
npm --version
git --version
```

想定:

```text
PHP 8.4.x
Composer 2.x
Node.js 22.x
npm
Git
```

PHP extensions:

```powershell
php -m | Select-String -Pattern 'zip|pdo_sqlite|sqlite3|mbstring|openssl|fileinfo'
```

最低限確認:

- [ ] zip
- [ ] pdo_sqlite
- [ ] sqlite3
- [ ] mbstring
- [ ] openssl
- [ ] fileinfo

Windows側で固定パスを使っている場合は、既存環境のパスを使用する。

例:

```powershell
$env:Path = "C:\node22\node-v22.16.0-win-x64;C:\php84;" + $env:Path
```

---

# 5. 依存関係を再現する

最新コード取得後に依存関係を復元する。

```powershell
composer install
npm install
```

`composer update` は使用しない。

`.env` が存在しない場合のみ作成:

```powershell
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    php artisan key:generate
}
```

## 既存 `.env` を使っている場合

`.env.example` の `APP_NAME` 修正は既存 `.env` へ自動反映されない。

確認:

```powershell
Select-String -Path .env -Pattern '^APP_NAME='
```

古い場合は修正:

```env
APP_NAME="Offline Work Order Manager"
```

---

# 6. Laravel側の確認

```powershell
php artisan optimize:clear
php artisan migrate --force
php artisan test
npm run build
```

## 合格条件

- [ ] migration成功
- [ ] Laravelテスト全成功
- [ ] Vite build成功
- [ ] 致命的なPHPエラーなし

現在の基準:

```text
28 tests
57 assertions
```

テスト結果はWindows証跡として保存する。

例:

```powershell
php artisan test --no-ansi |
    Tee-Object -FilePath .\evidence\windows-test-result-phase1.txt
```

---

# 7. NativePHP開発起動を確認する

```powershell
php artisan native:run
```

確認:

- [ ] Windows上でNativePHPウィンドウが開く
- [ ] ウィンドウ / アプリ表示名が `Offline Work Order Manager`
- [ ] メイン画面が正常表示される
- [ ] CSS / JavaScriptが正常
- [ ] Laravel例外画面が出ない

---

# 8. Windows開発環境でSQLite永続化を確認する

NativePHP上で実際にデータを登録する。

例:

```text
顧客を1件登録
受注を1件登録
```

確認:

- [ ] 登録成功
- [ ] 一覧に表示される
- [ ] NativePHPアプリを終了できる

その後再度:

```powershell
php artisan native:run
```

確認:

- [ ] 顧客データが残っている
- [ ] 受注データが残っている
- [ ] SQLiteファイルが別DBとして新規作成されていない

## SQLite保存場所を確認する

アプリの「システム情報」画面等でDB pathを確認する。

必ず以下を記録する。

```text
SQLite保存場所:
SQLiteファイルサイズ:
```

Phase1結果へ記入する。

---

# 9. 正式Windowsビルドを行う

NativePHP開発起動・SQLite保持が確認できたらビルドする。

```powershell
npm run build
php artisan native:build
```

成果物確認:

```powershell
Get-ChildItem .\nativephp\electron\dist -Recurse -File |
    Select-Object FullName, Length, LastWriteTime
```

## 合格条件

- [ ] `native:build` が正常終了
- [ ] Windowsインストーラーが生成される
- [ ] `win-unpacked` が生成される
- [ ] アプリ名が `Offline Work Order Manager`
- [ ] 旧 `Laravel-0.1.0-setup.exe` 名になっていない

---

# 10. 正式ビルド証跡を収集する

ビルド成功後:

```powershell
.\scripts\collect-build-evidence.ps1
```

期待される主な証跡:

```text
evidence/windows-build-environment-phase1.txt
evidence/windows-composer-packages-phase1.txt
evidence/windows-php-modules-phase1.txt
evidence/windows-laravel-about-phase1.txt
evidence/windows-build-artifacts-phase1.txt
evidence/windows-build-hashes-phase1.txt
evidence/windows-bundled-php-phase1.txt
```

確認:

```powershell
Get-ChildItem .\evidence
```

特に確認する。

- [ ] Windows環境情報
- [ ] 成果物一覧
- [ ] 成果物サイズ
- [ ] SHA-256
- [ ] bundled PHP確認結果

---

# 11. 成功ログを残す

過去の失敗ログ:

```text
evidence/windows-build-log-failed-20260808.txt
```

とは分ける。

成功したビルドログを保存する。

例:

```powershell
php artisan native:build *>&1 |
    Tee-Object -FilePath .\evidence\windows-build-log-success-phase1.txt
```

注意:

上記を実行して再ビルドする場合は、実際にそのコマンドが成功したことを確認する。

単に既存ログを成功ログとしてリネームしない。

---

# 12. クリーンWindows環境を用意する

Phase1の最重要試験。

開発PCとは別のWindows環境を使用する。

推奨:

```text
Windows 11 x64
PHP 未導入
Node.js 未導入
Composer 未導入
Git 未導入
```

候補:

- 別Windows PC
- Windows Sandbox
- Windows VM
- 初期状態へ戻せる検証用Windows

## 注意

開発環境のPATHやツールを流用しない。

クリーン環境には原則として以下だけを渡す。

```text
Windowsインストーラー
```

---

# 13. クリーンWindowsへインストールする

ビルドPCから生成したインストーラーをクリーンWindowsへコピーする。

インストール前に確認:

- [ ] PHPがインストールされていない
- [ ] Node.jsがインストールされていない
- [ ] Composerがインストールされていない

必要であればコマンド確認:

```powershell
php -v
node --version
composer --version
```

「認識されません」となる状態で問題ない。

その状態でインストーラーを実行する。

確認:

- [ ] インストーラー起動
- [ ] インストール完了
- [ ] PHPを別途要求されない
- [ ] Node.jsを別途要求されない
- [ ] Composerを別途要求されない

---

# 14. SmartScreen / 発行元警告を記録する

コード署名を本番化していないため、警告が出る可能性がある。

確認:

- [ ] SmartScreen警告の有無
- [ ] 「WindowsによってPCが保護されました」の有無
- [ ] 不明な発行元表示の有無
- [ ] 「詳細情報」等から実行可能か
- [ ] 一般利用者へ説明可能な手順か

スクリーンショットを保存する。

記録例:

```text
SmartScreen:
不明な発行元:
インストール継続可否:
必要操作:
実案件への影響:
```

Phase1ではコード署名自体は行わない。

---

# 15. Windows Defenderの挙動を確認する

クリーン環境のWindows Defenderを通常状態のまま使用する。

確認:

- [ ] インストーラーが削除されない
- [ ] Defenderが隔離しない
- [ ] インストール後exeが隔離されない
- [ ] 起動がブロックされない
- [ ] SQLite書き込みが妨害されない

問題が出た場合:

```text
検知名:
対象ファイル:
発生日時:
再現手順:
Windows Defender表示:
回避策:
実案件への影響:
```

を記録する。

---

# 16. クリーンWindowsで初回起動する

インストール後、ネットワーク接続中の状態で一度起動する。

確認:

- [ ] アプリ起動
- [ ] `Offline Work Order Manager` と表示される
- [ ] メイン画面表示
- [ ] CSS / JavaScript表示
- [ ] 顧客登録可能
- [ ] 受注登録可能
- [ ] SQLiteへ保存可能

最低1件ずつデータ登録する。

---

# 17. クリーンWindowsで再起動後データ保持を確認する

アプリを完全終了する。

再度起動。

確認:

- [ ] 登録済み顧客が残る
- [ ] 登録済み受注が残る
- [ ] 日本語が壊れていない
- [ ] SQLite DBが同じ保存場所を使用
- [ ] DB破損なし

---

# 18. ネットワークを切断する

クリーンWindowsでネットワークを切断する。

例:

```text
Wi-Fi OFF
LANケーブルを抜く
```

可能なら、ブラウザ等でインターネット接続不能を確認する。

---

# 19. 完全オフライン状態でアプリを起動する

ネットワーク切断状態でアプリを終了してから再起動する。

確認:

- [ ] オフラインで起動
- [ ] 外部通信待ちで停止しない
- [ ] CSS正常
- [ ] JavaScript正常
- [ ] データ一覧表示
- [ ] 既存SQLiteデータ表示

---

# 20. オフライン状態でSQLite操作する

ネットワーク切断中に以下を行う。

- [ ] 顧客新規登録
- [ ] 顧客更新
- [ ] 受注新規登録
- [ ] 受注更新
- [ ] アプリ終了
- [ ] アプリ再起動
- [ ] 追加・更新データ保持

Phase1の重要な合格条件は、

```text
インターネットなしでも通常業務データを扱える
```

こと。

---

# 21. Phase1結果を記録する

対象:

```text
PHASE1_RESULT_TEMPLATE.md
```

Windows検証の実測値を記入する。

## 環境

最低限記録:

```text
Windows版
CPUアーキテクチャ
PHP（BUILD環境）
Laravel
NativePHP Desktop
Node.js（BUILD環境）
Composer（BUILD環境）
Electron
electron-builder
APP_NAME
```

クリーン環境についても補足として必ず記録する。

```text
W11-BUILD:
W11-CLEAN:
```

## 結果

以下を `合格 / 不合格 / 未確認` で記入する。

- [ ] Windows配布物生成
- [ ] `native:run` 起動
- [ ] SQLite書き込み
- [ ] 再起動後データ保持
- [ ] SQLite保存場所確認
- [ ] クリーン環境へのインストール
- [ ] PHP / Node.js未導入で起動
- [ ] ネットワーク切断状態で起動
- [ ] オフラインSQLite書き込み
- [ ] SmartScreen / 発行元警告
- [ ] Windows Defender

---

# 22. Phase1正式合格条件

以下がすべて成立した場合、Windows Phase1を合格とする。

- [ ] Windows開発環境でLaravelテスト成功
- [ ] NativePHP開発起動成功
- [ ] SQLite書き込み成功
- [ ] 再起動後SQLite保持
- [ ] Windowsインストーラー生成
- [ ] 成果物SHA-256記録
- [ ] PHP / Node.js未導入環境へインストール
- [ ] PHP / Node.jsなしで起動
- [ ] オフライン起動
- [ ] オフラインSQLite操作
- [ ] SQLite保存場所を特定
- [ ] SmartScreen挙動記録
- [ ] Defender挙動記録
- [ ] Phase1結果をGitへ保存
- [ ] ビルド手順が再現可能

---

# 23. Phase1合格後にやること

Phase1合格後に次フェーズへ進む。

優先順:

1. 顧客検索
2. 受注検索・絞り込み
3. 顧客CSV取込 / 出力
4. PC A → PC B バックアップ・復元
5. v0.1 → v0.2 migration / データ保持試験

Phase1完了前に追加機能を増やさない。

---

# 24. 現在やらないもの

Phase1では以下は不要。

- コード署名の本番導入
- EV証明書購入
- GitHub Releases自動配信
- 自動更新本番化
- インストーラーUI改善
- LAN共有
- 複数PC同時利用
- クラウド同期
- 認証・権限管理

---

# 25. Windows側の実行順

## 今やる

### 1. 最新コード取得・再ビルド

```text
git fetch
git pull
composer install
npm install
php artisan test
npm run build
native:run
native:build
```

### 2. 証跡収集

```text
collect-build-evidence.ps1
成功ビルドログ
SHA-256
SQLite保存場所
```

### 3. クリーンWindows試験

```text
PHP / Node.jsなし
インストール
起動
SQLite登録
再起動保持
ネット切断
オフライン起動
オフライン更新
SmartScreen
Defender
PHASE1_RESULT_TEMPLATE更新
```

---

# 26. Windows側完了条件

以下が完了した時点でWindows側作業を終了する。

- [ ] 最新 `main` を取得
- [ ] 正式Windowsビルド成功
- [ ] アプリ名修正確認
- [ ] 証跡収集成功
- [ ] 成功ビルドログ保存
- [ ] SHA-256記録
- [ ] クリーンWindowsインストール成功
- [ ] PHP / Node.jsなし起動成功
- [ ] SQLite保持成功
- [ ] オフライン起動成功
- [ ] オフラインSQLite操作成功
- [ ] SmartScreen確認
- [ ] Defender確認
- [ ] `PHASE1_RESULT_TEMPLATE.md` 完成
- [ ] Git commit
- [ ] Git push

---

# 27. 最終方針

Windows側では追加機能を作らず、配布・インストール・オフライン・データ永続化を検証する。

このPhase1で確認したい本質は、

```text
Laravel / NativePHPで作ったWindows業務アプリを
開発環境のないWindows PCへ配布し、
インターネット接続なしでも
SQLite業務データを安全に扱えるか
```

である。

この条件が成立した段階でPhase1を合格とし、その後に検索、CSV、PC間移行、v0.1→v0.2更新検証へ進む。
