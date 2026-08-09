# v0.1.0 → v0.2.0 更新検証

実行日: 2026-08-09
PC: Windows 11 Home（ホストPC直接試験、VM断念）
v0.1 installer: Offline Work Order Manager-0.1.0-setup.exe
v0.1 SHA-256: 567600437E817ABCB9087474526D0AAC87F07A0E4B1ED2F9DE98F757BC9D1DEE
v0.2 installer: Offline Work Order Manager-0.2.0-setup.exe
v0.2 SHA-256: （後日記録）

## 試験方式

当初はVirtualBox VM上で実施予定だったが、以下の理由でホストPC直接試験に変更:

- VM環境構築にかかる時間対効果
- ホストPCで同等の検証が可能（v0.1インストール済み環境が存在）

検証の本質（v0.1既存データがv0.2上書き後も保持されること）はVM/ホスト問わず確認可能。

## 更新前（v0.1環境）

- [x] v0.1インストール済み
- [x] v0.1アプリ起動確認

### 更新前データ作成

顧客:

| 項目 | 値 |
|---|---|
| 名前 | v0.1保持確認顧客 |
| 備考 | v0.1→v0.2更新検証 |

受注:

| 項目 | 値 |
|---|---|
| 件名 | v0.1保持確認受注 |
| ステータス | 進行中 |

- [x] 顧客登録完了
- [x] 受注登録完了
- [x] データ件数記録

## v0.2ビルド

- [x] 検証用migration追加（poc_schema_checks テーブル）
- [x] 本番migration自動実行ロジック追加（AppServiceProvider）
- [x] NATIVEPHP_APP_VERSION=0.2.0
- [x] php artisan test: 58 tests / 158 assertions PASSED
- [x] npm run build: 成功
- [x] php artisan native:build win: 成功
- [x] installer名: Offline Work Order Manager-0.2.0-setup.exe

## v0.2上書きインストール

- [x] 上書きインストール実行
- [x] インストール成功
- [x] 既存AppDataが削除されていない

## v0.2起動

- [x] アプリ起動成功
- [x] バージョン表示: 0.2.0（システム情報画面）

## 既存データ確認

- [x] v0.1保持確認顧客が存在
- [x] v0.1保持確認受注が存在
- [x] 日本語正常
- [x] 顧客検索可能
- [x] 受注検索可能
- [x] CSV出力可能
- [x] データ件数変化なし

## migration適用確認

確認方法: システム情報画面でpoc_schema_checksテーブル存在を確認

- [x] poc_schema_checksテーブルが存在
- [x] 既存テーブル（customers, work_orders）が正常

## v0.2新規データ作成

- [x] v0.2新規顧客登録
- [x] v0.2新規受注登録
- [x] 登録成功

## 再起動確認

- [x] アプリ再起動
- [x] v0.1既存データ保持
- [x] v0.2新規データ保持

## オフライン確認

ネットワーク切断方法: Wi-Fi無効化

- [x] アプリ起動
- [x] v0.1既存データ表示
- [x] v0.2新規データ表示
- [x] 検索動作
- [x] CSV出力
- [x] SQLite更新

## 判定

v0.1.0 → v0.2.0 更新: 合格

備考:
- VM断念しホストPC直接試験で実施
- NativePHP本番環境での自動migration実行はAppServiceProvider::boot()で対応
- 既存データ保持・新規migration適用・オフライン動作すべて確認
