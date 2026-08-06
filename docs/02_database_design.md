# データベース設計

## データベースエンジン

SQLite（ファイルベース）

開発時パス: `database/database.sqlite`
NativePHP実行時: アプリケーションデータディレクトリ内に自動配置

## ER図

```
┌──────────────┐       ┌──────────────────┐
│  customers   │       │   work_orders    │
├──────────────┤       ├──────────────────┤
│ id (PK)      │←──┐   │ id (PK)          │
│ name         │   │   │ customer_id (FK) │───┘
│ phone        │   │   │ title            │
│ email        │       │ description      │
│ address      │       │ status           │
│ note         │       │ due_date         │
│ created_at   │       │ created_at       │
│ updated_at   │       │ updated_at       │
└──────────────┘       └──────────────────┘

┌──────────────┐
│  poc_checks  │  ※Phase1検証用
├──────────────┤
│ id (PK)      │
│ message      │
│ created_at   │
│ updated_at   │
└──────────────┘
```

## テーブル定義

### customers（顧客）

| カラム | 型 | NULL | 備考 |
|---|---|---|---|
| id | INTEGER | NO | 主キー（自動採番） |
| name | VARCHAR(255) | NO | 顧客名 |
| phone | VARCHAR(255) | YES | 電話番号 |
| email | VARCHAR(255) | YES | メールアドレス |
| address | TEXT | YES | 住所 |
| note | TEXT | YES | 備考 |
| created_at | TIMESTAMP | YES | 登録日時 |
| updated_at | TIMESTAMP | YES | 更新日時 |

### work_orders（受注／作業指示書）

| カラム | 型 | NULL | 備考 |
|---|---|---|---|
| id | INTEGER | NO | 主キー（自動採番） |
| customer_id | INTEGER | NO | 外部キー → customers.id（CASCADE DELETE） |
| title | VARCHAR(255) | NO | 件名 |
| description | TEXT | YES | 詳細 |
| status | VARCHAR(255) | NO | ステータス（デフォルト: 未着手） |
| due_date | DATE | YES | 納期 |
| created_at | TIMESTAMP | YES | 登録日時 |
| updated_at | TIMESTAMP | YES | 更新日時 |

### ステータス値

| 値 | 説明 |
|---|---|
| 未着手 | 登録直後のデフォルト状態 |
| 進行中 | 作業開始済み |
| 完了 | 作業完了 |
| キャンセル | 取り消し |

## リレーション

- `customers` 1 : N `work_orders`（顧客削除時は受注もカスケード削除）

## マイグレーションファイル

| ファイル | 内容 |
|---|---|
| `0001_01_01_000000_create_users_table.php` | Laravelデフォルト（未使用） |
| `0001_01_01_000001_create_cache_table.php` | Laravelデフォルト |
| `0001_01_01_000002_create_jobs_table.php` | Laravelデフォルト |
| `2026_08_06_085630_create_customers_table.php` | 顧客テーブル |
| `2026_08_06_085630_create_work_orders_table.php` | 受注テーブル |
| `2026_08_06_170418_create_poc_checks_table.php` | Phase1検証用 |
