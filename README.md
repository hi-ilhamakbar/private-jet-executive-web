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

In cPanel, either set the domain document root to `public/`, or deploy the contents of `public/` to `public_html` and keep `app/`, `config/`, `templates/`, `storage/`, and secrets one level above it. Update the include paths accordingly if using the latter arrangement.

For production, enable HTTPS and add `Strict-Transport-Security` at the web-server level after HTTPS is verified. Revisit CSP when third-party integrations (airport search, analytics, maps, or payment services) are introduced.

## Next implementation phase

Build the private-charter request flow: secure server validation, CSRF and CAPTCHA, the airport-search proxy, SMTP via PHPMailer, and optional database persistence. Configure SPF, DKIM, and DMARC with the actual mail provider before enabling production email.
