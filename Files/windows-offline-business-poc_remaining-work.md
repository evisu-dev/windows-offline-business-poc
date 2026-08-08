# Windows業務アプリPoC — 不足対応一覧

作成日: 2026-08-08（JST）  
対象リポジトリ: `evisu-dev/windows-offline-business-poc`

## 1. 現在の判定

現時点の判定は以下。

- Windows開発環境でのLaravelテスト: **合格**
- NativePHP Windowsビルド: **合格**
- Windowsインストーラー生成: **合格**
- Windows Phase1全体: **未完了**
- PoC全体: **未完了**

最新のWindows作業では以下を確認済み。

- PHP 8.4.24
- Node.js 22.16.0
- Composer 2.10.2
- Windows AMD64環境
- Laravelテスト `28 tests / 57 assertions` 成功
- NativePHPによるWindows配布物生成
- `Laravel-0.1.0-setup.exe` の生成

ただし、Phase1の目的である「開発環境ではないWindows PCへ配布し、オフラインでも業務データを扱えること」の確認が不足している。

---

# 2. 今やる対応

追加機能の実装へ進む前に、以下の3件を完了する。

## 2.1 ビルド設定・証跡収集手順の修正

### APP_NAMEを修正する

現在の `.env.example` は以下。

```env
APP_NAME=Laravel
```

このため、Windows配布物も以下の名称になっている。

```text
Laravel-0.1.0-setup.exe
laravel.exe
```

PoC用アプリ名へ変更する。

例:

```env
APP_NAME="Offline Work Order Manager"
```

必要に応じてNativePHP側のアプリ名設定も確認し、インストーラー名・実行ファイル名・ウィンドウタイトルが想定した名称になることを確認する。

### `collect-build-evidence.ps1` の成果物パスを修正する

現在の証跡収集スクリプトはルート直下の以下を参照している。

```powershell
$distPath = Join-Path $ProjectRoot 'dist'
```

実際のWindowsビルド成果物は以下。

```text
nativephp\electron\dist
```

そのため、実際の生成先に合わせて修正する。

例:

```powershell
$distPath = Join-Path $ProjectRoot 'nativephp\electron\dist'
```

### ドキュメントも同じパスへ統一する

以下の記載を確認し、成果物パスを統一する。

- `Files/windows_business_app_poc_windows_phase1_steps.md`
- `docs/08_deployment_and_update.md`
- `scripts/collect-build-evidence.ps1`

成果物パスは実際のビルド結果に合わせる。

```text
nativephp\electron\dist
```

### 証跡収集内容を整理する

最低限、以下を記録する。

- Windowsバージョン
- CPUアーキテクチャ
- PHPバージョン
- Node.jsバージョン
- npmバージョン
- Composerバージョン
- Laravelバージョン
- NativePHP Desktopバージョン
- Electron関連バージョン
- 配布物ファイル名
- 配布物ファイルサイズ
- 配布物SHA-256
- bundled PHPの有無
- ビルド日時
- ビルド時の警告・エラー

---

## 2.2 クリーンWindows環境で配布試験を行う

開発環境とは別のWindows環境を用意する。

推奨条件:

```text
Windows 11 x64
PHP 未導入
Node.js 未導入
Composer 未導入
Git 未導入でもよい
```

開発PCで生成したインストーラーのみを渡して検証する。

### 確認項目

#### インストール

- [ ] インストーラーを起動できる
- [ ] インストールを完了できる
- [ ] PHPを別途インストールしなくても動作する
- [ ] Node.jsを別途インストールしなくても動作する
- [ ] Composerを別途インストールしなくても動作する

#### 初回起動

- [ ] アプリを正常起動できる
- [ ] メイン画面が表示される
- [ ] Windows側で致命的なエラーが発生しない
- [ ] SQLiteデータベースが作成される
- [ ] SQLiteの保存場所を特定できる

#### SQLite書き込み

