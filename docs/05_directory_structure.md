# ディレクトリ構成

## プロジェクトルート

```
windows-offline-business-poc/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── SetupPdfFont.php              # PDF用フォント導入コマンド
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── BackupController.php          # バックアップ・復元
│   │   │   ├── Controller.php                # ベースコントローラー
│   │   │   ├── CustomerController.php        # 顧客CRUD・検索
│   │   │   ├── CustomerExportController.php  # 顧客CSV出力
│   │   │   ├── CustomerImportController.php  # 顧客CSV取込
│   │   │   ├── DashboardController.php       # ダッシュボード
│   │   │   ├── SystemController.php          # システム情報表示
│   │   │   ├── WorkOrderController.php       # 受注CRUD・検索
│   │   │   └── WorkOrderExportController.php # 受注CSV/PDF出力
│   │   └── Requests/
│   │       ├── StoreCustomerRequest.php      # 顧客バリデーション
│   │       └── StoreWorkOrderRequest.php     # 受注バリデーション
│   ├── Models/
│   │   ├── Customer.php                      # 顧客モデル
│   │   ├── User.php                          # Laravelデフォルト（未使用）
│   │   └── WorkOrder.php                     # 受注モデル（STATUSES定数含む）
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── NativeAppServiceProvider.php      # NativePHPウィンドウ設定
│   └── Support/
│       └── helpers.php                       # 共通ヘルパー関数（format_bytes）
├── config/
│   ├── dompdf.php                            # PDF生成設定
│   ├── nativephp.php                         # NativePHP設定
│   └── ...                                   # Laravel標準設定
├── database/
│   ├── database.sqlite                       # 開発用SQLiteファイル
│   ├── migrations/                           # マイグレーションファイル
│   ├── factories/
│   └── seeders/
├── docs/                                     # 仕様書ドキュメント
├── evidence/                                 # 検証エビデンス
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php                 # 共通レイアウト（ナビゲーション）
│       ├── backup/
│       │   └── index.blade.php               # バックアップ画面
│       ├── customers/
│       │   ├── _form.blade.php               # 顧客フォーム部品
│       │   ├── create.blade.php              # 顧客登録
│       │   ├── edit.blade.php                # 顧客編集
│       │   ├── import.blade.php              # 顧客CSV取込
│       │   └── index.blade.php               # 顧客一覧（検索フォーム付き）
│       ├── dashboard.blade.php               # ダッシュボード
│       ├── system/
│       │   └── index.blade.php               # システム情報
│       └── work_orders/
│           ├── _form.blade.php               # 受注フォーム部品
│           ├── create.blade.php              # 受注登録
│           ├── edit.blade.php                # 受注編集
│           ├── index.blade.php               # 受注一覧（検索フォーム付き）
│           └── pdf.blade.php                 # PDF用テンプレート
├── routes/
│   └── web.php                               # 全ルート定義（22ルート）
├── scripts/
│   ├── collect-build-evidence.ps1            # Windowsビルド証跡収集
│   ├── collect-macos-evidence.sh             # Mac証跡収集
│   ├── preflight-macos.sh                    # Mac前提条件チェック
│   └── setup-windows.ps1                     # Windows環境セットアップ
├── storage/
│   └── fonts/                                # PDF用フォント格納先
├── tests/
│   ├── Feature/
│   │   ├── BackupTest.php                    # バックアップ・復元テスト
│   │   ├── CustomerCrudTest.php              # 顧客CRUDテスト
│   │   ├── CustomerCsvTest.php               # 顧客CSV出力テスト
│   │   ├── CustomerImportTest.php            # 顧客CSV取込テスト
│   │   ├── CustomerSearchTest.php            # 顧客検索テスト
│   │   ├── ExampleTest.php                   # Laravelデフォルト
│   │   ├── PocCheckTest.php                  # ダッシュボード・システム情報テスト
│   │   ├── WorkOrderCrudTest.php             # 受注CRUDテスト
│   │   ├── WorkOrderExportTest.php           # 受注CSV/PDFテスト
│   │   └── WorkOrderSearchTest.php           # 受注検索テスト
│   ├── Unit/
│   │   └── ExampleTest.php
│   └── TestCase.php
├── .env.example                              # 環境変数テンプレート
├── composer.json                             # PHP ^8.4
├── composer.lock
├── MAC_SETUP.md                              # Mac環境構築手順
├── PHASE1_RESULT_TEMPLATE.md                 # Phase1検証結果
└── README.md                                 # プロジェクト概要
```

## 主要ファイルの役割

### コントローラー

| ファイル | 責務 |
|---|---|
| DashboardController | トップページ（顧客数・受注数の集計表示） |
| CustomerController | 顧客の一覧（検索付き）・登録・編集・削除 |
| CustomerExportController | 顧客CSV出力 |
| CustomerImportController | 顧客CSV取込（画面表示・バリデーション・登録） |
| WorkOrderController | 受注の一覧（検索・絞り込み付き）・登録・編集・削除 |
| WorkOrderExportController | 受注CSV出力・作業指示書PDF出力 |
| BackupController | DBバックアップダウンロード・リストア |
| SystemController | バージョン・環境情報の表示 |

### FormRequest

| ファイル | 用途 |
|---|---|
| StoreCustomerRequest | 顧客登録・更新のバリデーション |
| StoreWorkOrderRequest | 受注登録・更新のバリデーション（ステータス `Rule::in` 含む） |

### モデル

| ファイル | テーブル | リレーション | 備考 |
|---|---|---|---|
| Customer | customers | hasMany(WorkOrder) | |
| WorkOrder | work_orders | belongsTo(Customer) | `STATUSES` 定数を保持 |

### Artisanコマンド

| コマンド | 説明 |
|---|---|
| `php artisan pdf:setup-font` | IPAexゴシックフォントをダウンロードして配置 |
