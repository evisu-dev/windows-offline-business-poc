# Windows業務アプリPoC — Windows環境の残タスク
## 1台のPCだけで完結する検証方法

作成日: 2026-08-09（JST）  
対象リポジトリ: `evisu-dev/windows-offline-business-poc`

---

# 1. 現在の判定

Windows Phase1は**機能面では合格**として扱ってよい状態。

現在確認済み:

- Windows正式ビルド成功
- `Offline Work Order Manager-0.1.0-setup.exe` 生成
- Laravelテスト 28件 / 57 assertions 成功
- SQLite書き込み成功
- 再起動後データ保持
- オフライン起動・SQLite操作
- PHPを削除した環境で起動
- Windows Defenderで問題なし

ただし、公開用・技術実績用の証跡としては以下が残っている。

1. 完全なクリーンWindows環境で再確認
2. `windows-clean-install-checklist.md` の実績記入
3. インストール版SQLiteの実保存場所を特定
4. Node.jsも完全未導入の状態で起動確認
5. Phase1結果表を厳密なクリーン環境実績へ更新
6. 必要なら正式ビルド成功ログを保存

これらは**Phase1をやり直す作業ではなく、Phase1合格の証跡を強化する作業**。

---

# 2. 1台のPCだけで全て対応する構成

現在のホストOSは Windows 11 Home。

Windows 11 Homeでは、Microsoft公式の以下の機能は利用できない。

- Windows Sandbox
- Client Hyper-V

そのため、現在のPCをWindows 11 Homeのまま使用する場合は、

```text
物理Windows PC
├─ ホストWindows
│   └─ 開発・ビルド環境
│
└─ ローカル仮想マシン
    └─ 完全クリーンWindows検証環境
```

という構成を推奨する。

---

# 3. 推奨する1台構成

## ホスト

現在使っているWindows PC。

役割:

- Git
- PHP 8.4
- Composer
- Node.js 22
- Laravel
- NativePHP
- Windowsインストーラー生成
- Git commit / push

現在のプロジェクト:

```text
C:\src\windows-offline-business-poc
```

## ゲストVM

同じ物理PC上に新しく作るWindows仮想マシン。

役割:

- クリーンインストール試験
- PHP未導入試験
- Node.js未導入試験
- Composer未導入試験
- オフライン試験
- SmartScreen確認
- Defender確認
- インストール版SQLiteパス確認

---

# 4. Windows 11 Homeでの推奨方法

Windows 11 HomeではWindows SandboxとHyper-Vが公式サポート外のため、ローカルVMソフトを使用する。

例:

```text
Oracle VirtualBox
```

VirtualBoxはWindowsホスト上でWindows 11ゲストを作成でき、現行版では以下のVM機能を設定できる。

- UEFI
- Secure Boot
- TPM 2.0

Windows 11ゲストの最低構成目安:

```text
CPU: 2 vCPU以上
RAM: 4GB以上
ディスク: 64GB以上
UEFI
Secure Boot
TPM 2.0
```

ホストPCに余裕がある場合:

```text
CPU: 2〜4 vCPU
RAM: 6〜8GB
ディスク: 80GB程度（可変）
```

程度でよい。

---

# 5. Windows評価版を使う

クリーン試験専用VMなので、Microsoftが提供する

```text
Windows 11 Enterprise Evaluation
```

を利用できる。

Microsoft Evaluation CenterではWindows 11 Enterpriseの90日評価版ISOが提供されている。

このPoCでは、

```text
本番PCとして使う
```

のではなく、

```text
アプリのインストール・互換性・オフライン動作を検証する
```

用途なので、検証用VMとして使用する。

---

# 6. 1台PC構成の全体フロー

## ホスト側

```text
Git pull
↓
Laravel test
↓
Vite build
↓
NativePHP build
↓
Windows installer生成
↓
SHA-256取得
```

## VM側

