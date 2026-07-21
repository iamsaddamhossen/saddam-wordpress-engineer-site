# CV Feature Map

Use this document to convert implemented code into accurate CV bullets. Do not use a bullet until you can explain the referenced code and trade-offs.

| CV-ready capability | Repository implementation | How to explain it |
|---|---|---|
| Architected a custom WordPress theme and companion plugin | Theme `functions.php`; plugin bootstrap; `ARCHITECTURE.md` | Explain portability and lifecycle |
| Built custom post types, taxonomies and registered metadata | `class-spcore-cpt.php`; `class-spcore-meta.php` | Add or change a field safely |
| Developed versioned REST endpoints | `class-spcore-rest.php`; REST tests | Explain permissions, validation, headers and cache |
| Built a dynamic Gutenberg block | `blocks/engineering-proof/` | Explain editor attributes and server rendering |
| Implemented WP-CLI operational commands | `class-spcore-cli.php` | Run status and cache commands |
| Integrated WordPress privacy tools | `class-spcore-privacy.php` | Explain exporter/eraser paging and anonymization |
| Designed a custom indexed lead table | `class-spcore-leads.php` | Defend the CPT-plus-table trade-off |
| Secured a progressive contact workflow | `class-spcore-contact.php` | Walk through threat and failure paths |
| Added Site Health diagnostics | `class-spcore-health.php` | Add one new health test |
| Implemented cache lifecycle and cron retention | `class-spcore-cache.php` | Explain invalidation and scheduled cleanup |
| Added JSON-LD, metadata and `llms.txt` | schema and SEO modules | Explain generated versus hard-coded output |
| Established automated quality gates | Composer, PHPCS, PHPUnit, GitHub Actions | Read or fix a failed check |
| Built accessible dependency-free frontend behavior | Theme templates, `main.js`, `theme.json` | Test no-JS and reduced-motion behavior |

## Example CV bullets

- Architected a custom WordPress site as a portable companion plugin plus presentation theme, separating structured content and operational logic from the visual layer.
- Developed versioned WordPress REST endpoints with explicit permission callbacks, argument validation, pagination headers and transient caching.
- Built a dynamic `block.json` Gutenberg block with InspectorControls and server-side rendering from a single feature registry.
- Implemented a progressive contact workflow with no-JavaScript fallback, nonces, honeypot/time checks, optional CAPTCHA, rate limiting, safe webhook delivery and privacy-aware storage.
- Added WP-CLI operations, Site Health diagnostics, scheduled cache/retention tasks and WordPress personal-data exporter/eraser integrations.
- Established repository quality gates with WordPress Coding Standards, PHP compatibility rules, PHPUnit integration tests and GitHub Actions.
