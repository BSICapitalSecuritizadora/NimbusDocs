# Azure Infrastructure Setup Guide

This guide details how to set up the Azure resources required to host NimbusDocs and configured the GitHub Actions for automated deployment.

## 1. Prerequisites
- Azure CLI installed (`az`)
- GitHub CLI or access to repository settings

## 2. Resource Group
Create a resource group to organize all resources.
```bash
az group create --name nimbusdocs-rg --location brazilsouth
```

## 3. App Service Plan
Create a hosting plan. "B1" is the lowest cost basic tier with SSL support. For testing, "F1" (Free) is fine but has quotas.
```bash
az appservice plan create \
  --name nimbusdocs-plan \
  --resource-group nimbusdocs-rg \
  --sku B1 \
  --is-linux
```

## 4. Web App (App Service)
Create the actual web application container (PHP 8.2).
```bash
az webapp create \
  --name nimbusdocs-prod \
  --resource-group nimbusdocs-rg \
  --plan nimbusdocs-plan \
  --runtime "PHP:8.2"
```

## 5. Database (Azure Database for MySQL)
Create a flexible server instance.
```bash
az mysql flexible-server create \
  --name nimbusdocs-db \
  --resource-group nimbusdocs-rg \
  --location brazilsouth \
  --admin-user nimbusadmin \
  --admin-password "StrongPassword123!" \
  --sku-name Standard_B1ms \
  --tier Burstable \
  --version 8.0.21
```
*Note: Make sure to allow access from Azure services in the networking settings.*

## 6. Configure Environment Variables
Set the production environment variables in the App Service.
```bash
az webapp config appsettings set \
  --name nimbusdocs-prod \
  --resource-group nimbusdocs-rg \
  --settings \
  APP_ENV=production \
  APP_DEBUG=false \
  APP_URL=https://nimbusdocs-prod.azurewebsites.net \
  APP_SECRET="generate-32-char-random-string" \
  DB_CONNECTION=mysql \
  DB_HOST=nimbusdocs-db.mysql.database.azure.com \
  DB_PORT=3306 \
  DB_DATABASE=nimbusdocs \
  DB_USERNAME=nimbusadmin \
  DB_PASSWORD="StrongPassword123!"
```

## 7. GitHub Deployment Credentials
We use a **Publish Profile** for simplicity in this setup (or you can use Service Principal).

1. Get the publish profile XML:
   ```bash
   az webapp deployment list-publishing-profiles \
     --name nimbusdocs-prod \
     --resource-group nimbusdocs-rg \
     --xml
   ```
2. Copy the entire XML output.
3. Go to GitHub Repo > **Settings** > **Secrets and variables** > **Actions**.
4. Create a new secret named `AZURE_WEBAPP_PUBLISH_PROFILE`.
5. Paste the XML content.

## 8. Deployment
Now, pushing to `main` will trigger the `deploy.yml` workflow, which will:
1. Run tests (CI).
2. Zip the application.
3. Deploy to Azure using the Publish Profile.
4. Run migrations via SSH/Kudu (if configured).

## Troubleshooting
- **Logs**: View logs with `az webapp log tail --name nimbusdocs-prod --resource-group nimbusdocs-rg`.
- **SSH**: Access console via Azure Portal > Development Tools > SSH.