```text
新品Windows起動
↓
PHPなし確認
↓
Node.jsなし確認
↓
Composerなし確認
↓
installer導入
↓
アプリ起動
↓
SQLite登録
↓
再起動後保持
↓
ネットワーク切断
↓
オフライン起動
↓
オフラインSQLite操作
↓
SmartScreen / Defender確認
↓
SQLite実保存場所確認
```

## 最後にホスト側

```text
証跡更新
↓
PHASE1_RESULT_TEMPLATE.md更新
↓
README必要箇所更新
↓
git commit
↓
git push
```

この方法なら、**物理PCは1台だけでよい**。

---

# 7. 最初にホストPCの仮想化対応を確認する

PowerShellまたはコマンドプロンプト:

```powershell
systeminfo
```

出力末尾付近の仮想化関連項目を確認する。

またはタスクマネージャー:

```text
タスク マネージャー
→ パフォーマンス
→ CPU
→ 仮想化
```

確認:

```text
仮想化: 有効
```

になっていること。

無効の場合はBIOS / UEFIで、

```text
Intel VT-x
AMD-V / SVM
```

等を有効化する。

---

# 8. VMを作成する

VirtualBox等でWindows 11 VMを作る。

推奨例:

```text
名前:
Evisu-Works-Clean-Windows

OS:
Windows 11 64-bit

CPU:
2〜4

RAM:
6GB程度

ディスク:
80GB 可変

Firmware:
UEFI

Secure Boot:
有効

TPM:
2.0
```

Windows 11 Enterprise Evaluation ISOを仮想DVDとして指定する。

---

# 9. VM初期状態を保存する

Windowsセットアップ完了後、アプリを何もインストールしていない段階でスナップショットを作る。

スナップショット名例:

```text
00-clean-windows
```

この状態を保存しておけば、

```text
インストール試験
↓
VMを汚す
↓
スナップショットへ戻す
↓
再試験
```

が可能。

これにより、2台目のPCを用意する必要がなくなる。

---

# 10. クリーン環境を確認する

VM内でPowerShell:

```powershell
php -v
node --version
composer --version
```

期待:

```text
php : 認識されません
node : 認識されません
composer : 認識されません
```

ここで初めて、

```text
PHP完全未導入
Node.js完全未導入
Composer完全未導入
```

の証跡が取れる。

現在のPhase1ではNode.js v10が残った状態だったため、このVM試験でその曖昧さを解消する。

---

# 11. インストーラーをVMへ渡す

使用するファイル:

```text
Offline Work Order Manager-0.1.0-setup.exe
```

旧:

```text
Laravel-0.1.0-setup.exe
```

は使用しない。

VMへコピーする方法は任意。

例:

- 一時的にネットワーク経由で取得
- VM共有フォルダ
- USBメモリ
- 仮想メディア経由

コピー完了後にネットワークを切ってからオフライン試験を行う。

---

# 12. SHA-256を確認する

VMへコピーしたファイルがホストと同じか確認する。

VM PowerShell:

```powershell
Get-FileHash `
    ".\Offline Work Order Manager-0.1.0-setup.exe" `
    -Algorithm SHA256
```

現在の正式証跡値:

```text
567600437E817ABCB9087474526D0AAC87F07A0E4B1ED2F9DE98F757BC9D1DEE
```

再ビルドしている場合は、最新のホスト側証跡値を使用する。

確認:

- [ ] ホストSHA-256と一致

---

# 13. クリーンインストール試験

VMでインストーラーを実行する。

確認:

- [ ] インストーラー起動
- [ ] インストール完了
- [ ] PHPインストール要求なし
- [ ] Node.jsインストール要求なし
- [ ] Composerインストール要求なし
- [ ] アプリ起動

ここが成功すれば、

```text
NativePHPアプリはシステムPHP / Node.jsへ依存せず配布できる
```

というPhase1の重要条件を、より厳密に証明できる。

---

# 14. `windows-clean-install-checklist.md` を実績で埋める

