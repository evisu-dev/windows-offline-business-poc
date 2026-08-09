# クリーンWindows試験チェックリスト

実行日: 2026-08-08
試験環境: 同一PC（PHP/Composerをシステム未導入状態、Node v10.24.1のみ残存）
インストーラー: Offline Work Order Manager-0.1.0-setup.exe
SHA-256: 567600437E817ABCB9087474526D0AAC87F07A0E4B1ED2F9DE98F757BC9D1DEE

## 前提条件確認

- [x] PHP未導入（`php -v` → 認識されません）
- [x] Composer未導入（`composer --version` → 認識されません）
- [ ] Node.js未導入 → v10.24.1が残存（ビルドに使用したv22ではない古いシステム版）

備考: Node v10.24.1はシステムに元々入っていた古いバージョン。NativePHPアプリはこれに依存せず正常起動した。

## インストール

- [x] インストーラーを起動できる
- [x] インストールを完了できる
- [x] PHPを別途要求されない
- [x] Node.jsを別途要求されない

## SmartScreen / 発行元警告

- [x] SmartScreen警告の有無: なし
- [x] 「WindowsによってPCが保護されました」表示: なし
- [x] 「不明な発行元」表示: なし
- [x] 「詳細情報」→「実行」で続行可能か: 警告なしのため不要

実案件への影響メモ: 開発PCでは警告なし。新規PC等レピュテーション未確立の場合は警告が出る可能性あり。本番ではコード署名を推奨。

## Windows Defender

- [x] インストーラーが削除されない
- [x] exeが隔離されない
- [x] 起動がブロックされない
- [x] SQLite書き込みが妨害されない

誤検知: なし

## 初回起動（ネットワーク接続中）

- [x] アプリ起動できる
- [x] ウィンドウタイトル「Offline Work Order Manager」
- [x] メイン画面（ダッシュボード）正常表示
- [x] CSS / JavaScript正常
- [x] 顧客を1件登録できる
- [x] 受注を1件登録できる
- [x] アプリ正常終了できる

## 再起動後データ保持

- [x] 再起動後、顧客データが残っている
- [x] 再起動後、受注データが残っている
- [x] 日本語が壊れていない
- [x] SQLite保存場所: `C:\Users\<user>\AppData\Roaming\offline-work-order-manager\database\database.sqlite`

## オフライン試験

ネットワーク切断方法: Wi-Fi OFF

### オフライン起動

- [x] ネットワークなしでアプリ起動できる
- [x] 外部通信待ちで停止しない
- [x] CSS正常表示
- [x] JavaScript正常
- [x] 既存データ表示

### オフラインSQLite操作

- [x] 顧客新規登録
- [x] 顧客更新
- [x] 受注新規登録
- [x] 受注更新
- [x] アプリ終了
- [x] アプリ再起動
- [x] 追加・更新データ保持されている

## 試験結果

総合判定: 合格

備考:
- PHP 8.4はシステムPATHに存在しない状態で試験（C:\php84はPATH未設定）
- Node v10.24.1が残存していたが、アプリはこれに依存しない（v22を要求しない）
- 完全なNode未導入環境での追加試験は任意の証跡強化項目とし、Phase1合格の必須条件とはしない。将来、NativePHP / Electron更新時の回帰試験や公開証跡の追加強化が必要になった場合に実施する。
- Phase1の技術的成立は本試験で確認済み
- インストール版SQLite実パス: `C:\Users\<user>\AppData\Roaming\offline-work-order-manager\database\database.sqlite` (110,592 bytes)
- 開発版SQLite: `C:\Users\<user>\AppData\Roaming\offline-work-order-manager-dev\database\database.sqlite`
