# ACC Web

ACC Web is the web-interface family for the canonical ACC™ platform at `https://acc.onegodian.com`.

## Canonical role

The primary user interface now includes or reserves surfaces for:

- operational dashboard
- command center
- Oru’Valen™ intelligence-twin support
- OMOS™ runtime integration
- agents
- tasks
- workflows
- Engineering Council
- models
- connections
- executions
- approvals
- deployments
- verification
- audit
- system health

ACC Web is presentation and operator interaction. It does not create execution authority.

## Authority rule

```text
Authorized Human Judgment
→ Oru’Valen / OMOS decision support
→ ACC Web
→ governed ACC API
→ OCP
→ OEG
→ agents / tools / adapters
→ verification + audit
```

Privileged actions must remain approval-gated and attributable.

## Source of truth

The current primary application shell and production source live in `ohi-stack/acc`. This repository remains a compatible web-module family and must not diverge into a competing ACC implementation.

## Canonical routes added in ACC v1.3.0

- `/oruvalen`
- `/omos`

Associated integration contracts reserve nested memory, decision, provenance, reference-run, provider, persistence, and manifest surfaces according to verified implementation status.

Synchronized to ACC platform `v1.3.0` architecture on September 16, 2026.
