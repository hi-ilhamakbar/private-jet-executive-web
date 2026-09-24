# Private Jet Executive

A lightweight, server-rendered PHP foundation for Private Jet Executive. It is designed for conventional PHP/cPanel hosting and keeps application code separate from the public web root.

## Foundation included

- Responsive shared header, footer, navigation, typography, buttons, and layout tokens
- Brand asset copies in `public/assets/images/` (`logo.png`, `favicon.ico`, and the palette reference)
- Simple PHP routing with home, placeholder, 404, and safe generic-error views
- Per-page title, description, canonical, Open Graph, and Twitter metadata
- `robots.txt`, initial sitemap, security response headers, and a restrictive baseline CSP
- Secret-safe `.env.example`, configuration example, and Git ignores

## Requirements

- PHP 8.1 or newer
- Apache with `mod_rewrite` for clean URLs in the supplied `.htaccess`
- A document root that points to the `public/` directory

## Local development

From the repository root, run:

```powershell
php -S localhost:8000 -t public
```

Then open `http://localhost:8000`.

## Project structure

```text
app/Core/                 Routing and rendering primitives
config/                   Tracked configuration examples only
public/                   Web-accessible entry point and assets
templates/layouts/        Shared document layout
templates/partials/       Shared header and footer
templates/pages/          Page views
storage/logs/             Runtime logs; contents are ignored
```

## Configuration and deployment

Never commit SMTP, database, API, or server credentials. Copy `.env.example` to a secret location outside `public_html`, configure real environment variables there, and ensure it is unreadable by the web server's public path. `config/config.example.php` is a documented fallback shape, not production configuration.

### cPanel deployment where document roots must remain under `public_html`

Keep the Git repository outside the public web root, for example at:

```text
/home/ACCOUNT/repositories/private-jet-executive-web
```

Copy the **contents** of its `public/` directory—not the `public` folder itself—to the domain document root, for example:

```text
/home/ACCOUNT/public_html/privatejetexecutive.com
```

In that document root, copy `runtime-config.example.php` to `runtime-config.php` and set `app_root` to the repository's absolute path. The runtime configuration is ignored by Git and blocked from direct HTTP access. The entry point also recognizes `PJE_APP_ROOT` when the server makes environment variables available.

Do not copy `app/`, `config/`, `templates/`, or `storage/` inside the domain document root; keeping them in the repository prevents public access.

For production, enable HTTPS and add `Strict-Transport-Security` at the web-server level after HTTPS is verified. Revisit CSP when third-party integrations (airport search, analytics, maps, or payment services) are introduced.

## Next implementation phase

The contact and charter pages include server-side validation, CSRF tokens, honeypot protection, session-backed arithmetic CAPTCHA, rate limiting, reference numbers, and SMTP delivery via PHPMailer.

Airport and city lookup is routed through `public/api/airports.php`. The endpoint validates each query, applies a session rate limit, uses a short server-side cURL timeout, and forwards the request to RateHawk without exposing credentials in browser code. The cPanel deployment configuration includes this endpoint.

### SMTP production setup

Install production dependencies from the repository root before accepting live enquiries:

```powershell
composer install --no-dev --optimize-autoloader
```

Create `/home/ACCOUNT/privatejetexecutive-config/.env` outside `public_html` by copying `.env.example`, then set the real `SMTP_PASSWORD`. Alternatively, set `PJE_ENV_PATH` to an absolute path to that file through the hosting environment. Never place the real `.env` in the repository or document root.

The application sends the customer confirmation from `MAIL_FROM`, uses `MAIL_REPLY_TO` for replies, and BCCs the addresses in `MAIL_BCC_1` and `MAIL_BCC_2`. Configure SPF, DKIM, and DMARC with the actual email provider before enabling production use.

### Invoice administration

The protected invoice workspace is available at `/admin/invoices`. Set `ADMIN_USERNAME` and a PHP password hash in the secret `.env` outside `public_html`; never store an admin password in source control. Generate a hash locally with:

```powershell
php -r "echo password_hash('choose-a-strong-password', PASSWORD_DEFAULT), PHP_EOL;"
```

Create a MySQL or MariaDB database and database user in cPanel, then place `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in the same secret `.env`. Import `database/migrations/001_create_invoices.sql` through phpMyAdmin before using the workspace. The migration enforces invoice-number uniqueness.

Invoices use Asia/Jakarta for their generated date and follow `#INV/YYMMDD/EEEEE`. The final five digits are epoch-derived; insertion occurs under a unique database key and retries a collision before a PDF is produced. Monetary values are stored as integer USD cents. PDFs are generated server-side with Dompdf and downloaded directly to the authenticated administrator.
