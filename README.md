# Saddam Hossen — WordPress Engineering Site 1.0.0

[![WordPress quality](https://github.com/iamsaddamhossen/saddam-wordpress-engineer-site/actions/workflows/quality.yml/badge.svg)](https://github.com/iamsaddamhossen/saddam-wordpress-engineer-site/actions/workflows/quality.yml)

A recruiter-focused WordPress engineering site built as two installable packages:

- `saddam-engineer-portfolio/` — presentation, responsive UI, templates, block patterns and editor styles.
- `saddam-engineer-core/` — portable content models, registered metadata, REST routes, Gutenberg block, contact workflow, privacy, WP-CLI, Site Health, SEO/schema and assistant behavior.

## Public release goals

- Present Saddam Hossen clearly as a Senior WordPress Engineer.
- Prioritize production ownership, debugging, plugins, WooCommerce, Gutenberg and integrations.
- Separate engineering case studies from broader website delivery.
- Give non-technical editors safe control over normal copy, images and repeatable content.
- Keep claims grounded in visible content and inspectable implementation.

## Editor architecture

- Core blocks for normal copy and media.
- Theme block patterns for approved reusable sections.
- Custom post types plus registered fields for repeatable records.
- A custom block only for the engineering proof interface.
- Settings for sitewide profile, contact, SEO and homepage copy.

## Quality commands

```bash
composer install
composer phpcs
composer compatibility
composer test
```

GitHub Actions runs syntax, manifest, coding-standard and integration checks.

## Installation

1. Install `saddam-engineer-core`.
2. Install and activate `saddam-engineer-portfolio`.
3. Run **Saddam Engineer → Starter Content → Import / Repair Site Content**.
4. Save permalinks.
5. Configure Homepage, Profile, SEO, Email and Assistant settings.
6. Test the homepage, Engineering, Writing, Projects, Case Studies, Experience and Contact pages on desktop and mobile.

## Version

Initial public release: `1.0.0`.

## CI/CD and maintenance

- Pull requests run PHP, JavaScript, JSON, coding-standard, compatibility and WordPress integration checks.
- Semantic version tags build clean plugin/theme ZIP files and SHA-256 checksums.
- Production deployment stays staging-first and manual until the host provides scoped credentials, health checks and a documented rollback path.
- See `docs/CI-CD-OPERATIONS.md` and `docs/MAINTENANCE-RUNBOOK.md`.

## Contribution and security

Use pull requests and the supplied checklist. Never commit credentials, client exports, production database dumps or private client code. See `CONTRIBUTING.md` and `docs/SECURITY.md`.
