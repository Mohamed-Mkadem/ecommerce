# Sweetia VPS Deployment and Backup Runbook

Last updated: 2026-10-22

This guide records the deployment path used for the Sweetia demo, from an empty VPS through recurring backups and GitHub Actions deployments. It is also a template for future client deployments. Replace all example names, paths, branch names, database names, domains, and credentials for each client. Package versions and provider screens change; check the current Laravel, PHP, Ubuntu, CloudPanel, and provider documentation before reusing commands.

This runbook describes the Sweetia OVH VPS, not the separate Boughanmi Patisserie Hostinger/CloudPanel server.

## Deployment overview

The Sweetia demo uses:

- Ubuntu VPS, Nginx, PHP-FPM 8.4, MySQL 8.4, Node.js 22, and Composer.
- Laravel 12 + Vue 3/Inertia/Vite, served from the Laravel `public` directory.
- Domain `sweetia.mohamad.tn`, with DNS managed separately from the OVH VPS.
- A non-root `deploy` account for application files and releases.
- Supervisor for Laravel queue workers and systemd for Reverb.
- GitHub Actions to deploy the active branch over SSH.
- Daily local and encrypted Google Drive backups of the database and public uploads.

Sweetia currently requires PHP `^8.4.1` per `composer.json`. The PHP 8.5 packages available from the original Ubuntu image were incompatible with the locked PhpSpreadsheet dependency, so this deployment used PHP 8.4 from the Sury repository. Re-check the current lockfile and supported packages for a new deployment instead of blindly adding a third-party PHP repository.

## 1. Provision and prepare the VPS

1. Create the VPS with a supported Ubuntu LTS image. Record its public IPv4 address and keep the provider console/KVM available as a recovery path.
2. Connect using the initial account/provider credentials. On first login, change any temporary password when prompted. Prefer SSH keys after confirming key-based access works.
3. Update Ubuntu packages, then reboot if the upgrade installs a new kernel:

   ```bash
   sudo apt update
   sudo apt upgrade
   sudo reboot
   ```

   Reconnect and confirm `uname -r` reports the running kernel after reboot. Do not run a full distribution upgrade unless planned separately.

4. Create a dedicated deployment user (Sweetia used `deploy`) and install the administrator's public SSH key in that user's `~/.ssh/authorized_keys`. Verify a new SSH login as `deploy` before closing the original session. The deployment account must not have general sudo access.

   ```bash
   sudo adduser deploy
   sudo install -d -o deploy -g deploy -m 700 /home/deploy/.ssh
   sudo cp /home/ubuntu/.ssh/authorized_keys /home/deploy/.ssh/authorized_keys
   sudo chown deploy:deploy /home/deploy/.ssh/authorized_keys
   sudo chmod 600 /home/deploy/.ssh/authorized_keys
   ```

   Adjust the source account path if it is not `ubuntu`. On a new server, it is also fine to add the correct public key directly rather than copying another account's file.

5. On the administrator's Windows machine, add a host alias to `%USERPROFILE%\.ssh\config` so connecting is easy. Example:

   ```sshconfig
   Host sweetia-vps
       HostName <VPS_IP>
       User deploy
       IdentityFile ~/.ssh/<client_admin_key>
       IdentitiesOnly yes
   ```

   Connect with `ssh sweetia-vps`; enter the private-key passphrase if requested. Keep the administrator key distinct from the GitHub Actions key.

6. Enable UFW only after confirming SSH is allowed. Add web ports after Nginx is installed:

   ```bash
   sudo ufw allow OpenSSH
   sudo ufw default deny incoming
   sudo ufw default allow outgoing
   sudo ufw enable
   sudo ufw status verbose
   ```

   Later run `sudo ufw allow 'Nginx Full'` for ports 80 and 443. Keep SSH access allowed in both the VPS firewall and any provider firewall/security group.

## 2. Point the domain to the VPS

