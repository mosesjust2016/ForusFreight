# FTP deployment

Every push to `main` deploys the `website/` Laravel application through FTP after the build job succeeds.

Set these GitHub repository secrets:

| Secret | Value |
|---|---|
| `FTP_PASSWORD` | The FTP account password |
| `FTP_REMOTE_PATH` | Optional remote directory; leave unset when the FTP account opens directly in the web root |

The workflow already uses these supplied defaults:

- Host: `157.20.105.138`
- Port: `21`
- Username: `forusfreight`

Optional overrides are supported with `FTP_SERVER` and `FTP_USERNAME`. The password is never stored in the repository.

The separate VM deployment is disabled unless the repository variable `DEPLOY_VM` is set to `true`.

The FTP upload excludes `vendor/` to keep the deployment fast. The hosting account must already contain Composer dependencies. After the first upload, run this once from cPanel Terminal or SSH in the Laravel application directory:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
php artisan storage:link
php artisan migrate --force
php artisan optimize:clear
```

Repeat `composer install` whenever `composer.lock` changes or a new PHP package is added.
