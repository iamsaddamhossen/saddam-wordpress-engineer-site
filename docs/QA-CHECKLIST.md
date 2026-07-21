# Production QA Checklist

## Installation and data

- [ ] Plugin activates without fatal errors.
- [ ] Theme activates without fatal errors.
- [ ] Starter importer succeeds twice without duplicates.
- [ ] Permalinks resolve Home, Engineering, Experience, Projects and Case Studies.
- [ ] Existing user-created content remains intact.
- [ ] v3 demonstration claims are no longer public.

## Engineering evidence

- [ ] `/wp-json/spcore/v1/engineering` returns 200 while logged out.
- [ ] `/wp-json/spcore/v1/projects` validates pagination limits.
- [ ] `/wp-json/spcore/v1/health` rejects logged-out visitors.
- [ ] Engineering Proof block inserts and renders on the frontend.
- [ ] `wp spcore status`, cache clear and cache warm run successfully.
- [ ] Site Health shows custom tests and debug information.

## Contact and privacy

- [ ] SMTP test succeeds.
- [ ] Valid AJAX submission stores a private lead and table row.
- [ ] No-JavaScript submission redirects with a success status.
- [ ] Invalid nonce, invalid email and short message fail safely.
- [ ] Rate limit triggers after the configured test sequence.
- [ ] CAPTCHA succeeds and its API failure returns a clear fallback.
- [ ] Webhook reaches only the expected HTTPS endpoint and signature validates.
- [ ] IP remains empty when storage is disabled.
- [ ] Exporter returns the test lead.
- [ ] Eraser anonymizes both table and private post records.
- [ ] Retention task removes records older than the configured period.

## Frontend and accessibility

- [ ] Skip link is visible on keyboard focus.
- [ ] Header dropdown and mobile navigation work by keyboard.
- [ ] Escape closes the mobile navigation and optional modal.
- [ ] Every form field has a label and errors are announced.
- [ ] Heading order is logical.
- [ ] Focus states are visible.
- [ ] Reduced-motion preference removes animations.
- [ ] Site works with JavaScript disabled.
- [ ] No horizontal scroll at 320px.
- [ ] Check 375px, 768px, 1024px and 1440px layouts.
- [ ] Images have appropriate alternative text.

## Performance and compatibility

- [ ] No external animation or font dependency blocks rendering.
- [ ] Browser console has no errors.
- [ ] PHP error log has no warnings/notices during key flows.
- [ ] Caching does not serve stale project data after an update.
- [ ] Test current WordPress, supported PHP minimum and current PHP.
- [ ] Run Lighthouse as a diagnostic, not as an unsupported public claim.

## Repository quality

- [ ] PHP syntax check passes.
- [ ] JSON files parse.
- [ ] JavaScript syntax check passes.
- [ ] PHPCS and PHP compatibility pass after dependency installation.
- [ ] WordPress PHPUnit suite passes with a test database.
- [ ] GitHub Actions pass on a pull request.
- [ ] No secrets, production credentials, client exports or private data are committed.