The domain does not need to move to the VPS provider. Add an `A` record for the chosen subdomain in the DNS zone's authoritative hosting account:

```text
Host/name: <subdomain>
Type:      A
Value:     <VPS_IP>
```

For Sweetia, the record was `sweetia.mohamad.tn` → the OVH IPv4. Find the authoritative nameservers first if unsure which DNS panel to edit. Verify both the authoritative server and a public resolver:

```powershell
Resolve-DnsName <subdomain> -Type A -Server <authoritative-nameserver>
Resolve-DnsName <subdomain> -Type A -Server 1.1.1.1
```

Proceed with certificate issuance only after DNS resolves to the VPS. DNS propagation can take time.

## 3. Install the server stack

Install and verify the runtime needed by the current lockfiles. For Sweetia this included PHP 8.4 CLI/FPM and extensions for MySQL, GD, mbstring, XML, cURL, ZIP, Intl, and BCMath; MySQL 8.4; Node.js 22/npm; Composer; Nginx; Git; Supervisor; and Certbot. Example package names for PHP 8.4:

```bash
sudo apt install php8.4-cli php8.4-fpm php8.4-mysql php8.4-gd \
  php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-intl php8.4-bcmath
```

Package names and repositories depend on the Ubuntu release. Before installation, check `apt-cache policy` for each PHP version/package and verify the package source. Sweetia's original Ubuntu 26.04 image offered PHP 8.5, which did not satisfy its locked PhpSpreadsheet dependency; PHP 8.4 was installed from Sury. Use the repository setup instructions maintained by that repository and understand the trust implications before adding it.

Install Node.js/npm and Composer from the maintained sources appropriate for the OS, then verify:

```bash
php8.4 -v
php8.4 -m
node --version
npm --version
composer --version
mysql --version
```

Install and start MySQL, then verify root access locally:

```bash
sudo mysql -u root -e "SELECT VERSION(), CURRENT_USER();"
```

Avoid exposing MySQL publicly. Keep the application database bound to localhost unless there is a specific, secured remote-access requirement.

## 4. Create the database and app user

Use a separate least-privilege application account; do not put MySQL root credentials in Laravel's `.env`:

```sql
CREATE DATABASE <database_name> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '<app_db_user>'@'localhost' IDENTIFIED BY '<unique-long-password>';
GRANT ALL PRIVILEGES ON <database_name>.* TO '<app_db_user>'@'localhost';
SHOW GRANTS FOR '<app_db_user>'@'localhost';
```

Test the connection using the actual schema and account. Sweetia's schema is `sweetia`; `sweetia_app` is its database username, not its schema name.

## 5. Configure GitHub read access and clone the app

As `deploy`, generate a dedicated SSH key for read-only access to the GitHub repository. For unattended pulls, use a key with no passphrase and restrict it to this repository as a read-only deploy key. Never reuse the administrator's SSH key or expose the private key.

```bash
sudo -u deploy ssh-keygen -t ed25519 -f /home/deploy/.ssh/github_<client> -C "<client> deployment read key" -N ""
```

Add the `.pub` key to the repository's GitHub deploy keys with read-only access. Configure `/home/deploy/.ssh/config` for `github.com` with `User git`, the dedicated `IdentityFile`, and `IdentitiesOnly yes`; set ownership to `deploy:deploy` and mode `600`. Verify GitHub's host-key fingerprint against GitHub's published fingerprints before accepting it, then verify:

```bash
sudo -u deploy ssh -T git@github.com
sudo -u deploy git ls-remote --heads git@github.com:<owner>/<repo>.git <deployment-branch>
```

Create the application directory and clone only the intended branch:

```bash
sudo install -d -o deploy -g deploy -m 755 /var/www/<app>
sudo -u deploy git clone --branch <deployment-branch> --single-branch \
  git@github.com:<owner>/<repo>.git /var/www/<app>
```

