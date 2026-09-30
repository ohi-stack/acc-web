# ACC Web

ACC Web is the web-interface family supporting the canonical ACC™ platform at `https://acc.onegodian.com`.

## Canonical production source

The current primary application shell and production frontend live in:

- Repository: `ohi-stack/acc`
- Domain: `https://acc.onegodian.com`
- Production baseline: `v1.3.0`
- ACC V2 delegation foundation: `2.0.0-alpha.1` pre-release
- Oru’Valen™ surface: `https://acc.onegodian.com/oruvalen`
- OMOS™ surface inside ACC: `https://acc.onegodian.com/omos`

This repository remains a compatible companion web-module family and must not diverge into a competing ACC implementation.

## ACC V2 operator model

The primary operator experience is shifting from agent-centric navigation to an exception-driven command desk built around:

- Projects
- Persistent Responsibilities
- Work Orders
- Delegation
- Needs My Attention
- Running
- Blocked
- Completed
- Opportunities
- Approvals
- Connections
- Deployments
- Verification
- Audit

Canonical operational chain:

```text
Project
→ Responsibility
→ Work Order
→ Delegation
→ Authorized Provider / Executor
→ Approval
→ Verification
→ Deployment / Outcome
→ Audit
```

The web layer displays and manages these records; it does not create execution authority.

## Delegation surfaces

Canonical V2 foundation routes include:

- `/work-orders`
- `/responsibilities`
- `/delegation`

The canonical API exposes provider maturity so the UI can visibly distinguish executable, external, and reserved providers. A provider name must never be rendered as operational merely because it exists in the registry.

`openai-dot` is currently reserved and non-executable.

## Existing operator surfaces

- operational dashboard
- command center
- Oru’Valen™ operational-intelligence support
- OMOS™ integration status and runtime visibility
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

## Authority rule

```text
Authorized Human Judgment
→ Oru’Valen™ context and continuity
→ OMOS™ governed reasoning / Decision Record
→ Human gate
→ ACC™ Work Order and authorized execution
→ agents / tools / adapters / providers
→ verification + audit
```

Privileged actions must remain approval-gated and attributable.

## Oru’Valen and agent separation

Oru’Valen is the O-H-I Twin / operational intelligence layer. It is not classified as an external AI agent.

The ACC Agents registry remains for bounded agents, tools, workers, automations, and executors controlled through ACC.

## Repository status

`acc-web` is a **companion** repository. Before adding a production feature here, confirm that it does not belong in `ohi-stack/acc`, `ohi-stack/acc-oruvalen`, or another dedicated ACC service repository.

Production status requires separate deployment and runtime verification on `acc.onegodian.com`.
