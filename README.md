# PrivateJetExecutive.com

A lightweight, server-rendered PHP foundation for PrivateJetExecutive.com. It is designed for conventional PHP/cPanel hosting and keeps application code separate from the public web root.

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

The contact and charter pages include server-side validation, CSRF tokens, honeypot protection, session-backed arithmetic CAPTCHA, and basic session rate limiting. They deliberately do not store or email enquiries yet: until the SMTP feature is configured, a valid submission directs visitors to `charter@privatejetexecutive.com` rather than claiming delivery.

Before activating email delivery, configure PHPMailer, SMTP credentials outside the web root, customer and internal email templates, and appropriate SPF, DKIM, and DMARC records. The next technical addition after SMTP is the airport-search proxy.