For Sweetia, the directory is `/var/www/sweetia` and the currently deployed branch is `codex/laravel-12-upgrade`. The branch is not `main` or `dev`; its deployment workflow and server checkout must be changed together if promotion happens later.

## 6. Configure Laravel and build the application

Create `/var/www/<app>/.env` from `.env.example`, owned/readable only by the deployment account. Fill in production values for `APP_ENV`, `APP_DEBUG=false`, `APP_URL=https://<domain>`, database credentials, queue, mail, analytics, and any client-specific integrations. Generate a unique `APP_KEY`. Never commit `.env` or paste its secrets into tickets/chat.

For the first Composer install, Laravel package discovery may instantiate the configured Reverb broadcaster. If Reverb credentials are not configured yet, use a temporary command-scoped log broadcaster so Composer can finish without changing the saved `.env`:

```bash
sudo -u deploy env BROADCAST_CONNECTION=log php8.4 /usr/bin/composer install \
  --working-dir=/var/www/<app> --no-dev --optimize-autoloader --no-interaction
```

Then generate the app key and migrate after verifying the `.env` database values:

```bash
sudo -u deploy env BROADCAST_CONNECTION=log php8.4 /var/www/<app>/artisan key:generate --force
sudo -u deploy env BROADCAST_CONNECTION=log php8.4 /var/www/<app>/artisan migrate --force
```

Configure Reverb credentials, then set the saved broadcast driver back to `reverb`. For a browser-facing HTTPS deployment, make the public host/scheme/port consistent with the Nginx WebSocket proxy and rebuild Vite after changing `VITE_*` values. Keep the Reverb server bound to localhost; expose it through Nginx rather than opening its port publicly.

Install Node dependencies and build frontend assets:

```bash
sudo -u deploy npm ci --include=optional --prefix /var/www/<app>
sudo -u deploy npm run build --prefix /var/www/<app>
```

Sweetia's lockfile was generated on Windows and omitted Rollup's Linux optional binary. Its deployment script reads the locked Rollup version and installs the matching Linux package with `--no-save --package-lock=false` before `npm run build`. Keep this workaround if the lockfile still has that issue; preferably repair and validate the lockfile in a controlled cross-platform workflow later.

Create the public storage symlink and ensure Nginx can traverse/read public uploads while `deploy` can write Laravel storage/cache directories. Do not use `chmod 777`:

```bash
sudo -u deploy php8.4 /var/www/<app>/artisan storage:link
```

Confirm `public/storage` points to `storage/app/public` and test an uploaded file over HTTPS. Copy any required seeded images/download templates to `storage/app/public` using a secure transfer method. Keep uploaded files out of Git unless they are intentionally versioned catalog assets.

## 7. Configure Nginx and HTTPS

Create a site configuration with:

- `server_name <domain>`.
- Document root `/var/www/<app>/public` (never the repository root).
- Laravel's `try_files` fallback to `index.php`.
- FastCGI routed to the matching PHP-FPM socket/version.
- Denial of access to dotfiles and sensitive project files.
- A WebSocket proxy from the public HTTPS host to Reverb on `127.0.0.1:8080`.

Enable the site and validate before reloading:

```bash
sudo ln -s /etc/nginx/sites-available/<app> /etc/nginx/sites-enabled/<app>
sudo nginx -t
sudo systemctl reload nginx
```

Allow web traffic in UFW and request the Let's Encrypt certificate after DNS points to the VPS:

```bash
sudo ufw allow 'Nginx Full'
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d <domain>
```

Choose the HTTP-to-HTTPS redirect if prompted. Then set `APP_URL=https://<domain>`, clear/rebuild Laravel config cache, and confirm the response headers and browser page use HTTPS. Verify automatic Certbot renewal is configured.

## 8. Run queues and Reverb persistently

Install Supervisor and create a queue-worker program for the app. Run the worker as `deploy`, use the project's queue connection, set automatic restart/logging, and point it to the correct PHP binary and app path. Then:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