現在のファイル:

```text
evidence/windows-clean-install-checklist.md
```

はテンプレート状態。

VM試験をしながら、

```markdown
- [ ]
```

を、

```markdown
- [x]
```

へ更新する。

必ず以下も記入する。

```text
実行日
試験環境
SmartScreen
発行元警告
SQLite保存場所
総合判定
備考
```

試験環境例:

```text
Oracle VirtualBox
Windows 11 Enterprise Evaluation
完全新規VM
PHP未導入
Node.js未導入
Composer未導入
```

---

# 15. インストール版SQLiteの実保存場所を特定する

これは現在残っている重要な証跡。

開発時は:

```text
database\nativephp.sqlite
```

だが、インストール版は別。

NativePHP Desktop v2では、本番ビルドのSQLiteはユーザーのappdata配下に保存される。

現在のアプリID:

```text
jp.evisuworks.offlineworkorderpoc
```

なので、Windowsでは次のような場所が候補。

```text
%APPDATA%\jp.evisuworks.offlineworkorderpoc\database\database.sqlite
```

実際のファイルを確認して正式なパスを記録する。

---

# 16. SQLiteをPowerShellで探す

VM内:

```powershell
$env:APPDATA
```

次に:

```powershell
Get-ChildItem $env:APPDATA -Directory |
    Where-Object {
        $_.Name -match 'evisu|offline|work|jp\.evisuworks'
    }
```

SQLite検索:

```powershell
Get-ChildItem $env:APPDATA `
    -Recurse `
    -Filter *.sqlite `
    -ErrorAction SilentlyContinue |
    Select-Object FullName, Length, LastWriteTime
```

見つかったファイルのフルパスを記録する。

---

# 17. アプリ自身でもDBパスを確認する

現在のPoCにはシステム情報画面があるため、インストール版で表示されるDB pathも確認する。

確認:

```text
システム情報画面のDB Path
```

と、

```text
PowerShellで見つけたSQLiteパス
```

が一致すること。

記録例:

```text
Installed SQLite path:
C:\Users\<user>\AppData\Roaming\...\database\database.sqlite
```

---

# 18. SQLite永続化試験

VMのインストール版で:

```text
顧客A
受注A
```

を登録する。

確認:

- [ ] 登録成功
- [ ] SQLiteファイル更新日時が変わる
- [ ] アプリ終了
- [ ] アプリ再起動
- [ ] 顧客Aが残る
- [ ] 受注Aが残る

---

# 19. 完全オフライン試験

VMの仮想NICを無効化する。

重要:

```text
ホストPCのWi-Fiを切る必要はない
```

VMだけネットワーク切断すればよい。

これが1台PC方式の利点。

VM設定で:

```text
Network Adapter
→ Disabled
```

またはゲストWindows側でネットワークアダプターを無効化する。

---

# 20. オフライン起動

VMのネットワークを切ったまま:

```text
アプリ終了
↓
再起動
```

確認:

- [ ] アプリ起動
- [ ] CSS表示
- [ ] JavaScript動作
- [ ] 既存データ表示
- [ ] 外部通信待ちなし

---

# 21. オフラインSQLite更新

ネットワークなしで:

- [ ] 顧客追加
- [ ] 顧客更新
- [ ] 受注追加
- [ ] 受注更新
- [ ] アプリ終了
- [ ] 再起動
- [ ] データ保持

これでオフライン業務アプリとしての成立性を再確認する。

---

# 22. SmartScreen確認

VMはクリーン環境なので、ホストより実利用環境に近い。

インストーラー起動時に確認:

```text
SmartScreen警告
不明な発行元
WindowsによってPCが保護されました
```

結果をチェックリストへ記録する。

注意:

SmartScreenはファイルの取得経路やレピュテーションによって挙動が変わるため、

```text
警告なし = 将来すべてのPCで警告なし
```

とは判断しない。

Phase1では実測結果を記録できれば十分。

---

# 23. Windows Defender確認

Windows Defenderを通常設定のまま使用する。

確認:

- [ ] installer削除なし
- [ ] exe隔離なし
- [ ] 起動ブロックなし
- [ ] SQLite書き込み阻害なし

---

# 24. VMを再起動して再確認する

Windows自体を再起動する。

```text
VM Windows再起動
↓
アプリ起動
```

確認:

- [ ] アプリ正常起動
- [ ] SQLiteデータ保持
- [ ] オフラインでも起動可能

---

# 25. 必要ならスナップショットから再試験する

試験後:

```text
00-clean-windows
```

へ戻す。

再び:

```text
PHPなし
Nodeなし
Composerなし
```

を確認。

同じインストーラーを再インストールして同じ結果になることを確認すれば、再現性も高くなる。

これは必須ではないが、公開実績としては有効。

---

# 26. Phase1結果表を更新する

対象:

```text
PHASE1_RESULT_TEMPLATE.md
```

現在:

```text
W11-CLEAN:
同一PC（PHP/Node削除で代替）
```

を、VM試験後は例えば:

```text
W11-CLEAN:
Windows 11 Enterprise Evaluation
Oracle VirtualBox上の完全新規VM
PHP / Node.js / Composer未導入
```

へ更新する。

---

# 27. PHP/Node未導入項目を更新する

現在は:

```text
PHP/Composerなし、Node v10のみ
```

という証跡。

VM試験成功後:

```text
PHP: 未導入
Node.js: 未導入
Composer: 未導入
```

へ更新できる。

これによりPhase1の最大の証跡上の曖昧さがなくなる。

---

# 28. SQLite保存場所を更新する

現在はdev時の:

```text
database\nativephp.sqlite
```

が記載されている。

VM試験後は:

```text
開発:
database\nativephp.sqlite

インストール版:
<実測したAppData配下のdatabase.sqlite>
```

の両方を記録する。

---

# 29. クリーン試験チェックリスト完成

対象:

```text
evidence/windows-clean-install-checklist.md
```

完成条件:

- [x] 実行日
- [x] VM環境
- [x] PHP未導入
- [x] Node未導入
- [x] Composer未導入
- [x] インストール
- [x] SmartScreen
- [x] Defender
- [x] 初回起動
- [x] SQLite登録
- [x] 再起動保持
- [x] オフライン起動
- [x] オフライン更新
- [x] SQLiteフルパス
- [x] 総合判定

---

# 30. 正式ビルド成功ログ

現時点で必須ではないが、公開証跡を強くするなら残す。

ホスト側:

```powershell
php artisan native:build *>&1 |
    Tee-Object `
        -FilePath .\evidence\windows-build-log-success-phase1.txt
```

確認:

- [ ] build成功
- [ ] installer生成
- [ ] 致命的エラーなし

---

# 31. 証跡をGitへ反映する

VM試験結果をホスト側リポジトリへ記入する。

更新対象:

```text
PHASE1_RESULT_TEMPLATE.md
README.md
evidence/windows-clean-install-checklist.md
```

必要なら:

```text
evidence/windows-build-log-success-phase1.txt
```

も追加。

---

# 32. Git確認

```powershell
git status
git diff
```

含めないもの:

```text
.env
nativephp/
*.exe
VMファイル
Windows ISO
VirtualBox VMイメージ
```

---

# 33. commit

例:

```powershell
git add PHASE1_RESULT_TEMPLATE.md
git add README.md
git add evidence/windows-clean-install-checklist.md

git commit -m "test: confirm Phase1 in clean Windows VM"
git push
```

---

# 34. 1台PCでの最終完成形

```text
物理Windows PC
│
├─ Host Windows 11 Home
│  ├─ PHP 8.4
│  ├─ Composer
│  ├─ Node.js 22
│  ├─ Laravel
│  ├─ NativePHP
│  └─ installer生成
│
└─ VirtualBox
   └─ Windows 11 Enterprise Evaluation VM
      ├─ PHPなし
      ├─ Nodeなし
      ├─ Composerなし
      ├─ installer試験
      ├─ SQLite永続化
      ├─ オフライン試験
      ├─ SmartScreen
      └─ Defender
```

この構成なら、**2台目の物理PCは不要**。

---

# 35. Windows 11 Proへ上げる場合の代替案

将来ホストWindowsをWindows 11 Proへ変更した場合は、

```text
Windows Sandbox
```

を使用する方法がさらに簡単。

Windows Sandboxは毎回クリーンなWindows環境を作れるため、

```text
installer
↓
Sandbox起動
↓
インストール
↓
動作試験
↓
Sandbox終了
↓
完全初期化
```

という試験が可能。

ただし現在のWindows 11 Homeでは公式サポート対象外なので、**今回のPoCのためだけにProへ変更する必要はない**。

---

# 36. 最小対応で済ませる場合

Phase1はすでに機能面で合格している。

したがって公開前の最低限対応なら、以下の3件だけでよい。

## 1

同一PC上に完全新規Windows VMを1つ作る。

## 2

VMで:

```text
PHPなし
Nodeなし
Composerなし
installer起動
オフラインSQLite
```

を再確認。

## 3

以下を完成:

```text
evidence/windows-clean-install-checklist.md
PHASE1_RESULT_TEMPLATE.md
```

これでPhase1の証跡は十分強くなる。

---

# 37. Windows側の残タスク優先順位

## 今やる

1. VirtualBox等でクリーンWindows VM作成
2. PHP / Node / Composer完全未導入試験
3. インストール版SQLite実パス確認

## 同時に記録

- SmartScreen
- Defender
- 再起動保持
- オフライン操作
- `windows-clean-install-checklist.md`

## 最後

- `PHASE1_RESULT_TEMPLATE.md` 更新
- README微修正
- commit / push

---

# 38. Phase2との関係

今回の残タスクはPhase1の**証跡強化**。

Phase2の開発を止める重大な技術問題ではない。

ただし、Evisu Worksの公開実績として使う前には、

```text
完全クリーンVM試験
インストール版SQLiteパス
```

の2点を回収しておくことを推奨する。

---

# 39. 外部仕様上の根拠

本手順では以下の公式仕様を前提としている。

- Microsoft Learn「Windows サンドボックス」
  - Windows SandboxはPro / Enterprise / Educationで利用可能
  - Windows Homeは非対応
- Microsoft Learn「Hyper-V のシステム要件」
  - Client Hyper-VはWindows 11 Pro / Enterpriseが対象
- Microsoft Learn「Windows 11 の要件」
  - Windows 11 VMは2 vCPU以上、4GB RAM以上、64GBストレージ以上が基本要件
- Microsoft Evaluation Center「Windows 11 Enterprise」
  - 90日評価版ISOを提供
- Oracle VirtualBox User Guide
  - UEFI / Secure Boot / TPM設定をサポート
- NativePHP Desktop v2「Databases / Building / Files」
  - 開発時は `nativephp.sqlite`
  - 本番版はユーザーappdata配下にSQLite DBを作成
  - 本番初回起動時に `{appdata}/database/database.sqlite` を作成
  - Windowsのappdataは `%APPDATA%` 配下

---

# 40. 最終目標

1台の物理PCだけで、

```text
開発
↓
Windowsビルド
↓
インストーラー生成
↓
完全クリーン環境
↓
PHP / Nodeなし起動
↓
SQLite永続化
↓
完全オフライン動作
↓
証跡保存
```

まで完結させる。

この検証が完了すれば、

```text
Laravel / NativePHPで作成したWindows業務アプリを
開発環境のないWindows PCへ配布し、
インターネットなしでもSQLiteデータを扱える
```

というPhase1の技術的成立を、再現性のある形で示せる。
