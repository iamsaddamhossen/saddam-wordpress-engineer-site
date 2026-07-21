# CI/CD Operations

## Purpose

The repository uses pull-request quality gates and tag-based release packaging. It does not deploy directly to production because the live site may be hosted on infrastructure where credentials, filesystem layout, and rollback controls differ. Release ZIPs are the trusted deployment artifacts.

## Pull-request checks

`quality.yml` runs:

- Composer metadata validation;
- PHP syntax across the plugin and theme;
- WordPress Coding Standards;
- PHP compatibility checks;
- JavaScript syntax checks;
- JSON validation;
- WordPress integration tests against MySQL.

## Required branch protection

In GitHub repository settings, protect `main` with:

- Require a pull request before merging.
- Require approvals: 1 when another reviewer is available.
- Require status checks: `Static quality - PHP 7.4`, `8.1`, `8.3`, and `WordPress integration tests`.
- Require branches to be up to date before merging.
- Block force pushes and branch deletion.
- Require conversation resolution.
- Enable secret scanning and push protection when available.

## Release flow

1. Merge a tested pull request into `main`.
2. Update `CHANGELOG.md` and package version headers.
3. Run staging QA and a rollback check.
4. Create an annotated semantic version tag.
5. Push the tag.
6. The release workflow builds clean plugin and theme ZIPs, verifies them, generates SHA-256 checksums, and creates a GitHub release.

```bash
git checkout main
git pull --ff-only
git tag -a v1.0.1 -m "Maintenance release 1.0.1"
git push origin v1.0.1
```

## Security rules

- Actions are pinned to full commit SHAs.
- Checkout credentials are not persisted.
- Workflow permissions are minimal.
- User-controlled values are not written to `GITHUB_ENV`.
- Release artifacts are built without dependency caches.
- Secrets never enter the repository or release ZIPs.
- Dependabot proposes action and Composer updates; every update still passes CI before merge.

## Production deployment

Use a staging-first manual deployment until the hosting platform has a documented API, deployment user, atomic symlink strategy, health check, and rollback command. Do not automate FTP overwrites directly from `main`.

Recommended sequence:

1. Download the GitHub release ZIPs.
2. Verify `SHA256SUMS.txt`.
3. Back up database and `wp-content`.
4. Install on staging.
5. Run smoke tests and compare critical pages.
6. Deploy in a low-traffic window.
7. Purge caches.
8. Run post-deployment health checks.
9. Keep the previous package ready for rollback.