Sweetia uses `QUEUE_CONNECTION=database`; its worker was confirmed `RUNNING`. A queued job does not execute reliably in production until a worker is running.

Create a systemd unit for Reverb with `WorkingDirectory=/var/www/<app>`, the app's PHP binary, and `artisan reverb:start --host=127.0.0.1 --port=8080`. Enable and verify it:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now <app>-reverb
sudo systemctl status <app>-reverb --no-pager
```

Configure Nginx to proxy WebSocket connections to that local listener. Confirm the browser establishes a WebSocket and real-time notifications arrive. Do not open port 8080 in UFW.

## 9. Prepare automated deployments

The deployment process uses a separate GitHub Actions SSH key, not the human key and not the repository read key. Generate an Ed25519 key with an empty passphrase in a secure local environment. Install only its public key in `/home/deploy/.ssh/authorized_keys`; prefix it with restrictions such as:

```text
no-agent-forwarding,no-port-forwarding,no-X11-forwarding,no-pty ssh-ed25519 <public-key> github-actions-<client>
```

Store the private key in a GitHub Actions repository secret. Add repository secrets for `VPS_HOST`, `VPS_PORT`, `VPS_KNOWN_HOSTS` (the verified server host-key line), and `VPS_SSH_KEY`. Do not enable SSH password fallback or disable strict host-key checking in CI.

The deployment script needs to restart only the app's worker and Reverb services. Install a root-owned wrapper under `/usr/local/sbin/<app>-restart-services`, then grant the deploy account passwordless sudo only for that exact wrapper via `/etc/sudoers.d/<app>-deploy`. Validate sudoers with `visudo -cf` and test the wrapper as `deploy`; do not grant broad sudo rights.

The workflow should:

1. Run only for the explicitly selected deployment branch and/or manual dispatch.
2. Use strict SSH host verification and `BatchMode`.
3. Fetch and fast-forward the same branch checked out on the VPS.
4. Run the repository deployment script.
5. Check the public HTTPS storefront after deployment.

Sweetia's `.github/workflows/deploy-production.yml` currently watches `codex/laravel-12-upgrade`. Its `scripts/deploy-production.sh` takes local/offsite backups, installs Composer/npm dependencies, builds assets, enters maintenance mode, migrates, refreshes caches, restarts the queue, exits maintenance mode, then restarts queue and Reverb through the limited sudo wrapper. Review the deployment script before production use: migrations must be backward-compatible with the active app version or have a rollback plan.

Test first with a deliberate, harmless commit and inspect the GitHub Actions run. Confirm the deployed branch/commit, healthy worker/Reverb services, and public site before treating automation as complete.

## 10. Backups: local, encrypted offsite, scheduled, and restore-tested

### Database credentials for unattended backup

Create `/home/deploy/.my.cnf` readable only by `deploy`:

```ini
[client]
user=<app_db_user>
password=<database-password>
host=127.0.0.1
```

Then set ownership and permissions:

```bash
sudo chown deploy:deploy /home/deploy/.my.cnf
sudo chmod 600 /home/deploy/.my.cnf
```

Do not add `database=<schema>` in this file: `mysqldump` treated that as an unknown option on this server. Pass the schema name as the final argument to the dump command. Verify the account can connect:

```bash
sudo -u deploy mysql --defaults-extra-file=/home/deploy/.my.cnf <database_name> \
  -e "SELECT DATABASE(), CURRENT_USER();"
