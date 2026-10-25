# ACC Web user/admin isolation specification

Target host: quantumohi.com. Status: specification; implementation and deployment must be verified.

## Frontend boundaries
User routes: /dashboard, /dashboard/tasks, /dashboard/workflows, /dashboard/usage, /dashboard/settings.
Admin routes: /admin, /admin/governance, /admin/policies, /admin/approvals, /admin/runtime, /admin/audit, /admin/tenants, /admin/integrations.

## Data access
User APIs: /api/me/* and /api/tenants/:tenantId/* with membership and resource ownership checks. Admin APIs: /api/admin/* with explicit elevated scopes. Enforce authorization on the server for every request, not just client route guards. Use separate server-side data loaders, query keys, cache partitions, and access-controlled websocket channels. No admin secrets, unredacted system-wide telemetry, cross-tenant records, or privileged policy data in user HTML, hydration state, bundles, or API responses.

## UX requirements
Distinct navigation, dashboard KPIs, empty/error states, and permission-denied pages. Admin area must not be a conditional tab in a user dashboard. Accessible keyboard navigation and session expiry handling. No fabricated live metrics or green service indicators.

## Tests required before release
Unauthenticated access; user direct navigation to /admin; user direct admin API calls; cross-tenant ID substitution; privilege revocation; stale cache after account switch; admin step-up; logout; responsive browser smoke tests.

## Release
Deploy to staging first. Validate actual DNS, hosting source, and environment configuration before changing quantumohi.com. Retain acc.onegodian.com until rollback and redirect tests pass.
