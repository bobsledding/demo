# 開發環境說明

本專案已使用 Docker Compose 建立開發環境，包含以下服務：

- **workspace**：PHP 開發環境（含 Composer、Node.js 等工具）
- **nginx**：HTTP 伺服器
- **php-fpm**：PHP 執行環境

## 快速啟動

1. 進入 `./docker` 目錄
2. 執行以下指令啟動容器：

   ```bash
   docker-compose up -d