最低1件、実際の画面からデータを登録する。

確認例:

```text
顧客登録
または
受注登録
```

- [ ] SQLiteへデータを書き込める
- [ ] アプリを正常終了できる

#### 再起動後データ保持

アプリを終了し、再度起動する。

- [ ] 前回登録したデータが残っている
- [ ] SQLiteファイルが別の場所へ作り直されていない
- [ ] データ破損がない

---

## 2.3 オフライン・Windows固有挙動を検証する

クリーンWindows環境でネットワークを切断する。

例:

```text
Wi-Fi OFF
LANケーブルを抜く
```

その状態で以下を確認する。

### オフライン起動

- [ ] ネットワークなしでアプリを起動できる
- [ ] 外部APIやCDN待ちで画面が停止しない
- [ ] CSS / JavaScriptが正常表示される

### オフラインSQLite操作

- [ ] 顧客登録
- [ ] 顧客更新
- [ ] 受注登録
- [ ] 受注更新
- [ ] アプリ終了
- [ ] 再起動
- [ ] データ保持

最低限「オフライン状態でも業務データの登録・再表示が可能」であることを確認する。

### Windows SmartScreen

インストーラー初回起動時の挙動を記録する。

確認内容:

- [ ] SmartScreen警告の有無
- [ ] 「不明な発行元」表示の有無
- [ ] 警告が出ても利用者がインストール可能か
- [ ] 画面キャプチャを保存

コード署名はPhase1では実施しなくてよい。

警告内容と、実案件でユーザーへどう説明する必要があるかを記録する。

### Windows Defender / ウイルス対策

最低限Windows Defenderで確認する。

- [ ] インストーラーが削除されない
- [ ] 実行ファイルが隔離されない
- [ ] アプリ起動時にブロックされない
- [ ] SQLite書き込みが妨害されない

誤検知が出た場合は以下を記録する。

```text
検知名:
対象ファイル:
再現手順:
回避策:
実案件への影響:
```

---

# 3. Phase1結果ファイルを正式に更新する

現在の `PHASE1_RESULT_TEMPLATE.md` は未確認状態のままなので、実測結果で更新する。

最低限以下を記入する。

```text
開始日時（JST）
終了日時（JST）
実作業時間
判定
```

## 環境

```text
W11-BUILD
W11-CLEAN
CPUアーキテクチャ
PHP
Laravel
NativePHP Desktop
nativephp/php-bin
Node.js
Composer
Electron
electron-builder
```

## 判定項目

- [ ] `native:run` 起動
- [ ] SQLite書き込み
- [ ] 再起動後のデータ保持
- [ ] Windows配布物生成
- [ ] クリーン環境へのインストール
- [ ] PHP/Node未導入で起動
- [ ] ネットワーク切断状態で起動
- [ ] オフラインSQLite書き込み
- [ ] SmartScreen・発行元警告記録
- [ ] ウイルス対策ソフトの挙動

すべて完了した時点でPhase1の正式判定を行う。

---

# 4. Phase1合格条件

以下がすべて成立した場合、Phase1を合格とする。

- [ ] Windows開発環境で依存関係を再現できる
- [ ] Laravelテストがすべて成功する
- [ ] NativePHPアプリをWindowsで起動できる
- [ ] SQLiteへ書き込める
- [ ] 再起動後もSQLiteデータが保持される
- [ ] Windows向けインストーラーを生成できる
- [ ] PHP / Node.js未導入Windowsへインストールできる
- [ ] PHP / Node.js未導入状態で起動できる
- [ ] ネットワーク切断状態で起動できる
- [ ] オフライン状態でSQLiteへ書き込める
- [ ] SmartScreen / 発行元警告の挙動を記録済み
- [ ] Windows Defender等の挙動を記録済み
- [ ] SQLite保存場所を確認済み
- [ ] ビルド手順が再現可能
- [ ] Phase1結果記録がGitへ保存されている

---

