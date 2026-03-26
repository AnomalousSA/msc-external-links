# Micro Site Care: External Links

Lightweight external link highlighting for WordPress. Adds an icon to external links, enforces `rel="noopener noreferrer"` and `target="_blank"` where configured, and provides domain exclusion controls.

## Features

- Adds an external-link indicator icon to links pointing to external domains
- Automatically applies `rel="noopener noreferrer"` and `target="_blank"` (configurable)
- Domain exclusion list to leave internal or trusted domains untouched
- Lightweight and dependency-free — small footprint and no external services
- Settings fallback when using the Pro extension

## Requirements

- WordPress 5.9 or later
- PHP 7.4 or later

## Installation

There are two common ways to install this plugin:

### 1) Upload via WordPress Admin

1. Compress the plugin folder into a ZIP file (for example `msc-external-links.zip`).
2. Go to **Plugins > Add New** in your WordPress admin.
3. Click **Upload Plugin**, choose the ZIP file, and click **Install Now**.
4. After installation click **Activate Plugin**.

### 2) Install via FTP / SFTP

1. Extract the plugin folder and upload the `msc-external-links` directory to `wp-content/plugins/` on your server.
2. Visit **Plugins** in the WordPress admin and click **Activate** under *Micro Site Care: External Links*.

## Configuration

After activation, open the settings at **Site Care > External Links** to configure:

- Enable/disable icon insertion
- Choose whether to force `target="_blank"` and `rel="noopener noreferrer"`
- Add domains to the exclusion list (one domain per line)

If the Pro extension is installed, additional settings and per-link overrides become available in **Site Care > External Links Pro**.

## Usage

Once activated and configured, the plugin runs automatically and modifies front-end output to mark external links. For per-link control (Pro only) the Gutenberg link toolbar exposes override options.

## FAQ

- Q: Will this affect internal links?
	- A: Only links that point to external domains are affected. Add trusted domains to the exclusion list to keep them unchanged.
- Q: Does it modify the database?
	- A: No persistent database changes are made by the free plugin; it filters output on render.

## Compatibility with Pro

Install `msc-external-links-pro` alongside this plugin to enable icon style variants, click analytics, an admin analytics dashboard, and per-link Gutenberg overrides.

## Screenshots

1. Settings page: External Links options
2. Front-end: Example external link with icon

(Add actual screenshots to `assets/screenshots/` if desired.)

## Changelog

### 0.1.0
- Initial release.

## Support

For support and feature requests, open an issue or contact the maintainers.

## License

This plugin is licensed under the GNU General Public License v2 (or later). See the `LICENSE` file for details.

## Development & Linting

This repository contains development tooling (`composer.json`, `package.json`, `phpcs.xml.dist`, `.editorconfig`). These files are not included in packaged ZIPs and are intended for development and linting only. Run `composer install` then `npm run lint` or `npm run lint-fix` in the plugin directory to use the configured PHPCS setup.

---

Micro Site Care — small utilities to keep WordPress sites tidy.
