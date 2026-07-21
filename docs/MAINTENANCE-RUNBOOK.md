# Portfolio Maintenance Runbook

## Weekly

- Review uptime, PHP errors, failed form submissions, and WordPress Site Health.
- Apply safe plugin/theme updates on staging first.
- Test homepage, Engineering, Writing, Contact, one project, one case study, and one article.
- Review Search Console coverage, broken links, and new search queries.
- Confirm GitHub Actions are green and Dependabot PRs are triaged.
- Publish or improve one useful engineering note, project README, or case-study detail.

## Monthly

- Verify a backup can be restored.
- Review administrator accounts, API keys, webhooks, and unused plugins.
- Compare performance against the saved baseline.
- Check schema output, canonical URLs, sitemap discovery, and social previews.
- Review content dates, outdated claims, screenshots, links, and role availability.
- Update one pinned GitHub repository and one LinkedIn Featured item.

## Quarterly

- Test against the latest stable WordPress and supported PHP versions.
- Review WordPress Coding Standards and Plugin Check findings.
- Run a full accessibility and keyboard audit.
- Rotate credentials where appropriate.
- Audit database size, cron events, transients, logs, and media storage.
- Refresh CV, LinkedIn, WordPress.org profile, and portfolio evidence.
- Archive weak or abandoned GitHub repositories rather than leaving misleading work public.

## Release checklist

- Version headers and changelog agree.
- Database migrations are idempotent and tested.
- No secrets or client-confidential data are included.
- PHP, JavaScript, JSON, PHPCS, compatibility, and tests pass.
- Mobile, desktop, forms, keyboard, and error states are checked.
- Backup and rollback steps are documented.
- Release tag and artifacts are immutable and checksummed.
