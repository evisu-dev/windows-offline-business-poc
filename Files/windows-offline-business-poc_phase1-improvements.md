# Windows業務アプリPoC — Phase1完了前の改善対応

作成日: 2026-08-08（JST）  
対象リポジトリ: `evisu-dev/windows-offline-business-poc`

## 1. 現在の状態

現時点では以下まで確認済み。

- Windows 11 Home / AMD64
- PHP 8.4.24
- Node.js 22.16.0
- npm 10.9.2
- Composer 2.10.2
- Laravel 12.65.0
- Laravelテスト: 28 tests / 57 assertions 全成功
- NativePHP Windows正式ビルド成功
- `Offline Work Order Manager-0.1.0-setup.exe` 生成
- `offline-work-order-manager.exe` 生成
- インストーラーSHA-256取得
- クリーンWindows試験チェックリスト作成済み

正式インストーラー:

```text
Offline Work Order Manager-0.1.0-setup.exe
```

SHA-256:

```text
567600437E817ABCB9087474526D0AAC87F07A0E4B1ED2F9DE98F757BC9D1DEE
```

ただし、Windows Phase1全体はまだ未完了。

---

# 2. 今回対応する改善点

Phase1正式判定前に以下を整理する。

1. 旧Windowsビルド成果物の削除
2. 正式ビルド証跡の再収集
3. bundled PHP確認結果の扱い整理
4. 重複した失敗ログの整理
5. Windowsテスト結果の文字化け改善
6. `PHASE1_RESULT_TEMPLATE.md` の途中結果反映
7. クリーンWindows試験の実施
8. READMEの現在地更新

---

# 3. 旧 `Laravel-*` ビルド成果物を削除する

現在の `nativephp\electron\dist` には新旧両方のインストーラーが残っている。

旧:

```text
Laravel-0.1.0-setup.exe
Laravel-0.1.0-setup.exe.blockmap
```

正式:

```text
Offline Work Order Manager-0.1.0-setup.exe
Offline Work Order Manager-0.1.0-setup.exe.blockmap
```

クリーンWindows試験や証跡で混同しないよう、旧成果物を削除する。

```powershell
Set-Location C:\src\windows-offline-business-poc

Remove-Item ".\nativephp\electron\dist\Laravel-0.1.0-setup.exe" -ErrorAction SilentlyContinue
Remove-Item ".\nativephp\electron\dist\Laravel-0.1.0-setup.exe.blockmap" -ErrorAction SilentlyContinue
```

削除後:

```powershell
Get-ChildItem .\nativephp\electron\dist -File |
    Select-Object Name, Length, LastWriteTime
```

確認:

- [ ] `Laravel-0.1.0-setup.exe` が存在しない
- [ ] 正式インストーラーが残っている
- [ ] `win-unpacked` が残っている

---

# 4. 正式ビルド証跡を再収集する

旧成果物削除後に再度実行する。

```powershell
.\scripts\collect-build-evidence.ps1
```

確認する証跡:

```text
evidence/windows-build-environment-phase1.txt
evidence/windows-composer-packages-phase1.txt
evidence/windows-php-modules-phase1.txt
evidence/windows-laravel-about-phase1.txt
evidence/windows-build-artifacts-phase1.txt
evidence/windows-build-hashes-phase1.txt
evidence/windows-bundled-php-phase1.txt
```

`windows-build-artifacts-phase1.txt` に旧 `Laravel-*` が残っていないことを確認する。

正式インストーラーのSHA-256も再確認する。

```powershell
Get-FileHash `
    ".\nativephp\electron\dist\Offline Work Order Manager-0.1.0-setup.exe" `
    -Algorithm SHA256
```

再ビルドした場合は値が変わる可能性があるため、最新の実測値を正式値とする。

---

# 5. bundled PHP確認結果の扱いを整理する

現在:

```text
Bundled PHP executable was not found in the expected paths.
```

となっている。

これは「PHPがアプリに含まれていない」と確定する証拠ではなく、`collect-build-evidence.ps1` が想定したパスでは `php.exe` を直接確認できなかった、という結果。

Phase1では内部パッケージ構造の完全解析より、

```text
PHP未導入Windowsでアプリが起動するか
```

を実機で証明することを優先する。

方針:

- [ ] `windows-bundled-php-phase1.txt` は削除しない
- [ ] 「直接検出できず」と記録する
- [ ] クリーンWindows試験でPHP未導入起動を確認する
- [ ] 起動成功なら配布要件上は合格とする

Phase1結果には以下のように記録する。

```text
bundled PHP:
証跡スクリプトでは直接検出できず。
PHP未導入クリーンWindowsでの起動試験により配布成立性を判定する。
```

