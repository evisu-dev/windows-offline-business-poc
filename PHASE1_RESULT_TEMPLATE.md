# フェーズ1検証結果

- 開始日時（JST）:
- 終了日時（JST）:
- 実作業時間:
- 判定: 合格 / 不合格 / 保留

## 環境

| 項目 | 値 |
|---|---|
| Windows版 | |
| CPUアーキテクチャ | |
| PHP | |
| Laravel | |
| NativePHP Desktop | 2.2.1 |
| Node.js | |
| Composer | |
| Electron | |
| electron-builder | |
| APP_NAME | Offline Work Order Manager |

## 結果

| 確認項目 | 結果 | 証跡 |
|---|---|---|
| Windows配布物生成 | 未確認 | `evidence/windows-build-artifacts-phase1.txt` |
| `native:run` 起動 | 未確認 | |
| SQLite書き込み | 未確認 | |
| 再起動後のデータ保持 | 未確認 | |
| SQLite保存場所の確認 | 未確認 | |
| クリーン環境へのインストール | 未確認 | |
| PHP/Node未導入で起動 | 未確認 | |
| ネットワーク切断状態で起動 | 未確認 | |
| オフラインSQLite書き込み | 未確認 | |
| SmartScreen・発行元警告記録 | 未確認 | |
| ウイルス対策ソフトの挙動 | 未確認 | |

### 結果の記入ルール

- `合格` — 期待通りに動作した
- `不合格` — 期待通りに動作しなかった（詳細を「発生した問題」に記載）
- `未確認` — まだ検証していない

## 発生した問題

- 

## ビルド情報

| 項目 | 値 |
|---|---|
| 成果物パス | `nativephp\electron\dist` |
| インストーラー名 | |
| インストーラーサイズ | |
| SHA-256 | |
| bundled PHP | |

## CRUDへ進む条件

- [ ] フェーズ1の全項目が合格
- [ ] SQLite保存場所を確認できた
- [ ] ビルド手順が再現可能
- [ ] 2時間上限を超えていない

## 次の判断

- 今やる:
- 後でやる:
- やらない:
