# Windows業務アプリPoC — フェーズ1実行資料

作成日: 2026-08-06（JST）

## 入口

- Macで実装準備を始める: `MAC_SETUP.md`
- Windowsで正式ビルドを行う: 既存のPowerShellスクリプトを使用
- 結果記録: `PHASE1_RESULT_TEMPLATE.md`

## スクリプト

### macOS

- `scripts/preflight-macos.sh`
- `scripts/bootstrap-phase1-macos.sh`
- `scripts/collect-macos-evidence.sh`

### Windows

- `scripts/bootstrap-phase1.ps1`
- `scripts/collect-build-evidence.ps1`

## 制約

フェーズ1が合格するまで、CRUD、CSV、PDF、バックアップ、更新検証へ進まない。
