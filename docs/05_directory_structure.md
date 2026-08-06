# ディレクトリ構成

## プロジェクトルート

```
windows-offline-business-poc/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── SetupPdfFont.php          # PDF用フォント導入コマンド
│   ├── Http/
│   │   └── Controllers/
│   │       ├── BackupController.php      # バックアップ・復元
│   │       ├── Controller.php            # ベースコントローラー
│   │       ├── CustomerController.php    # 顧客CRUD
│   │       ├── SystemController.php      # システム情報表示
│   │       └── WorkOrderController.php   # 受注CRUD + CSV/PDF
│   ├── Models/
│   │   ├── Customer.php                  # 顧客モデル
│   │   ├── User.php                      # Laravelデフォルト（未使用）
│   │   └── WorkOrder.php                 # 受注モデル
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── NativeAppServiceProvider.php  # NativePHPウィンドウ設定
│   └── Support/
│       └── helpers.php                   # 共通ヘルパー関数
├── config/
│   ├── dompdf.php                        # PDF生成設定
│   ├── nativephp.php                     # NativePHP設定
│   └── ...                               # Laravel標準設定
├── database/
│   ├── database.sqlite                   # 開発用SQLiteファイル
│   ├── migrations/                       # マイグレーションファイル
│   ├── factories/
│   └── seeders/
├── docs/                                 # 仕様書ドキュメント
├── evidence/                             # Phase1検証エビデンス
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php             # 共通レイアウト
│       ├── backup/
│       │   └── index.blade.php           # バックアップ画面
│       ├── customers/
│       │   ├── _form.blade.php           # 顧客フォーム部品
│       │   ├── create.blade.php          # 顧客登録
│       │   ├── edit.blade.php            # 顧客編集
│       │   └── index.blade.php           # 顧客一覧
│       ├── dashboard.blade.php           # ダッシュボード
│       ├── system/
│       │   └── index.blade.php           # システム情報
│       └── work_orders/
│           ├── _form.blade.php           # 受注フォーム部品
│           ├── create.blade.php          # 受注登録
│           ├── edit.blade.php            # 受注編集
│           ├── index.blade.php           # 受注一覧
│           └── pdf.blade.php             # PDF用テンプレート
├── routes/
│   └── web.php                           # 全ルート定義
├── scripts/                              # セットアップスクリプト
│   ├── bootstrap-phase1-macos.sh
│   ├── bootstrap-phase1.ps1
│   ├── collect-build-evidence.ps1
│   ├── collect-macos-evidence.sh
│   └── preflight-macos.sh
├── storage/
│   └── fonts/                            # PDF用フォント格納先
├── tests/
│   ├── Feature/
│   │   ├── BackupTest.php
│   │   ├── CustomerCrudTest.php
│   │   ├── ExampleTest.php
│   │   ├── PocCheckTest.php
│   │   ├── WorkOrderCrudTest.php
│   │   └── WorkOrderExportTest.php
│   ├── Unit/
│   │   └── ExampleTest.php
│   └── TestCase.php
├── .env.example                          # 環境変数テンプレート
├── composer.json
├── composer.lock
├── MAC_SETUP.md                          # Mac環境構築手順
├── PHASE1_RESULT_TEMPLATE.md             # Phase1検証結果記入用
└── README.md                             # プロジェクト概要
```

## 主要ファイルの役割

### コントローラー

| ファイル | 責務 |
|---|---|
| CustomerController | 顧客の一覧・登録・編集・削除 |
| WorkOrderController | 受注の一覧・登録・編集・削除・CSV出力・PDF出力 |
| BackupController | DBバックアップダウンロード・リストア |
| SystemController | バージョン・環境情報の表示 |

### モデル

| ファイル | テーブル | リレーション |
|---|---|---|
| Customer | customers | hasMany(WorkOrder) |
| WorkOrder | work_orders | belongsTo(Customer) |

### Artisanコマンド

| コマンド | 説明 |
|---|---|
| `php artisan pdf:setup-font` | IPAexゴシックフォントをダウンロードして配置 |
