# ACC Web

ACC Web is the web-interface family supporting the canonical ACC™ platform at `https://acc.onegodian.com`.

## Canonical production source

The current primary application shell and production frontend live in:

- Repository: `ohi-stack/acc`
- Domain: `https://acc.onegodian.com`
- ACC baseline: `v1.3.0`
- Oru’Valen™ surface: `https://acc.onegodian.com/oru`

This repository remains a compatible companion web-module family and must not diverge into a competing ACC implementation.

## Canonical role

ACC Web concerns presentation and operator interaction for surfaces such as:

- operational dashboard
- command center
- Oru’Valen™ operational-intelligence support
- OMOS™ integration status and outbound reasoning links
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

ACC Web does not create execution authority.

## Authority rule

```text
Authorized Human Judgment
→ Oru’Valen™ context and continuity
→ OMOS™ governed reasoning / Decision Record
→ Human gate
→ ACC™ authorized execution
→ agents / tools / adapters
→ verification + audit
```

Privileged actions must remain approval-gated and attributable.

## Oru’Valen and agent separation

Oru’Valen is the O-H-I Twin / operational intelligence layer. It is not classified as an external AI agent.

The ACC Agents registry remains for bounded agents, tools, workers, automations, and executors controlled through ACC.

## Route standard

Canonical ACC v1.3 Oru route:

- `/oru`

OMOS remains a separate governed reasoning platform at:

- `https://omos.onegodian.com`

Do not introduce a competing `/oruvalen` or local `/omos` production route unless the canonical `ohi-stack/acc` application is intentionally changed and versioned to support it.

## Repository status

`acc-web` is a **companion** repository. Before adding a production feature here, confirm that it does not belong in `ohi-stack/acc`, `ohi-stack/acc-oruvalen`, or another dedicated ACC service repository.

Synchronized to ACC platform `v1.3.0` architecture on September 16, 2026.
