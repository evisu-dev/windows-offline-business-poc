# ルート定義

## 全ルート一覧

| メソッド | パス | 名前 | コントローラー | 説明 |
|---|---|---|---|---|
| GET | `/` | - | Closure | ダッシュボード |
| GET | `/customers` | customers.index | CustomerController@index | 顧客一覧 |
| GET | `/customers/create` | customers.create | CustomerController@create | 顧客登録フォーム |
| POST | `/customers` | customers.store | CustomerController@store | 顧客登録処理 |
| GET | `/customers/{customer}/edit` | customers.edit | CustomerController@edit | 顧客編集フォーム |
| PUT/PATCH | `/customers/{customer}` | customers.update | CustomerController@update | 顧客更新処理 |
| DELETE | `/customers/{customer}` | customers.destroy | CustomerController@destroy | 顧客削除 |
| GET | `/work_orders` | work_orders.index | WorkOrderController@index | 受注一覧 |
| GET | `/work_orders/create` | work_orders.create | WorkOrderController@create | 受注登録フォーム |
| POST | `/work_orders` | work_orders.store | WorkOrderController@store | 受注登録処理 |
| GET | `/work_orders/{work_order}/edit` | work_orders.edit | WorkOrderController@edit | 受注編集フォーム |
| PUT/PATCH | `/work_orders/{work_order}` | work_orders.update | WorkOrderController@update | 受注更新処理 |
| DELETE | `/work_orders/{work_order}` | work_orders.destroy | WorkOrderController@destroy | 受注削除 |
| GET | `/work_orders/export/csv` | work_orders.export_csv | WorkOrderController@exportCsv | CSV出力 |
| GET | `/work_orders/{work_order}/pdf` | work_orders.export_pdf | WorkOrderController@exportPdf | PDF出力 |
| GET | `/backup` | backup.index | BackupController@index | バックアップ画面 |
| GET | `/backup/download` | backup.download | BackupController@download | DBダウンロード |
| POST | `/backup/restore` | backup.restore | BackupController@restore | DBリストア |
| GET | `/system` | system.index | SystemController@index | システム情報 |

## ルート定義ファイル

`routes/web.php`

## ミドルウェア

全ルートにLaravelデフォルトのWebミドルウェアグループが適用される:
- EncryptCookies
- AddQueuedCookiesToResponse
- StartSession
- ShareErrorsFromSession
- VerifyCsrfToken
- SubstituteBindings

認証ミドルウェアは未適用（単一ユーザー前提のオフラインアプリのため）。
