# GitHub Workflow

## 1. Create the repository

A clear repository name is:

```text
saddam-wordpress-engineer-site
```

Do not initialize it with generated files if this folder already contains the README and license.

## 2. Configure Git locally

```bash
git init
git branch -M main
git config user.name "Saddam Hossen"
git config user.email "YOUR_GITHUB_EMAIL"
git add .
git commit -m "chore: establish WordPress engineering site"
git remote add origin git@github.com:iamsaddamhossen/saddam-wordpress-engineer-site.git
git push -u origin main
```

Use an email connected to GitHub or the GitHub no-reply address so contributions appear on your profile.

## 3. Build history honestly

Do not create fake or backdated commits. Use small commits that explain the actual change:

```text
feat(rest): add project collection endpoint
feat(block): add dynamic engineering proof block
feat(privacy): integrate lead exporter and eraser
fix(contact): validate engagement allowlist
refactor(theme): simplify the full-viewport homepage hero
test(rest): cover health-route permissions
docs: add technical review path
```

## 4. Branch and pull-request flow

```bash
git checkout -b feat/site-health-mail-test
# edit and test
git add saddam-engineer-core/includes saddam-engineer-core/tests
git commit -m "feat(health): report missing mail configuration"
git push -u origin feat/site-health-mail-test
```

A useful pull-request description includes:

- problem and context;
- implementation summary;
- screenshots or endpoint examples;
- automated and manual tests;
- risks and rollback;
- follow-up work.

Even for a solo project, pull requests preserve decisions and make future maintenance easier.

## 5. Protect quality

In repository settings:

- require a pull request before merging into `main`;
- require the static-quality and WordPress-integration checks;
- block force pushes to `main`;
- enable Dependabot alerts and secret scanning when available;
- keep production credentials out of repository variables and files.

## 6. Create releases

After staging QA:

```bash
git checkout main
git pull --ff-only
git tag -a v1.0.0 -m "Initial public release 1.0.0"
git push origin v1.0.0
```

The release workflow builds separate install ZIPs for the plugin and theme.

## 7. Keep the repository useful

Pin the repository and add links to:

- the live site;
- `/engineering/`;
- the public REST engineering endpoint.

Keep the README, technical review guide, issues and pull requests current. Avoid filling the profile with unmodified tutorial clones.