# 5. Phase1完了後に対応する不足機能

以下はPhase1合格前には対応しない。

## 5.1 顧客検索

現在の顧客一覧は全件取得。

追加候補:

- 顧客名部分一致検索
- 電話番号検索
- メールアドレス検索

PoCでは最低限、顧客名部分一致検索のみでよい。

---

## 5.2 受注検索・絞り込み

現在の受注一覧は全件取得。

最低限追加する。

- 顧客
- ステータス
- 日付

必要であれば件名検索を追加する。

---

## 5.3 顧客CSV取込

現状は受注CSV出力のみ。

追加する。

- CSVファイル選択
- UTF-8 / BOM対応
- 必須項目チェック
- 不正行のエラー表示
- 正常行の登録
- 重複時の扱いを決定

PoCでは複雑なマッピング機能は不要。

---

## 5.4 顧客CSV出力

顧客データをCSVとして出力できるようにする。

最低項目:

```text
ID
顧客名
電話番号
メールアドレス
住所
登録日
```

---

## 5.5 PC間データ移行試験

PC Aでデータを作成し、バックアップする。

PC Bへアプリをインストールし、バックアップファイルを復元する。

確認:

- [ ] 顧客データ
- [ ] 受注データ
- [ ] 日本語
- [ ] 日付
- [ ] SQLite整合性

---

## 5.6 v0.1 → v0.2更新試験

実運用で重要なため、Phase1合格後に必ず行う。

### v0.1

例:

```text
customers
work_orders
```

の状態でデータを登録する。

### v0.2

既存テーブルへカラムを1件追加する。

例:

```text
customers.memo
```

migrationを追加し、v0.2をビルドする。

### 確認

- [ ] v0.1の既存データが残る
- [ ] migrationが正常適用される
- [ ] 新カラムを利用できる
- [ ] rollbackを要求しない
- [ ] SQLiteファイルが壊れない

---

# 6. 現時点ではやらないもの

以下はPoCの成立確認には不要、または後回しとする。

- 自動アップデート本番運用
- GitHub Releases自動配信
- コード署名
- EVコードサイニング証明書
- インストーラーUIの細かな装飾
- 多言語対応
- 高度なCSVマッピング
- 権限管理
- 複数ユーザー対応
- ネットワーク共有DB
- クラウド同期

まず「単一Windows PC上で、インストール・オフライン利用・SQLite永続化・配布が成立するか」を確定させる。

---

# 7. 推奨実行順

## 今やる

### 1. ビルド関連修正

- APP_NAME修正
- `collect-build-evidence.ps1` 修正
- ドキュメントのdistパス統一
- Windows再ビルド

### 2. クリーンWindows試験

- インストール
- PHP / Node.jsなしで起動
- SQLite書き込み
- 再起動後データ保持
- SQLite保存場所確認

### 3. オフライン・Windows固有試験

- ネットワーク切断
- オフライン起動
- オフラインSQLite操作
- SmartScreen
- Windows Defender
- `PHASE1_RESULT_TEMPLATE.md` 更新

## 後でやる

Phase1合格後に以下へ進む。

1. 顧客・受注検索
2. 顧客CSV入出力
3. PC間バックアップ・復元
4. v0.1 → v0.2更新試験

---

# 8. Phase1終了時の判断

## 合格

クリーンWindows・オフライン環境でも安定して動作する場合。

次フェーズへ進む。

## 保留

NativePHP自体は動作するが、SmartScreen、Defender、SQLite保存先、インストール手順などに実案件上の課題が残る場合。

課題を整理して継続可否を判断する。

## 不合格

以下のような問題が解決困難な場合。

- クリーンPCで起動できない
- PHP / Node.jsへの外部依存が残る
- オフラインで動作しない
- SQLiteが安定して保持されない
- インストーラーの誤検知が実運用上許容できない
- ビルド・配布手順の再現性が低い

この場合、NativePHPへ固執せず別構成を検討する。
