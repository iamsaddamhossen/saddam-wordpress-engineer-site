# Launch Checklist

## Before upload

- Back up WordPress files and the database.
- Verify WordPress and PHP meet the package requirements.
- Confirm SMTP or transactional email before enabling public confirmations.
- Keep API keys, passwords and client-private data out of Git.

## Install

1. Upload and activate `saddam-engineer-core`.
2. Upload and activate `saddam-engineer-portfolio`.
3. Run **Saddam Engineer → Starter Content → Import / Repair Site Content** once.
4. Save **Settings → Permalinks**.
5. Configure profile links, email, privacy and optional anti-spam/AI settings.
6. Clear application, host, CDN and browser caches.

## Runtime checks

- Open `/wp-json/spcore/v1/engineering`.
- Open `/wp-json/spcore/v1/projects`.
- Run `wp spcore status` when WP-CLI is available.
- Review **Tools → Site Health**.
- Insert the Engineering Proof block on a test page.
- Submit the contact form with and without JavaScript.
- Check the homepage at 1440px, 1024px, 768px, 390px and 320px.

## Release gate

Do not launch until `docs/QA-CHECKLIST.md` passes on staging. Every public metric, project statement and recommendation must remain accurate and explainable.
