# v0.1.0 → v0.2.0 更新検証

実行日: 2026-08-09  
PC: Windows 11 Home（ホストPC直接試験）  
v0.1 installer: Offline Work Order Manager-0.1.0-setup.exe  
v0.1 SHA-256: 567600437E817ABCB9087474526D0AAC87F07A0E4B1ED2F9DE98F757BC9D1DEE  
v0.2 installer: Offline Work Order Manager-0.2.0-setup.exe  
v0.2 SHA-256: 42394B3A428E925DA6836E6586A43CE8BC71019EAECC62B433D35DC13631555C

## 試験方式

当初はVirtualBox VM上で実施予定だったが、VM環境構築の時間対効果を考慮し、v0.1がインストール済みのWindows 11 HomeホストPCで直接実施した。

## 更新前

- [x] v0.1インストール済み
- [x] v0.1アプリ起動確認
- [x] 更新保持確認用の顧客を登録
- [x] 更新保持確認用の受注を登録
- [x] 更新前データ件数を記録

## v0.2ビルド

- [x] 検証用migration追加（`poc_schema_checks` テーブル）
- [x] 試験時点ではAppServiceProviderに一時的なmigration hookを追加
- [x] `NATIVEPHP_APP_VERSION=0.2.0`
- [x] `php artisan test`: 58 tests / 158 assertions PASSED
- [x] `npm run build`: 成功
- [x] `php artisan native:build win`: 成功
- [x] `Offline Work Order Manager-0.2.0-setup.exe` 生成

## v0.2上書きインストール

- [x] 上書きインストール成功
- [x] 既存AppDataが削除されていない
- [x] アプリ起動成功
- [x] バージョン0.2.0表示

## 既存データ確認

- [x] v0.1で作成した顧客が存在
- [x] v0.1で作成した受注が存在
- [x] 日本語正常
- [x] 顧客検索可能
- [x] 受注検索可能
- [x] CSV出力可能
- [x] データ件数変化なし

## migration適用確認

確認方法: システム情報画面で `poc_schema_checks` テーブル存在を確認。

- [x] `poc_schema_checks` テーブルが存在
- [x] 既存テーブル（customers, work_orders）が正常

## v0.2新規データ・再起動・オフライン

- [x] v0.2新規顧客登録
- [x] v0.2新規受注登録
- [x] アプリ再起動後もv0.1/v0.2データ保持
- [x] Wi-Fi無効化状態でアプリ起動
- [x] オフラインで既存データ表示
- [x] オフラインで検索・CSV出力・SQLite更新

## 判定

**v0.1.0 → v0.2.0 更新: 合格**

## 現行実装との差分

本検証時点では更新時migrationを確実に適用するため、`AppServiceProvider::boot()` から一時的な独自migration hookを使用していた。検証後、NativePHP標準のmigrationライフサイクルへ責務を統一するため、このhookは削除済み。

したがって本資料は、**v0.1→v0.2上書き更新時の既存データ保持・migration適用・更新後動作を実測した証跡**として扱う。NativePHP標準migration機構のみを用いた更新試験を別途実施したものではない。