---

# 6. 重複した失敗ビルドログを整理する

現在、同じ失敗ログが以下のように重複している可能性がある。

```text
evidence/build-log.txt
evidence/windows-build-log-failed-20260808.txt
```

正式な命名:

```text
evidence/windows-build-log-failed-20260808.txt
```

のみを残す。

ハッシュ確認:

```powershell
Get-FileHash .\evidence\build-log.txt
Get-FileHash .\evidence\windows-build-log-failed-20260808.txt
```

同一なら:

```powershell
Remove-Item .\evidence\build-log.txt
```

---

# 7. 成功ビルドログを残す

正式Windowsビルド成功ログが独立ファイルとしてない場合は、次回ビルド時に保存する。

```powershell
php artisan native:build *>&1 |
    Tee-Object -FilePath .\evidence\windows-build-log-success-phase1.txt
```

確認:

- [ ] コマンドが正常終了
- [ ] `Offline Work Order Manager-0.1.0-setup.exe` が生成
- [ ] 致命的エラーなし
- [ ] 失敗ログとは別ファイル

既存の失敗ログを成功ログへリネームしない。

---

# 8. Windowsテスト結果の文字化けを改善する

現在のWindowsテスト証跡には、本来のチェック記号が以下のように文字化けしている箇所がある。

```text
笨・
```

テスト自体は `28 passed / 57 assertions` で成功しているため、Phase1判定には影響しない。

公開用・長期保存用証跡としてはUTF-8出力を整える。

```powershell
$OutputEncoding = [Console]::OutputEncoding = [System.Text.Encoding]::UTF8

php artisan test --no-ansi |
    Out-File `
        -FilePath .\evidence\windows-test-result-phase1.txt `
        -Encoding utf8
```

確認:

```powershell
Get-Content .\evidence\windows-test-result-phase1.txt
```

---

# 9. `PHASE1_RESULT_TEMPLATE.md` に途中結果を反映する

正式ビルドまで完了しているため、全項目を `未確認` のままにしない。

環境例:

```markdown
| 項目 | 値 |
|---|---|
| W11-BUILD | Microsoft Windows 11 Home 10.0.26200 |
| W11-CLEAN | 未確認 |
| CPUアーキテクチャ | AMD64 |
| PHP | 8.4.24 |
| Laravel | 12.65.0 |
| NativePHP Desktop | 2.2.1 |
| Node.js | v22.16.0 |
| npm | 10.9.2 |
| Composer | 2.10.2 |
| APP_NAME | Offline Work Order Manager |
```

現時点で少なくとも以下は合格にできる。

```markdown
| Windows配布物生成 | 合格 | `evidence/windows-build-artifacts-phase1.txt` |
```

`native:run`、SQLite書き込み、再起動保持については、実施済みで証跡を残せる場合だけ合格へ変更する。

---

# 10. クリーンWindows試験を実施する

対象:

```text
evidence/windows-clean-install-checklist.md
```

正式インストーラー:

```text
Offline Work Order Manager-0.1.0-setup.exe
```

のみをクリーンWindowsへ渡す。

---

# 11. クリーンWindowsの前提条件

推奨:

```text
Windows 11 x64
PHP未導入
Node.js未導入
Composer未導入
```

確認:

```powershell
php -v
node --version
composer --version
```

期待は「コマンドが認識されない」状態。

---

# 12. インストール試験

確認:

- [ ] インストーラー起動
- [ ] インストール完了
- [ ] PHPを要求されない
- [ ] Node.jsを要求されない
- [ ] Composerを要求されない

これが成功すれば、bundled PHPを直接検出できなかった問題についても、実利用上の配布要件は成立したと判断できる。

---

# 13. SmartScreen / 発行元警告

確認:

- [ ] SmartScreen警告の有無
- [ ] 「WindowsによってPCが保護されました」の有無
- [ ] 「不明な発行元」の有無
- [ ] 続行可能か
- [ ] スクリーンショット保存

記録:

```text
SmartScreen:
発行元表示:
続行可否:
必要操作:
利用者への説明:
実案件上のリスク:
```

コード署名そのものはPhase1では行わない。

---

# 14. Windows Defender

確認:

- [ ] インストーラーが削除されない
- [ ] exeが隔離されない
- [ ] 起動がブロックされない
- [ ] SQLite書き込みが妨害されない

誤検知時:

```text
検知名:
対象ファイル:
再現手順:
回避策:
実案件への影響:
```

---

# 15. 初回起動・SQLite書き込み

ネットワーク接続中で起動する。

確認:

