=== Micro Site Care: External Links ===
Contributors: anomalousdevelopers
Tags: external links, noopener, target blank, link icon, SEO
Requires at least: 5.9
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight external link highlighting: automatic noopener/noreferrer, optional target blank, icon indicator, and domain exclusion list.

== Description ==

Micro Site Care: External Links automatically detects and decorates external hyperlinks in your post content with configurable behaviour:

* Adds `rel="noopener noreferrer"` for security.
* Optionally opens external links in a new tab.
* Optionally appends a small arrow icon via CSS.
* Per-domain exclusion list — matched domains are treated as internal.
* Filter hook `mscel_is_external_link` for developer override.

Upgrade to **External Links Pro** for icon style variants (arrow, box, text), Gutenberg per-link override toolbar, and click analytics.

== Installation ==

1. Upload the `msc-external-links` folder to `wp-content/plugins/`.
2. Activate through **Plugins > Installed Plugins**.
3. Navigate to **Site Care > External Links** to configure.

== Frequently Asked Questions ==

= Does this work with the block editor? =
Yes. The content filter runs on `the_content`, which covers both classic and block editor output.

= How do I exclude my affiliate network? =
Go to **Site Care > External Links** and add the domain (e.g. `partner.example.com`) to the Exclude Domains field.

== Changelog ==

= 0.1.0 =
* Initial release.

== Upgrade Notice ==

= 0.1.0 =
Initial release.
