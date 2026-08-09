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

#### CustomerSearchTest（5テスト）

| テスト名 | 検証内容 |
|---|---|
| search_returns_matching_customers | 検索文字を含む顧客だけ表示 |
| search_is_partial_match | 部分一致で検索できる |
| empty_query_returns_all_customers | q未指定で全件表示 |
| no_results_shows_appropriate_message | 該当なしメッセージ表示 |
| search_with_japanese_characters | 日本語顧客名で検索可能 |

#### CustomerCsvTest（6テスト）

| テスト名 | 検証内容 |
|---|---|
| csv_export_returns_csv_response | CSVレスポンスが返る |
| csv_export_contains_customer_data | 顧客データが含まれる |
| csv_export_has_bom | BOM付きUTF-8で出力される |
| csv_export_has_correct_headers | 正しいヘッダ行を持つ |
| csv_export_with_no_data_returns_header_only | データ0件でヘッダのみ |
| csv_filename_contains_date | ファイル名に日付を含む |

#### CustomerImportTest（12テスト）

| テスト名 | 検証内容 |
|---|---|
| import_page_is_accessible | 取込画面が200を返す |
| valid_csv_imports_customers | 正常CSVで顧客が登録される |
| utf8_bom_csv_is_accepted | BOM付きUTF-8を受け付ける |
| invalid_header_is_rejected | ヘッダ不正を拒否する |
| missing_name_is_rejected | 名前なし行を拒否する |
| invalid_email_is_rejected | 不正メールを拒否する |
| error_in_any_row_prevents_all_imports | エラー時に全件登録されない |
| empty_rows_are_skipped | 空行をスキップする |
| empty_csv_is_rejected | データ行なしCSVを拒否する |
| max_length_exceeded_is_rejected | 最大長超過を拒否する |
| file_is_required | ファイル未選択でエラー |
| csv_roundtrip | 出力→取込のラウンドトリップ |

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

#### WorkOrderSearchTest（7テスト）

| テスト名 | 検証内容 |
|---|---|
| search_by_title | 件名部分一致検索 |
| filter_by_status | ステータス絞り込み |
| filter_by_customer | 顧客絞り込み |
| combined_filters | 複数条件AND検索 |
| no_filters_returns_all | 条件なしで全件表示 |
| no_results_shows_message | 該当なしメッセージ表示 |
| invalid_status_is_ignored | 不正ステータス値を無視 |

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
php artisan test --filter=CustomerSearchTest

# カバレッジ付き（Xdebug必要）
php artisan test --coverage
```

## テスト総数

- テスト数: 58
- アサーション数: 158

#### CustomerCsvTest（6テスト）

| テスト名 | 検証内容 |
|---|---|
| csv_export_returns_csv_response | CSVレスポンスが返る |
| csv_export_contains_customer_data | 顧客データが含まれる |
| csv_export_has_bom | BOM付きUTF-8で出力される |
| csv_export_has_correct_headers | 正しいヘッダ行を持つ |
| csv_export_with_no_data_returns_header_only | データ0件でヘッダのみ |
| csv_filename_contains_date | ファイル名に日付を含む |

#### CustomerImportTest（12テスト）

| テスト名 | 検証内容 |
|---|---|
| import_page_is_accessible | 取込画面が200を返す |
| valid_csv_imports_customers | 正常CSVで顧客が登録される |
| utf8_bom_csv_is_accepted | BOM付きUTF-8を受け付ける |
| invalid_header_is_rejected | ヘッダ不正を拒否する |
| missing_name_is_rejected | 名前なし行を拒否する |
| invalid_email_is_rejected | 不正メールを拒否する |
| error_in_any_row_prevents_all_imports | エラー時に全件登録されない |
| empty_rows_are_skipped | 空行をスキップする |
| empty_csv_is_rejected | データ行なしCSVを拒否する |
| max_length_exceeded_is_rejected | 最大長超過を拒否する |
| file_is_required | ファイル未選択でエラー |
| csv_roundtrip | 出力→取込のラウンドトリップ |

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

#### WorkOrderSearchTest（7テスト）

| テスト名 | 検証内容 |
|---|---|
| search_by_title | 件名部分一致検索 |
| filter_by_status | ステータス絞り込み |
| filter_by_customer | 顧客絞り込み |
| combined_filters | 複数条件AND検索 |
| no_filters_returns_all | 条件なしで全件表示 |
| no_results_shows_message | 該当なしメッセージ表示 |
| invalid_status_is_ignored | 不正ステータス値を無視 |

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
php artisan test --filter=CustomerSearchTest

# カバレッジ付き（Xdebug必要）
php artisan test --coverage
```

## テスト総数

- テスト数: 58
- アサーション数: 158
