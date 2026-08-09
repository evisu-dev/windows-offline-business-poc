# Phase2 Windows検証結果

実行日: 2026-08-09
対象commit: 83cba01 (main)
Windows: Microsoft Windows 11 Home 10.0.26200
PHP: 8.4.24
Node.js: v22.16.0
Laravel: 12.65.0
NativePHP Desktop: 2.2.1

## 自動テスト

- [x] php artisan test: 58 tests / 158 assertions ALL PASSED
- [x] npm run build: Viteビルド成功

テスト結果ファイル: `evidence/windows-test-result-phase2.txt`

## NativePHP起動確認

- [x] native:run起動
- [x] ウィンドウタイトル「Offline Work Order Manager」
- [x] ダッシュボード正常表示
- [x] 顧客一覧表示
- [x] 受注一覧表示

## 顧客検索

- [x] 日本語部分一致
- [x] 0件表示
- [x] クリア

## 受注検索

- [x] 件名検索
- [x] ステータス絞り込み
- [x] 顧客絞り込み
- [x] 複数条件AND
- [x] クリア

## 顧客CSV

- [x] CSV出力
- [x] UTF-8 BOM
- [x] 日本語正常
- [x] CSV取込
- [x] 不正データ拒否
- [x] エラー時に全件ロールバック

## 受注CSV / PDF

- [x] 受注CSV出力
- [x] PDF出力

## バックアップ

- [x] バックアップダウンロード
- [x] 非SQLiteファイル拒否
- [x] テーブル不整合拒否

## オフライン

- [x] 顧客検索
- [x] 受注検索
- [x] CSV出力
- [x] CSV取込

## 永続化

- [x] 再起動後データ保持

## 判定

Phase2 Windows: 合格
