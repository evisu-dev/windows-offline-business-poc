# テスト仕様

## テスト環境

- フレームワーク: PHPUnit 11.x
- データベース: SQLite `:memory:`（RefreshDatabase trait使用）
- テストディレクトリ: `tests/Feature/`、`tests/Unit/`

## テスト一覧

### Feature Tests

#### CustomerCrudTest（7テスト）

| テスト名 | 検証内容 |
|---|---|
| customer_index_is_accessible | 顧客一覧が200を返す |
| customer_create_form_is_accessible | 登録フォームが200を返す |
| customer_can_be_stored | POSTで顧客がDBに保存される |
| customer_store_requires_name | 名前未入力でバリデーションエラー |
| customer_edit_form_is_accessible | 編集フォームが200を返す |
| customer_can_be_updated | PUTで顧客情報が更新される |
| customer_can_be_deleted | DELETEで顧客がDBから削除される |

#### WorkOrderCrudTest（8テスト）

| テスト名 | 検証内容 |
|---|---|
| work_order_index_is_accessible | 受注一覧が200を返す |
| work_order_create_form_is_accessible | 登録フォームが200を返す |
| work_order_can_be_stored | POSTで受注がDBに保存される |
| work_order_store_requires_title_and_customer | 必須項目未入力でエラー |
| work_order_edit_form_is_accessible | 編集フォームが200を返す |
| work_order_can_be_updated | PUTで受注情報が更新される |
| work_order_can_be_deleted | DELETEで受注がDBから削除される |
| deleting_customer_cascades_to_work_orders | 顧客削除時に受注もカスケード削除 |

#### WorkOrderExportTest（4テスト）

| テスト名 | 検証内容 |
|---|---|
| csv_export_returns_csv_file | CSV形式でダウンロードできる |
| csv_export_with_no_data_returns_header_only | データなしでもヘッダー行を返す |
| pdf_export_returns_pdf_file | PDFとしてダウンロードできる |
| pdf_export_with_invalid_id_returns_404 | 存在しないIDで404を返す |

#### BackupTest（5テスト）

| テスト名 | 検証内容 |
|---|---|
| backup_page_is_accessible | バックアップ画面が200を返す |
| backup_download_requires_file_based_db | DBファイルの存在に応じた動作 |
| restore_rejects_non_sqlite_file | 非SQLiteファイルを拒否する |
| restore_requires_file | ファイル未選択でエラー |
| restore_rejects_sqlite_without_required_tables | 必要テーブルがないSQLiteを拒否 |

#### PocCheckTest（2テスト）

| テスト名 | 検証内容 |
|---|---|
| dashboard_is_accessible | ダッシュボードが200を返す |
| system_page_is_accessible | システム情報が200を返す |

#### ExampleTest（1テスト）

| テスト名 | 検証内容 |
|---|---|
| the_application_returns_a_successful_response | トップページが200を返す |

### Unit Tests

#### ExampleTest（1テスト）

| テスト名 | 検証内容 |
|---|---|
| that_true_is_true | PHPUnit動作確認 |

## テスト実行コマンド

```bash
# 全テスト実行
php artisan test

# 特定のテストクラスのみ実行
php artisan test --filter=CustomerCrudTest

# カバレッジ付き（Xdebug必要）
php artisan test --coverage
```

## テスト総数

- テスト数: 28
- アサーション数: 57
