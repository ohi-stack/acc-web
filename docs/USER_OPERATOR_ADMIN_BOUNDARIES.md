# User, Operator and Administrator Separation

This repository is companion frontend modules, not a second production ACC shell. Provide distinct operator and administrator navigation and view modules for the canonical ohi-stack/acc frontend. User personal data belongs to quantumohi-platform and tenant-scoped user APIs; admin data belongs to separately authorized ACC endpoints. Browser route guards are presentation only. Never bundle admin data or credentials in user views. Require server-side RBAC, tenant isolation and OCP approval. This document does not assert implemented UI.
