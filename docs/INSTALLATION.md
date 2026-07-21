# Installation and Launch

## Recommended staging path

1. Create or open a staging WordPress site.
2. Back up the current files and database.
3. Upload and activate `saddam-engineer-core-v1.0.0.zip`.
4. Upload and activate `saddam-engineer-portfolio-v1.0.0.zip`.
5. Open **Saddam Engineer → Starter Content**.
6. Click **Import / Repair Site Content** once.
7. Open **Settings → Permalinks** and click **Save Changes**.
8. Open **Saddam Engineer → Profile** and set the CV, GitHub, LinkedIn, Upwork, email and availability details.
9. Configure SMTP before relying on public contact email.
10. Configure CAPTCHA, retention, webhook or AI settings only after staging tests.
11. Clear WordPress, host, CDN and browser caches, then test in a private browser window.

## Upgrade behavior

The importer is idempotent. It updates seeded records and renames the earlier project, case study and theme/plugin article titles instead of creating a second copy. It does not delete arbitrary user-created content.

## Development installation

```bash
wp plugin activate saddam-engineer-core
wp theme activate saddam-engineer-portfolio
wp rewrite flush
wp spcore status
```

## Required post-launch edits

- Add the correct CV, LinkedIn, GitHub and Upwork URLs.
- Review every project and case study for NDA boundaries.
- Keep only metrics and testimonials supported by records.
- Replace temporary images with permission-safe production visuals.
- Submit the contact form, test privacy tools and review the PHP/browser logs.