```

### Configure encrypted Google Drive storage with rclone

1. Install a current rclone release on the VPS and a compatible version on a trusted computer with a browser.
2. Configure an OAuth Google Drive remote as the `deploy` user. When rclone asks for browser authentication on the headless VPS, use its displayed `rclone authorize` command on the trusted computer and paste back the resulting token. Use a dedicated Google Cloud OAuth client where practical.
3. Create a second `crypt` remote that wraps the Drive remote. Use standard filename encryption and directory-name encryption, choose a strong unique encryption password and a distinct salt, and store them in a secure password manager. Without them, the files on Drive cannot be decrypted.
4. Verify remote configuration as `deploy`; do not configure it only under `root` or `ubuntu`:

   ```bash
   sudo -u deploy rclone listremotes
   sudo -u deploy rclone lsd <encrypted-remote>:
   ```

An empty crypt remote may report that its directory does not exist until its first upload. After a backup, confirm the encrypted remote can list the decrypted filenames. Never expose the OAuth token or crypt password in logs/chat.

### Backup script behavior and schedule

Sweetia's `scripts/backup-sweetia.sh`:

- Dumps the MySQL schema with `--single-transaction --no-tablespaces`, compresses it, and archives `storage/app/public`.
- Verifies the gzip and tar archives before publishing them.
- Writes mode-600 archives under `/var/backups/sweetia`.
- Uploads through the encrypted rclone remote and keeps 14 days locally and remotely.
- Uses UTC timestamps in filenames.

The script currently names the database `sweetia` and app directory `/var/www/sweetia`; adapt these values or its environment overrides for every client. It backs up public uploads, not the whole application or every generated file under `storage`. Decide separately whether private/generated files must be preserved.

Run one manual backup as `deploy` and check both destinations:

```bash
sudo -u deploy bash /var/www/<app>/scripts/backup-sweetia.sh
sudo -u deploy ls -lh /var/backups/<app>
sudo -u deploy rclone ls <encrypted-remote>:
```

Schedule daily at 03:00 server time in the `deploy` crontab (Sweetia uses the command below):

```cron
0 3 * * * /usr/bin/bash /var/www/sweetia/scripts/backup-sweetia.sh >> /var/backups/sweetia/backup.log 2>&1
```

Install with `sudo -u deploy crontab -e`, then verify with `sudo -u deploy crontab -l`. Review the log after the first scheduled run. The deployment script also runs a backup before changing the app.

### Verify and practice restoration

Check archive integrity after copying a backup to a temporary restore-test directory:

```bash
gzip -t /tmp/<restore-test>/database.sql.gz
tar -tzf /tmp/<restore-test>/uploads.tar.gz >/dev/null
```

This validates archive readability, but it is not a full restore drill. Before relying on backups for a client, restore the SQL into a separate temporary database and extract uploads into a temporary directory; verify expected tables/files and document the recovery sequence. Never test restore by overwriting the live database or uploads.

## 11. Final go-live checklist

- DNS resolves to the intended VPS and HTTPS certificate is valid/renewing.
- `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL`, and no production secrets committed.
- PHP-FPM version matches Composer requirements and the site's CloudPanel/Nginx configuration.
- Database uses a dedicated app account; migrations completed.
- Frontend manifest exists and production assets load over HTTPS.
- `storage:link` works and uploaded assets return HTTP 200.
- Queue worker and Reverb are running; live notifications work through Nginx WebSockets.
- Supervisor, systemd, Nginx, and Laravel logs are accessible for debugging.
- Manual backup exists locally and on encrypted offsite storage; archive checks pass; restore procedure has been practiced.
- GitHub Actions secrets, verified SSH host key, restricted deployment key, and narrow sudo wrapper are in place.
- A deliberate CI deployment succeeds, the public health check passes, and the VPS is on the intended branch/commit.
- Production admin password, mail provider, analytics identifiers, and provider-specific credentials are unique to this client.

## Sweetia-specific reference files

- `tasks.md` — the original staged VPS/deployment plan.
- `SWEETIA_PROJECT_CONTEXT.md` — product, feature, architecture, and current deployment handoff.
- `scripts/deploy-production.sh` — automated application deployment.
- `scripts/backup-sweetia.sh` — local and encrypted offsite backup.
- `scripts/restart-sweetia-services.sh` — worker/Reverb service restart commands.
- `.github/workflows/deploy-production.yml` — branch-triggered GitHub Actions workflow.
