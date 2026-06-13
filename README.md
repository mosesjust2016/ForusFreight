# ForusFreight

> A full-suite freight & logistics platform built on Laravel (Livewire), with an Ecommerce portal and Terraform infrastructure configs.

---

## 📁 Project Structure

```
ForusFreight/
├── website/        # Laravel 11 + Livewire main application
├── Ecommerce/      # Ecommerce portal
└── TerraForm/      # Infrastructure-as-code (Terraform)
```

---

## 🚀 Local Setup (website)

```bash
cd website
cp .env.example .env
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate
php artisan serve
```

---

## 🔄 Deployment

Pushes to the `main` branch automatically deploy to the cPanel production server via GitHub Actions.

See [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml) for the full pipeline.

### Required GitHub Secrets

| Secret | Description |
|---|---|
| `CPANEL_HOST` | cPanel server hostname / IP |
| `CPANEL_USERNAME` | SSH username |
| `CPANEL_SSH_KEY` | Private SSH key (Ed25519 / RSA) |
| `CPANEL_SSH_PASSPHRASE` | Private key passphrase, if the key is encrypted |
| `CPANEL_PORT` | SSH port (default `22`) |
| `CPANEL_DEPLOY_PATH` | Absolute path on server e.g. `/home/user/public_html` |
| `VM_SSH_KEY` | Private SSH key for `root@46.62.161.138` VM deploy |

If deployment fails with `Permission denied (publickey,password)`, verify that `CPANEL_SSH_KEY`
contains the full private key, the matching public key is authorized for `CPANEL_USERNAME` in
cPanel SSH Access, and the username / host / port match the same account.

The `forus-digital-api` and `forus-digital-admin-portal` services deploy to
`root@46.62.161.138:/opt/forus-digital` with Docker Compose, not to cPanel. Set
`VM_SSH_KEY` to the contents of the VM private key, for example
`/Users/moses/Desktop/molomarketing/terraform/ssh_keys/id_rsa_github`.

---

## 🛠 Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Livewire 3, Tailwind CSS, Vite
- **Database**: MySQL
- **CI/CD**: GitHub Actions → cPanel (SSH/rsync)
