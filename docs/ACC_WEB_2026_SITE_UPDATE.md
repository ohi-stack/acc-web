# ACC Web 2026 Site Update

Canonical site: `https://acc.onegodian.com`

## Purpose

`acc-web` is the operator-facing web interface for ACC™. It should present the unified OHI Command Console and Agent Command Console as one control-plane experience.

## Primary navigation

1. Dashboard
2. Agents
3. Tasks
4. Workflows
5. Plugins
6. Sites
7. Audit
8. Settings

## Required page set

| Page | Purpose | 2026 status |
|---|---|---|
| `/` | ACC landing and status summary | setup |
| `/dashboard` | Operator dashboard | setup |
| `/agents` | Agent registry and capability status | setup |
| `/tasks` | Task queue and task health | setup |
| `/workflows` | Workflow registry and workflow run status | setup |
| `/plugins` | WordPress/plugin health across all OneGodian properties | setup |
| `/sites` | Site and node manifest status | setup |
| `/omos` | OMOS node integration | setup |
| `/app` | app.OneGodian.com integration | setup |
| `/capital` | capital.OneGodian.com node + plugin integration | setup |
| `/galaxy` | galaxy.OneGodian.com plugin/runtime integration | setup |
| `/quantumohi` | QuantumOHI.com platform plugin integration | setup |
| `/audit` | Audit log and approval history | setup |
| `/settings` | Environment and integration status | setup |

## Required connected properties

- `app.OneGodian.com`
- `OMOS.OneGodian.com`
- `OneGodian.org`
- `OneGodian.com`
- `u.OneGodian.com`
- `galaxy.OneGodian.com`
- `capital.OneGodian.com`
- `QuantumOHI.com`
- `QRV.Network`

## Plugin status screen requirement

The `/plugins` page must show every configured property with the following four-state health model:

- Registered
- Working
- Connected
- Error

Each row/card should include:

- Site
- Plugin name
- Plugin version
- Shortcodes registered
- REST namespace
- Last successful check
- Errors
- Production status

## Site dashboard cards

Each connected property card should display:

- Domain
- Repository
- Runtime type
- Public role
- Integration type
- Health endpoint
- Manifest endpoint
- Current version
- Current status
- Next required action

## Compliance language

Use this site-wide note where appropriate:

> ACC™ is an operator-facing software control plane for coordinating approved agents, workflows, integrations, and system status. It does not replace human authority, legal review, financial compliance, or governance instruments.

## Version rule

> If it is not fully operational, documented, and repeatable, it does not exist in the current version.

## Immediate next build

1. Add shared `sites` and `plugins` data files.
2. Add `/sites` and `/plugins` pages.
3. Add manifest cards for App, OMOS, Capital, Galaxy, QuantumOHI, OneGodian.org, OneGodian.com, U OneGodian, and QRV.
4. Add non-sensitive API routes for site/plugin status.
5. Add smoke tests.
6. Add deployment documentation for `acc.onegodian.com`.
