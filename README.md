# acc-web

Companion and historical web-interface repository for ACC™.

## Canonical production frontend

The canonical ACC production shell is now maintained in:

- Repository: `ohi-stack/acc`
- Production domain: `https://acc.onegodian.com`
- Oru’Valen™ route: `https://acc.onegodian.com/oru`
- Current ACC baseline: `v1.3.0`

Do not treat this repository as a competing production frontend unless a later architecture decision explicitly reactivates it.

## Current role

This repository may retain reusable web assets, documentation, WordPress bridge material, experiments, or migration references that support ACC. New production console work should target `ohi-stack/acc` unless the work is specifically scoped to a component maintained here.

## Canonical architecture

```text
Human authority
→ Oru’Valen™ continuity / operational intelligence
→ OMOS™ governed reasoning and Decision Records
→ Human gate
→ ACC™ authorized execution
→ agents / tools / integrations
→ measured outcome
```

Oru’Valen is an O-H-I Twin and is not classified as an external AI agent. External agents remain bounded executors registered and controlled through ACC.

## Repository rule

Before adding functionality here, confirm that it should not instead live in `ohi-stack/acc`, `ohi-stack/acc-oruvalen`, or another dedicated ACC service repository.