- [ ] `Offline Work Order Manager` が起動
- [ ] ダッシュボード正常表示
- [ ] CSS正常
- [ ] JavaScript正常
- [ ] 顧客登録
- [ ] 受注登録
- [ ] 正常終了

記録:

```text
SQLite保存場所:
SQLiteファイルサイズ:
```

---

# 16. 再起動後データ保持

確認:

- [ ] 顧客データ保持
- [ ] 受注データ保持
- [ ] 日本語保持
- [ ] 同じSQLiteファイルを使用
- [ ] DB破損なし

---

# 17. 完全オフライン試験

ネットワークを切断する。

例:

```text
Wi-Fi OFF
LAN切断
```

アプリを終了後、オフライン状態で再起動する。

確認:

- [ ] オフライン起動
- [ ] 外部通信待ちなし
- [ ] CSS表示
- [ ] JavaScript動作
- [ ] 既存データ表示

---

# 18. オフラインSQLite操作

ネットワーク切断状態で以下を行う。

- [ ] 顧客新規登録
- [ ] 顧客更新
- [ ] 受注新規登録
- [ ] 受注更新
- [ ] アプリ終了
- [ ] アプリ再起動
- [ ] 追加・更新データ保持

ここがPhase1の主要判定項目。

---

# 19. クリーンWindowsチェックリストを完成させる

対象:

```text
evidence/windows-clean-install-checklist.md
```

空欄を実測値で埋め、最後に:

```text
総合判定: 合格 / 不合格 / 保留
```

を記入する。

---

# 20. Phase1結果を正式確定する

対象:

```text
PHASE1_RESULT_TEMPLATE.md
```

記録する内容:

```text
開始日時
終了日時
実作業時間
W11-BUILD
W11-CLEAN
各ツールバージョン
SQLite保存場所
インストーラー名
インストーラーサイズ
SHA-256
SmartScreen
Defender
```

結果項目:

- [ ] Windows配布物生成
- [ ] `native:run` 起動
- [ ] SQLite書き込み
- [ ] 再起動後データ保持
- [ ] SQLite保存場所
- [ ] クリーン環境インストール
- [ ] PHP / Node.js未導入で起動
- [ ] オフライン起動
- [ ] オフラインSQLite書き込み
- [ ] SmartScreen記録
- [ ] Defender記録

すべて成立すれば:

```text
判定: 合格
```

とする。

---

# 21. READMEを現在地へ合わせる

Phase1判定後にREADMEも更新する。

現在のREADMEには初期方針として、

```text
フェーズ1が合格するまで、CRUD、CSV、PDF、バックアップ、更新検証へ進まない。
```

という記載が残っているが、実際にはCRUD等は先行実装済み。

例:

```markdown
## 現在の状態

- Laravel / NativePHP Windows正式ビルド成功
- Windowsインストーラー生成済み
- Laravel自動テスト成功
- CRUD / CSV / PDF / バックアップは先行実装済み
- Phase1正式判定はクリーンWindows・オフライン検証待ち
```

初期方針は「当初の検証順序」として残してもよい。

---

# 22. Gitへ保存する

改善対応後:

```powershell
git status
git diff
```

確認:

- `.env` が含まれていない
- `nativephp/` が含まれていない
- exeが含まれていない
- 不要な重複ログがない
- 証跡ファイルだけが追加・更新されている

コミット例:

```text
chore: finalize Windows Phase1 build evidence
```

クリーン試験完了後:

```text
test: record clean Windows Phase1 validation
```

---

# 23. 今やる順番

## 1. 証跡整理

- 旧 `Laravel-*` 成果物削除
- 証跡再収集
- 重複失敗ログ削除
- Windowsテストログ文字化け改善
- Phase1途中結果反映

## 2. クリーンWindows試験

- PHP / Node.js未導入確認
- インストール
- SmartScreen
- Defender
- SQLite
- 再起動保持
- オフライン操作

## 3. Phase1正式判定

- チェックリスト完成
- `PHASE1_RESULT_TEMPLATE.md` 完成
- README現在地更新
- commit / push

---

# 24. 今はやらない

Phase1確定前は以下を増やさない。

- 顧客検索
- 受注検索
- CSV追加実装
- v0.2 migration
- 自動更新
- コード署名本番導入
- GitHub Releases配信
- UI改善

---

# 25. 最終判断

現在のWindows正式ビルド自体は成立している。

残る最大の確認事項は、

```text
開発環境のないWindows PCへ配布し、
PHP / Node.jsなしで起動し、
インターネットなしでもSQLite業務データを
正常に登録・保持できるか
```

である。

このクリーンWindows試験が成功した時点で、Windows Phase1を正式に合格とする。
