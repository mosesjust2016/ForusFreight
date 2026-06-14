# FTP Deployment Guide

**Updated**: June 13, 2026  
**Deployment Method**: GitHub Actions → FTP (milanmk/actions-file-deployer)

---

## Overview

The deployment pipeline has been updated to use FTP for the cPanel shared hosting and Docker for the VM.

### Deployment Architecture:

| Component | Deployment Method | Location | Purpose |
|-----------|-------------------|----------|---------|
| **website/** | FTP (cPanel) | `/public_html` | Laravel web application |
| **Ecommerce/** | Docker | VM 46.62.161.138 | API, Admin Portal, Storefront |
| **TerraForm/** | Manual | Git | Infrastructure as Code |

### FTP Server Details:
- **FTP Server**: ftp.forusfl.co.zm
- **FTP Username**: update9240@forusfl.co.zm
- **FTP Port**: 21 (Explicit FTPS)
- **Target Path**: `/public_html` (web root)

---

## GitHub Secrets Required

Add these secrets to your GitHub repository:

### Settings → Secrets and variables → Actions

#### For cPanel FTP Deployment (website/ folder):
| Secret Name | Value | Description |
|-------------|-------|-------------|
| `FTP_SERVER` | `ftp.forusfl.co.zm` | FTP server hostname |
| `FTP_USERNAME` | `update9240@forusfl.co.zm` | FTP username |
| `FTP_PASSWORD` | `[your_password]` | FTP password (keep secret!) |
| `FTP_REMOTE_PATH` | `` (empty) or `/` | Remote path on FTP (relative to FTP home) |

⚠️ **Important**: Files in `website/` folder are uploaded directly to `/public_html/` on the server.

#### For Ecommerce/VM FTP Deployment (Optional):
| Secret Name | Value |
|-------------|-------|
| `VM_FTP_SERVER` | VM FTP server hostname |
| `VM_FTP_USERNAME` | VM FTP username |
| `VM_FTP_PASSWORD` | VM FTP password |

---

## How to Add Secrets to GitHub

### Step 1: Go to Repository Settings
1. Navigate to: **https://github.com/mosesjust2016/ForusFreight/settings**
2. Click: **Secrets and variables** → **Actions**

### Step 2: Add FTP Secrets
Click **"New repository secret"** for each:

```
FTP_SERVER = ftp.forusfl.co.zm
FTP_USERNAME = update9240@forusfl.co.zm
FTP_PASSWORD = [your_actual_ftp_password]
FTP_REMOTE_PATH = [leave empty or use /]
```

**Note**: Leave `FTP_REMOTE_PATH` empty if FTP user home is `/public_html`, or set to `/` to upload to root.

### Step 3: Test Deployment
Push to `main` branch:
```bash
git push origin main
```

Check GitHub Actions tab to see deployment progress.

---

## Deployment Workflow

### What Happens When You Push to `main`:

1. **Build Stage** (~3-5 min):
   - Checkout code with submodules
   - Setup PHP 8.2
   - Install Composer dependencies
   - Setup Node 20
   - Install NPM packages
   - Build Vite assets

2. **Website FTP Deploy Stage** (~2-3 min):
   - Clean build artifacts (node_modules, vendor/.git, cache)
   - Upload **only `website/` folder** to `/public_html/`
   - Skip uploading: .env, vendor, storage/logs, node_modules
   - Report success/failure

3. **Ecommerce Docker Deploy Stage** (~2-3 min) - *Parallel*:
   - Upload **only `Ecommerce/` folder** to VM
   - Trigger Docker Compose rebuild
   - Deploy services: API, Admin Portal, Storefront

4. **Manual Post-Deployment** (You do this via SSH):
   ```bash
   # SSH into cPanel server
   ssh update9240@forusfl.co.zm
   
   # Navigate to web root
   cd /public_html
   
   # Run migrations
   php artisan migrate --force
   
   # Clear caches
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   
   # Restart queue worker
   sudo supervisorctl restart forusfreight-queue:*
   ```

---

## FTP vs SSH Comparison

| Feature | SSH (Old) | FTP (New) |
|---------|-----------|----------|
| Key Management | Complex (RSA/Ed25519 keys) | Simple (username/password) |
| Security | High (key-based auth) | Medium (password auth) |
| Speed | Fast (~2 min) | Slower (~5 min) |
| Reliability | Good | Excellent (simple protocol) |
| Post-deploy scripts | ✅ Can run via SSH | ❌ Need separate setup |
| Ideal for | Full automation | Manual post-steps |

---

## Important Notes

### ⚠️ Post-Deployment Steps are Manual

Unlike SSH deployment, FTP cannot run commands on the server. You must manually SSH and run:

```bash
cd /path/to/website
php artisan migrate --force
php artisan cache:clear
php artisan view:clear
```

**Frequency**: Only needed after first deployment or when:
- Database schema changes (new migrations)
- Cache needs clearing
- New config values added

### 🔒 FTP Password Security

- Store password ONLY in GitHub secrets, never commit to repo
- Rotate FTP password annually
- If password leaked, change in hosting control panel immediately

### 📁 Deployment Targets

#### Website → cPanel (via FTP)
**Source**: `website/` folder  
**Destination**: `/public_html/` on cPanel  
**Contents**: Laravel application files

```
/public_html/
├── app/                    ← Laravel app directory
├── config/                 ← Configuration files
├── resources/              ← Views, assets
├── routes/                 ← Route definitions
├── storage/                ← Logs, cache, uploads
├── public/                 ← Web-accessible files (CSS, JS, images)
├── artisan                 ← Laravel CLI
├── composer.json           ← PHP dependencies
├── .env                    ← Environment config (NOT uploaded, manually set)
└── [other Laravel files]
```

#### Ecommerce → VM (via Docker)
**Source**: `Ecommerce/` folder  
**Destination**: VM 46.62.161.138:/opt/forus-digital  
**Services**: 
- forus-digital-api
- forus-digital-admin-portal
- forus-digital-storefront

---

## Troubleshooting

### FTP Connection Fails

**Error**: `Connection refused on ftp.forusfl.co.zm:21`

**Solutions**:
1. Verify FTP credentials in GitHub secrets
2. Check FTP is enabled in cPanel (not disabled)
3. Try connecting locally to verify credentials:
   ```bash
   ftp ftp.forusfl.co.zm
   # Login with update9240@forusfl.co.zm
   ```

### Files Not Uploading

**Error**: `Permission denied` or `550 No such file or directory`

**Solutions**:
1. Verify `CPANEL_DEPLOY_PATH` is set to `/public_html`
2. Ensure FTP user (update9240@forusfl.co.zm) has write permissions on `/public_html`
3. Check in cPanel file manager that `/public_html` exists and is writable
4. Verify FTP user home directory maps to root (not a subdirectory)

### Deployment Succeeds But Site Broken

**Likely cause**: Missing post-deployment steps

**Fix**:
```bash
# SSH into server
cd /public_html/website

# Run migrations
php artisan migrate --force

# Clear cache
php artisan cache:clear
php artisan view:clear

# Restart queue
sudo supervisorctl restart forusfreight-queue:*
```

---

## Monitoring Deployments

### View Deployment Status

1. Go to: https://github.com/mosesjust2016/ForusFreight/actions
2. Click latest workflow run
3. Expand "Deploy to cPanel Server via FTP" step
4. Read logs to see upload progress

### Common Log Messages

```
✅ "FTP deployment completed!"
   → Files uploaded successfully

⚠️  "500 Syntax error"
   → FTP credentials invalid

⏳ "Uploading 234 files..."
   → Upload in progress (be patient)

❌ "FAILED: Connection timeout"
   → FTP server unreachable or down
```

---

## Setting Up Post-Deployment Automation (Advanced)

If you want to automate the post-deployment steps, consider:

### Option 1: cPanel Cron Job
Create a cron job that runs daily at 2 AM:
```bash
cd /public_html/website && php artisan migrate --force 2>&1
```

### Option 2: GitHub Actions SSH Step (Requires SSH access)
Add SSH step after FTP to run migrations:
```yaml
- name: Run Post-Deployment Tasks
  uses: appleboy/ssh-action@v1.0.3
  with:
    host: forusfl.co.zm
    username: ${{ secrets.SSH_USERNAME }}
    key: ${{ secrets.SSH_KEY }}
    script: |
      cd /public_html/website
      php artisan migrate --force
      php artisan cache:clear
```

### Option 3: Deploy Hook Script
Ask hosting provider if they support deploy hooks that trigger after FTP upload.

---

## Performance Tips

### Reduce Upload Time

Currently uploads all files. To optimize:

1. **Use `.ftpignore`** (if supported):
   ```
   node_modules/
   vendor/
   .git/
   storage/logs/
   bootstrap/cache/
   ```

2. **Compress on upload**:
   Modify workflow to tar.gz and extract on server

3. **Incremental uploads**:
   Only upload changed files (rsync-style)

---

## Rollback Strategy

If deployment breaks production:

### Quick Rollback
```bash
# SSH into server
cd /public_html/website

# Revert to previous git commit
git fetch origin
git reset --hard origin/main~1

# Clear cache
php artisan cache:clear
```

### Safer: Keep Backup
Keep a backup branch:
```bash
git branch backup-2026-06-13
# Then rollback to it if needed
```

---

## Environment Setup Checklist

Before first production deployment:

- [ ] Add all FTP secrets to GitHub
- [ ] Test FTP credentials locally
- [ ] Know the correct FTP home directory path
- [ ] Have SSH access for post-deployment steps
- [ ] Setup supervisor for queue worker
- [ ] Verify `.env` is NOT in FTP upload (use `.env.example`)
- [ ] Test deployment on staging first
- [ ] Have rollback plan ready
- [ ] Document exact steps for your hosting provider

---

## Quick Reference

### Deploy Now
```bash
git push origin main
```

### Monitor Deployment
https://github.com/mosesjust2016/ForusFreight/actions

### SSH After Deployment
```bash
ssh update9240@forusfl.co.zm
cd /public_html/website
php artisan migrate --force
php artisan cache:clear
```

### Disable Deployment
Comment out `on: push` in `.github/workflows/deploy.yml`

---

## Support

**Issues?** Check:
1. GitHub Actions logs (Actions tab)
2. FTP credentials in GitHub secrets
3. Server permissions (ls -la in FTP)
4. `.env` file exists with correct values
5. Queue worker running (supervisorctl status)

---

**Last Updated**: June 13, 2026  
**Deployment Method**: FTP via GitHub Actions  
**Status**: ✅ Ready for use
