# Technical Review Guide

This guide provides a focused path through the codebase and the engineering decisions behind it.

## 1. Architecture boundary

Open:

- `saddam-engineer-portfolio/functions.php`
- `saddam-engineer-core/saddam-engineer-core.php`
- `saddam-engineer-core/docs/ARCHITECTURE.md`

Review:

- why presentation lives in a theme and portable data/behavior live in a plugin;
- activation, deactivation and non-destructive upgrades;
- WordPress hooks and module boot order;
- the trade-off between two packages and stronger portability.

## 2. WordPress data modeling

Open:

- `includes/class-spcore-cpt.php`
- `includes/class-spcore-meta.php`
- `includes/class-spcore-leads.php`

Review:

- CPTs and registered metadata for editorial data;
- an indexed custom table for operational lead queries;
- the private CPT mirror used for admin usability;
- capabilities, sanitization, escaping and status synchronization.

## 3. API and Gutenberg

Open:

- `includes/class-spcore-rest.php`
- `blocks/engineering-proof/block.json`
- `blocks/engineering-proof/editor.js`
- `blocks/engineering-proof/render.php`

Review:

- public versus administrator-only routes;
- argument validation and pagination limits;
- transient caching and invalidation;
- dynamic/server rendering and `block.json` registration.

## 4. Contact, privacy and failure paths

Open:

- `includes/class-spcore-contact.php`
- `includes/class-spcore-privacy.php`
- `includes/class-spcore-cache.php`

Review:

- AJAX plus no-JavaScript fallback;
- nonce, honeypot, time trap, CAPTCHA, allowlists and rate limiting;
- mail failure while preserving the stored request;
- privacy-first IP defaults, exporter/eraser and retention;
- signed optional webhook and safe URL validation.

## 5. Operations and quality

Open:

- `includes/class-spcore-cli.php`
- `includes/class-spcore-health.php`
- `tests/`
- `composer.json`
- `.github/workflows/quality.yml`

Review:

- operational visibility outside wp-admin;
- Site Health checks for configuration risks;
- automated-test boundaries and remaining manual integration tests;
- coding standards, PHP compatibility and release packaging.

## 6. Frontend and accessibility

Open:

- `saddam-engineer-portfolio/front-page.php`
- `template-engineering.php`
- `assets/css/main.css`
- `assets/js/main.js`
- `theme.json`

Review:

- full-viewport hero measurement and responsive fallbacks;
- semantic content hierarchy and consistent grids;
- dependency-free progressive enhancement;
- reduced motion, keyboard navigation, focus states and no-JavaScript behavior.

## Practical exercises

1. Add a `technology` filter to the projects REST route and update its test.
2. Add a new feature field to the dynamic block.
3. Add a Site Health test for mail configuration.
4. Implement cache invalidation for a new project taxonomy.
5. Design a queue/retry path for failed webhook delivery.
