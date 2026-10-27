# Kalpana database

This is an independent SQLite database. `src/database.mjs` is the executable schema. No school/student records are read or modified.

| Table | Key fields | Purpose |
| --- | --- | --- |
| documents | id PK, title, content, sha256, mime_type, created_at | Immutable source text and extraction metadata |
| catalogs | revision PK, digest, payload, created_at | Immutable approved pricing/configuration snapshots |
| settings | key PK, value | Active catalog revision |
| conversations | id PK, token_hash, expires_at, state JSON, created_at | Private customer sessions and current requirements |
| messages | id PK, conversation_id FK, role, payload JSON, created_at | Recent dialogue with advice, sources and quote references |
| quotes | id PK, conversation_id FK, catalog_revision FK, payload JSON, expires_at, created_at | Server-calculated quote snapshot and requirement checks |
| orders | id PK, conversation_id FK, quote_id UNIQUE FK, status, customer JSON, provider_reference, checkout_url, timestamps | Confirmed purchase requests or demo simulations |
| audit | id PK, action, target, detail JSON, created_at | Document/catalog publication and purchase actions |

The orders.quote_id unique constraint prevents duplicate purchase requests for the same quote. Ownership checks bind each quote/order to its conversation token. Catalog and source snapshots support traceability. provider_reference and checkout_url are reserved for the later payment-provider integration and stay null today.

The schema stores contact details, not card details. Current order states are requested, demo_pending, and demo_completed. None is evidence of a real payment.
