# Saddam Hossen Site Assistant

## Guided mode

Guided mode is enabled by default and needs no third-party API. Quick actions and keyword routing run in the browser and guide visitors to relevant work, engineering details, articles and contact options.

## AI mode

AI mode sends a visitor question to a nonce-protected WordPress AJAX action. The server validates the 500-character limit, applies a short-lived hashed visitor rate limit and builds an instruction grounded in the site’s published content. The API key is never localized into browser JavaScript.

Configure under **Saddam Engineer → Assistant**:

- Mode: `ai`
- Provider: `openai` or `webhook`
- Model: an available Responses API model
- API key: stored server-side

For stronger secret management, define `SPCORE_OPENAI_API_KEY` and optionally `SPCORE_OPENAI_MODEL` in server configuration or `wp-config.php`; constants take precedence over database settings.

## Privacy and limitations

The site does not store assistant conversations. Guided responses remain in the browser. AI questions are sent to the configured provider, so the privacy policy must identify that provider and its processing terms. The system instruction rejects invented employers, metrics, testimonials and outcomes, but generated answers still require monitoring.
