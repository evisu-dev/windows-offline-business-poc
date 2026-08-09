# v0.1.0 → v0.2.0 更新検証

実行日: ____-__-__
PC: VirtualBox Windows 11 Enterprise Evaluation VM
v0.1 installer: Offline Work Order Manager-0.1.0-setup.exe
v0.1 SHA-256: 567600437E817ABCB9087474526D0AAC87F07A0E4B1ED2F9DE98F757BC9D1DEE
v0.2 installer: Offline Work Order Manager-0.2.0-setup.exe
v0.2 SHA-256: ____

## 更新前（v0.1環境）

- [ ] v0.1インストール済み
- [ ] v0.1アプリ起動確認

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

- [ ] 顧客登録完了
- [ ] 受注登録完了
- [ ] データ件数記録: 顧客 __件 / 受注 __件

### VMスナップショット

- [ ] スナップショット作成: `01-v0.1-with-data`

## v0.2ビルド（ホスト側）

- [ ] 検証用migration追加
- [ ] NATIVEPHP_APP_VERSION=0.2.0
- [ ] php artisan test: __ tests / __ assertions PASSED
- [ ] npm run build: 成功
- [ ] php artisan native:build: 成功
- [ ] installer名: Offline Work Order Manager-0.2.0-setup.exe
- [ ] SHA-256: ____

## v0.2上書きインストール

- [ ] v0.2インストーラーをVMへ転送
- [ ] SHA-256一致確認
- [ ] 上書きインストール実行
- [ ] インストール成功
- [ ] 既存AppDataが削除されていない

## v0.2起動

- [ ] アプリ起動成功
- [ ] バージョン表示: 0.2.0（システム情報画面）

## 既存データ確認

- [ ] v0.1保持確認顧客が存在
- [ ] v0.1保持確認受注が存在
- [ ] 日本語正常
- [ ] 顧客検索可能
- [ ] 受注検索可能
- [ ] CSV出力可能
- [ ] データ件数変化なし: 顧客 __件 / 受注 __件

## migration適用確認

確認方法: ____

- [ ] 新規migrationで追加されたテーブル/カラムが存在
- [ ] 既存テーブル（customers, work_orders）が正常

## v0.2新規データ作成

- [ ] v0.2新規顧客登録
- [ ] v0.2新規受注登録
- [ ] 登録成功

## 再起動確認

- [ ] VM Windows再起動
- [ ] アプリ起動
- [ ] v0.1既存データ保持
- [ ] v0.2新規データ保持

## オフライン確認

ネットワーク切断方法: VM仮想NIC無効化

- [ ] アプリ起動
- [ ] v0.1既存データ表示
- [ ] v0.2新規データ表示
- [ ] 検索動作
- [ ] CSV出力
- [ ] SQLite更新

## 判定

v0.1.0 → v0.2.0 更新: ____

備考:
- ____
