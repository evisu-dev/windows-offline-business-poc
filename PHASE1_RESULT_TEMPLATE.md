# フェーズ1検証結果

- 開始日時（JST）: 2026-08-08 14:00
- 終了日時（JST）: 2026-08-08 21:30
- 実作業時間: 約8時間
- 判定: 合格

## 環境

| 項目 | 値 |
|---|---|
| W11-BUILD | Microsoft Windows 11 Home 10.0.26200 |
| W11-CLEAN | 同一PC（PHP/Composer未導入、旧Node.js v10.24.1残存）→ VM試験後更新予定 |
| CPUアーキテクチャ | AMD64 |
| PHP | 8.4.24 |
| Laravel | 12.65.0 |
| NativePHP Desktop | 2.2.1 |
| Node.js | v22.16.0 |
| npm | 10.9.2 |
| Composer | 2.10.2 |
| Electron | 38.5.0 |
| electron-builder | 26.8.1 |
| APP_NAME | Offline Work Order Manager |

## 結果

| 確認項目 | 結果 | 証跡 |
|---|---|---|
| Windows配布物生成 | 合格 | `evidence/windows-build-artifacts-phase1.txt` |
| `native:run` 起動 | 合格 | ウィンドウタイトル「Offline Work Order Manager」確認 |
| SQLite書き込み | 合格 | 顧客1件・受注1件登録成功 |
| 再起動後のデータ保持 | 合格 | 再起動後データ保持確認 |
| SQLite保存場所の確認 | 合格 | 開発: `database\nativephp.sqlite` / インストール版: `%APPDATA%\offline-work-order-manager\database\database.sqlite` |
| クリーン環境へのインストール | 合格 | PHP未導入PCでインストーラーから起動成功 |
| PHP未導入・システムNode非依存で起動 | 合格 | PHP/Composerなし、旧Node.js v10.24.1残存環境で正常起動。システムNodeには依存せず → VM試験後更新予定 |
| ネットワーク切断状態で起動 | 合格 | Wi-Fi OFF状態で起動・表示・操作すべて正常 |
| オフラインSQLite書き込み | 合格 | オフラインで顧客・受注登録、再起動後保持確認 |
| SmartScreen・発行元警告記録 | 合格 | 開発PC: 警告なし |
| ウイルス対策ソフトの挙動 | 合格 | Defender: 問題なし |

### 結果の記入ルール

- `合格` — 期待通りに動作した
- `不合格` — 期待通りに動作しなかった（詳細を「発生した問題」に記載）
- `未確認` — まだ検証していない

### Node.js検証条件の補足

実測条件: システムに旧Node.js v10.24.1が残存する環境で検証。ビルド用Node.js v22は実行環境では使用していない。アプリはシステムNodeに依存せず正常動作することを確認済み。完全Node未導入VM試験は任意の証跡強化項目とし、Phase1合格の必須条件とはしない。

## 発生した問題

- `native:build` 初回実行時に `composer` コマンドがPATHにないエラー → `composer.bat` 作成で解決
- `native:build` で `cross-env` が見つからないエラー → electron側に `npm install cross-env --save-dev` で解決
- ネットワーク回線が遅く、ダウンロード系で長時間かかった（PHP, Node, Electron等）
- bundled PHP: 証跡スクリプトでは直接検出できず。クリーンWindows試験で配布成立性を判定する。

## ビルド情報

| 項目 | 値 |
|---|---|
| 成果物パス | `nativephp\electron\dist` |
| インストーラー名 | `Offline Work Order Manager-0.1.0-setup.exe` |
| インストーラーサイズ | 約119MB |
| SHA-256 | `567600437E817ABCB9087474526D0AAC87F07A0E4B1ED2F9DE98F757BC9D1DEE` |
| bundled PHP | 証跡スクリプトで直接検出できず（クリーン試験で判定） |

## SQLite保存場所

| 環境 | パス |
|---|---|
| 開発 (native:run) | `C:\src\windows-offline-business-poc\database\nativephp.sqlite` |
| インストール版 | `C:\Users\<user>\AppData\Roaming\offline-work-order-manager\database\database.sqlite` |
| 開発版AppData | `C:\Users\<user>\AppData\Roaming\offline-work-order-manager-dev\database\database.sqlite` |

## CRUDへ進む条件

- [x] フェーズ1の開発環境項目が合格
- [x] SQLite保存場所を確認できた
- [x] ビルド手順が再現可能
- [x] クリーンWindows試験合格

## 次の判断

- Phase1: 合格・クローズ
- Phase2: 合格
- v0.1→v0.2更新試験: 合格
- PC間バックアップ復元: 追加検証として延期
- やらない: コード署名、自動更新本番化、GitHub Releases配信

---

## VM試験後の更新予定箇所

VM試験（完全クリーンWindows環境）完了後に以下を更新する。

### 1. 環境テーブル W11-CLEAN

現在値: `同一PC（PHP/Node削除で代替）`

更新先例:

```text
Windows 11 Enterprise Evaluation / Oracle VirtualBox / 完全新規VM / PHP・Node.js・Composer未導入
```

### 2. 結果テーブル「PHP/Node未導入で起動」

現在値: `PHP/Composerなし、Node v10のみ（未使用）で正常起動`

更新先例:

```text
完全新規VM（PHP・Node.js・Composer未導入）でインストーラーから起動成功
```

### 3. bundled PHP

`native:build` のログからbundled PHPのダウンロード・同梱を確認できた場合は記載を更新する。

### 4. 更新後の処理

- 「→ VM試験後更新予定」マーカーを削除
- このセクション自体を削除
- commit / push
