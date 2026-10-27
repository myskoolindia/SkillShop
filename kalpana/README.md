# Kalpana

> **Inside SkillShop.** This folder is part of SkillShop. Kalpana still runs as its own Node service (Node 22.13+, port 8787), and SkillShop serves it under its own address at `/kalpana/` — locally through `scripts/local-kalpana-proxy.php`, on Apache through the `/kalpana/` proxy rules in `.htaccess` and `public/.htaccess` (`mod_proxy` + `mod_proxy_http`), or a vhost `ProxyPass /kalpana/ http://127.0.0.1:8787/`. SkillShop's `./run-local.sh` starts Kalpana together with the shop. Set `PUBLIC_URL` to the SkillShop address including the prefix (for example `https://shop.example/kalpana/`) so launch links open there. SkillShop's own backends keep calling Kalpana directly on `http://127.0.0.1:8787` (`LABDESIGN_API_BASE`, club-shop `kalpana.baseURL`). Running it stand-alone on port 8787 still works.

Kalpana is a separate, Gemini-based product and package advisor. It reads approved product documents, understands a customer's budget and learner count, compares compatible packages/add-ons, creates itemized quotes, and records a confirmed purchase request. It has its own HTTP API, SQLite database, customer studio, document administration page and embeddable widget.

It does not depend on, modify or connect to RoboQuest school accounts or student records. Run it independently and attach it to a product website when needed.

**Current purchase scope:** the payment provider has not been selected. Normal mode saves purchase requests for the team. Demo mode includes a simulated checkout. Neither mode collects money or marks a real order as paid. Live direct purchase needs a payment-provider integration after you choose one.

## Run immediately without a key

On this Linux/macOS workspace:

```sh
cd SkillShop/kalpana
./run.sh --demo
```

Open **http://localhost:8787/lab-planner** for the school lab planner, or **http://localhost:8787** for the package studio. The launcher selects a compatible installed Node version from PATH or NVM; it has no dependency on the RoboQuest folder. This avoids the machine's default Node 16. On another computer, install Node **22.13+** first; no npm dependencies are required.

`--demo` explicitly selects a deterministic sample conversation engine, seeds fictional product/pricing documents and enables simulated checkout. It does **not** call Gemini. The UI labels both the demo engine and sample pricing. It is never used as a silent fallback when Gemini fails.

Try:

> I need robotics and AI for 30 students under INR 40,000.

The sample shortlist should include Robotics Classroom plus the AI add-on for **INR 31,000**. Customize and review it, provide a test name/email, confirm, then complete the simulation. Its status becomes `demo_completed`, never `paid`.

The three sample core packages are Digital Foundations, Robotics Classroom and Complete Innovation Lab. Their fictional pricing is in `examples/pricing.md`; product rules are in `examples/products.md`. Demo tax is configured as zero and does not represent the tax treatment of your business.

## Use your Gemini API key

A local `.env` has been prepared with a generated administrator token, an empty Gemini key and `SEED_DEMO=true`. It is ignored by Git. If starting from a fresh copy, copy `.env.example` to `.env`, generate a random administrator token, and set `SEED_DEMO=true` while testing with sample documents.

Edit `.env` locally:

```env
GEMINI_API_KEY=your-key-here
GEMINI_MODEL=gemini-3.8-flash
AI_MODE=gemini
CHECKOUT_MODE=request
SEED_DEMO=true
```

Use a Gemini model enabled for your project; `GEMINI_MODEL` is configurable. The default follows the model used in the current Google API reference and may not be enabled for every account. Restart after changes. Do not paste the key into a chat message or client JavaScript.

Run without the demo flag:

```sh
./run.sh
```

With Node 22.13+ already on your PATH, `npm start` and `npm run demo` are equivalent alternatives. On Windows, use those npm commands after installing a compatible Node release.

The studio should say **Powered by Gemini**. Sample catalog labels remain until you publish your real catalog. Gemini requires internet connectivity; Kalpana is separate from RoboQuest's offline classroom service.

The key stays on the server. The generateContent REST request sends approved catalog data, selected source excerpts, the recent conversation and the user's request to Gemini. Customer name/email from purchase forms are not included in the model prompt. Do not attach confidential internal documents to a customer-facing catalog: referenced excerpts are visible to customers.

## Documents and approved prices

Open **http://localhost:8787/admin** and enter `ADMIN_TOKEN` from `.env`. The admin page keeps it in page memory, not browser storage.

1. Upload product or pricing text, Markdown, JSON text, or a PDF up to 1 MB. You can also paste text up to 60,000 characters. PDF text is extracted using Gemini and must be reviewed. Export DOCX/XLSX to PDF or text before upload; direct binary DOCX/XLSX parsing is not implemented.
2. Review the saved text and copy its source ID. Sources are immutable: upload a new ID for a changed document.
3. Load the sample catalog template or current catalog into the editor.
4. Replace product IDs, names, prices, learner capacity, included features, compatible add-ons, taxes, delivery and source IDs. Amounts are integers in minor currency units: INR 12,000 = `1200000`.
5. Set `demo: false` only when you have reviewed real commercial terms. Set an expiry in `validUntil`, choose a new `revision`, then click **Approve & publish catalog**.

Example catalog product:

```json
{
  "id": "robotics-classroom",
  "name": "Robotics Classroom",
  "kind": "package",
  "unitAmount": 2500000,
  "capacity": 30,
  "features": ["coding", "robotics", "offline", "training"],
  "sourceIds": ["your-product-document-id", "your-price-document-id"],
  "active": true
}
```

An add-on uses `kind: "addon"`, `compatibleWith: ["robotics-classroom"]`, and its own price/features/source IDs. Add-on prices are per core-package unit, not per learner. This first version supports one core package per quote, up to 100 units, and up to eight add-on types. The recommendation engine considers every compatible add-on combination and selects the cheapest valid configuration per core package, then presents up to three packages.

Catalog-level fields are `revision`, `title`, `demo`, `currency`, `taxBasisPoints`, `deliveryAmount`, `validUntil`, and `products`. Supported currencies are INR/USD/EUR/GBP; currency conversion is not implemented. Prices are one-time; recurring subscriptions, arbitrary discounts, negotiated package component removal, inventory reservations and fulfillment are not implemented. Unsupported requirements need team review.

Tax is rounded once from `subtotal × taxBasisPoints / 10000`. Total is subtotal + configured tax + delivery. This is a configurable calculation, not automatic tax determination. A business with different tax/shipping rules should extend the calculator before selling those offers.

Uploading a document does **not** automatically approve a new sellable price. Only explicitly published catalog entries can be quoted. Draft documents absent from the current catalog are excluded from customer retrieval. Changing document text requires a new immutable source; changing a price requires a new immutable catalog revision.

## Attach Kalpana elsewhere

Set the host website's exact origin in `.env`, then restart:

```env
EMBED_ORIGINS=https://your-product.example https://another-product.example
```

Add this to that website:

```html
<script
  src="https://kalpana.your-domain.example/widget.js"
  data-kalpana-url="https://kalpana.your-domain.example"
  defer>
</script>
```

The floating button opens an isolated Kalpana iframe. Chat credentials and administrator keys are never passed to the parent site. The host site's Content Security Policy must allow the widget script and iframe domain; browser storage restrictions may affect session restoration in third-party iframes. Alternatively embed `https://kalpana.your-domain.example/?embed=1` yourself in an iframe or link to the standalone studio.

Host the service behind HTTPS for remote use. The default server binds to localhost only. `EMBED_ORIGINS` controls permitted iframe parents; it does not enable cross-origin REST calls. Backend integrations can call the API with their own conversation tokens. Use one deployment/database per independent merchant; multiple isolated business tenants in one database are not implemented.

## How recommendations and purchases work

```text
Attached documents ── admin review ── approved catalog revision
                                            │
Customer request ── retrieval ── Gemini structured requirements
                                            │
                         deterministic package/price calculator
                                            │
                    sourced, expiring, itemized shortlist/quote
                                            │
                         explicit customer review + confirmation
                                            │
                   purchase request OR clearly labeled demo checkout
```

Gemini interprets the conversation, maps needs to approved features, answers from attached sources, and asks clarifying questions. It cannot set a checkout amount or trigger a purchase. The server validates model product/source IDs. Numerical or price-like model prose is replaced; computed amounts appear in verified quote fields. Document excerpts are treated as data, not system instructions. Grounding reduces unsupported claims but does not guarantee every language-model statement is correct; the approved quote is authoritative.

Quote creation checks product availability, package quantity, learner capacity, add-on compatibility, requested feature coverage, budget, tax, delivery and source references. Over-budget alternatives are shown as alternatives and cannot be ordered until constraints change. No quote can proceed if custom requirements need review.

Quotes expire after 30 minutes. A new catalog revision or changed confirmed requirements invalidates a previous quote for a new purchase action. Server-side prices override any client line-price suggestions; checkout rejects client total/amount fields. Retrying a confirmed request for the same quote/customer returns the same order instead of duplicating it.

`src/checkout.mjs` is the payment integration boundary. Current states are `requested`, `demo_pending`, `demo_completed`. There is deliberately no live `paid` transition. Once a provider is selected, add provider checkout creation, retry/reconciliation handling, signed webhook verification, amount/currency matching, transaction IDs and fulfillment hooks. Never treat a browser redirect as proof of payment.

## External system integration

Kalpana runs as an independent application. Copy the entire `Kalpana/` folder to deploy it; RoboQuest no longer includes Kalpana links or a proxy. Other systems connect directly using the integration API.

Use the versioned launch, plan and quotation APIs in [docs/INTEGRATION.md](docs/INTEGRATION.md). Import [postman/Kalpana_Integration.postman_collection.json](postman/Kalpana_Integration.postman_collection.json). A working button integration is included in `examples/integration/`; it keeps the integration key on the caller's backend. Session results are persisted in Kalpana's own database.

## API and Postman

Import `postman/Kalpana.postman_collection.json` and `postman/Kalpana_Local.postman_environment.json`. Select **Kalpana · Local**, fill `adminToken` from `.env`, then run folders 00–05 in order. The collection contains **24 requests**, assumes the sample catalog, and works in demo mode. The demo completion request automatically skips itself in request-only mode. Session tokens and generated IDs are saved to collection variables; clear them before exporting a used collection.

Customer APIs use `Authorization: Bearer <conversation token>`. `POST /api/conversations` creates a private 24-hour session. Admin APIs use `ADMIN_TOKEN` instead. Keys are never included in query strings.

| Method | Endpoint | Purpose |
| --- | --- | --- |
| GET | `/api/health` | AI mode, key-configured flag, checkout mode, catalog revision |
| GET | `/api/catalog` | Approved products, prices and source references |
| POST | `/api/conversations` | Create conversation and receive bearer token |
| GET | `/api/conversation` | Restore recent messages and confirmed requirements |
| POST | `/api/chat` | `{message, budgetMinor?, learners?}` → grounded advice and calculated quotes |
| POST | `/api/quotes` | `{items:[{productId,quantity}]}` → customized verified quote |
| GET | `/api/quotes/:id` | Read an owned valid quote |
| POST | `/api/orders` | `{quoteId,customer:{name,email},confirm:true}` → purchase request |
| GET | `/api/orders/:id` | Read an owned request and quote |
| POST | `/api/orders/:id/demo-complete` | Explicit demo only; `{confirm:true}` |
| GET | `/api/admin/documents` | List source metadata |
| POST | `/api/admin/documents` | Attach `{title,content,id?}` or `{title,pdfBase64,id?}` |
| GET | `/api/admin/documents/:id` | Review full extracted source text |
| GET | `/api/admin/catalog` | Read current approved catalog |
| POST | `/api/admin/catalog` | Validate and publish catalog JSON |
| GET | `/api/admin/sample-catalog` | Read sample template |
| GET | `/api/admin/orders` | Inspect recent purchase requests |

Browser mutations must be same-origin. Public conversation creation and chat have basic in-process rate limits. These are a first deployment safeguard, not a distributed abuse-management service. A hosted public deployment should configure appropriate edge rate limits and customer authentication if long-lived order access is needed.

## Storage and testing

`data/kalpana.sqlite` contains documents, immutable catalog revisions, conversations, messages, quote snapshots, purchase requests and audit entries. It does not use the RoboQuest school or master databases. Only session token hashes are stored. Raw user messages and customer contact details are stored for the service's workflow; define retention and deletion procedures before public rollout. To take a simple local backup, stop the service and copy the entire data directory. Environment credentials need a separate protected backup.

Run with Node 22.13+:

```sh
npm test
```

The tests cover actual SQLite persistence and HTTP requests, budget calculations, add-on compatibility, feature coverage, source isolation, expiry/revision handling, tampering, session ownership, duplicate purchases and explicit simulation states. Gemini transport tests inject controlled responses; they do not validate a real API key or incur live model usage. Missing keys or provider errors return explicit errors and never silently switch to demo mode.

Full browser UI automation is not included in the local checks. Use the studio and admin page to validate the interaction in your browser. There is no frontend dependency install or build step.

Implementation references:

- [Gemini generateContent API](https://ai.google.dev/api/generate-content)
- [Gemini structured output](https://ai.google.dev/gemini-api/docs/structured-output)
- [Gemini models](https://ai.google.dev/gemini-api/docs/models)

## Products from Skillvation (live source)

With `CATALOG_SOURCE=skillvation` in `.env`, Kalpana takes its products from:

- **Skillvation Club Shop**, `https://devpro.skillvation.com/club-shop`: `/api/categories`, `/api/products` (all pages) and `/api/products/{id}` (bundle contents).
- **MySkool collab courses**, `https://collab.myskool.club`: S2S (`type=8&upskill=0`), U4T (`type=8&upskill=1`) and TTT (`type=10`).

A sync runs on startup and every `SKILLVATION_SYNC_MINUTES` (default 60). Each sync validates every response; a failed, partial or empty fetch is recorded and the current catalog stays active. A new immutable catalog revision is published only when products or prices change. With `SKILLVATION_AUTO_PUBLISH=false`, changes are staged for an administrator to publish from `/admin`. The shop and course listings are also stored as source documents, so answers cite them.

The lab page also exposes every active course before a plan is generated, with text search, S2S/U4T/TTT filters, fees and explicit participant counts. Equipment skill areas remain a separate selector. The options endpoint includes `courses`, `catalogRevision` and `syncedAt`. Courses selected before the school details are known remain in the draft and join the server-calculated quote once the missing details are supplied.

Live-source mode refuses to serve an old sample catalog while the first import is pending. Incomplete pagination and duplicate course IDs fail the import, preserving the last complete catalog. Browsers start a fresh conversation when restoring quotes from an older catalog revision; database history is retained.

How the data is used:

- **Prices.** API prices are whole rupees (the shop page shows ₹7,908 for `price: 7908`) and are stored as paise. `price_discounted` is used when present. GST comes per item from `vat_rate`, or from `cgst_rate + sgst_rate` (18% on the four Skill Lab bundles today). Bundles are quoted at their listed price; their components are shown, not re-priced.
- **Lab planner.** Each shop category with Basic/Advanced/Premium items becomes a skill area. Per area and active room, a plan has one Lab, plus `SKILLVATION_KITS_PER_ROOM` (default 4, the shop bundles' convention) Kit and Consumable sets. CBSE plans also include tiered mandatory equipment (except materials-only). Every plan adds curriculum & training and installation & transport. A tier the shop lacks falls back to the nearest one, and the plan says so. Results also compare the shop's ready-made bundles and list matching courses.
- **Package studio.** It recommends ready-made bundles, custom tier builds for the skill areas mentioned, and courses (priced per participant, multiplied by learners).
- **Work categories.** Skill areas are mapped to CBSE work categories by name (agriculture and food → life forms; healthcare, beauty, media and retail → human services; the rest → machines & materials). This is a planning default in `src/skillvation.mjs`.

`/admin` shows sync status, counts and **data checks**: issues found in the shop data, such as bundle prices that differ from their components, tier price inversions, identical tier prices and duplicate items. Admin APIs: `GET /api/admin/skillvation`, `POST /api/admin/skillvation/sync`, `POST /api/admin/skillvation/publish`.

## Autopilot and intelligent studio

**Lab autopilot.** On `/lab-planner`, describe the school in plain language (typed or spoken), for example *"CBSE school, classes 6 to 10, 400 students, one 650 sq ft room, robotics and AI, budget 1.5 lakh"*. Kalpana extracts the profile, shows what it understood as chips (dashed chips are assumptions), asks only for missing essentials (board, student strength, room area), then generates the plan and verified quote automatically. Follow-ups such as "materials only", "two rooms of 450 and 420 sq ft" or "without horticulture" redesign the plan. Room areas, student counts and budgets are never invented. For CBSE, missing work categories are filled in and the change is stated. The step-by-step form remains under **Fine-tune every detail yourself** and stays in sync.

`POST /api/lab-planner/interpret` takes `{message, draft?}` and returns `{reply, draft, understood, notes, missing, questions, plan?}`. Gemini only proposes fields. `src/lab-intake.mjs` sanitizes every value, and the deterministic planner/calculator produces the plan. Demo mode uses a visible regex parser, never as a silent Gemini fallback.

**Package studio.** It shows AI-suggested next messages as one-click chips, a *What Kalpana understood* panel (manual inputs are under *Adjust manually*), a **Best match** badge with reasons computed from verified quote fields, staged progress while thinking, and voice input where the browser supports speech recognition.

**Live mascot and diagrams.** `public/mascot.js` draws an animated robot that blinks, follows the pointer and reacts to state (listening, thinking, asking, happy, sad). Click it for tips. `public/diagrams.js` draws a scaled floor plan with stations and the CBSE minimum outline, an enrolment → batches → stations → rooms flow, a cost-breakdown donut and budget bars. All figures come from the verified plan and quote. Motion is disabled under `prefers-reduced-motion`.

**Without a Gemini key**, the lab autopilot uses the built-in local parser and is labelled *Local autopilot*. Package chat still requires a key, by design.

## RoboQuest lab planner integration

Open `/lab-planner` for the new four-step **Make Your Own Lab** flow. It collects school board, setup/room configuration, classes, total strength, batch size, courses, goals, readiness and budget. The output includes versioned planning observations, required items, optional upgrades, equipment quantities and an itemized quote.

The sample catalog is now `demo-v2-labs`. If you already used `demo-v1`, stop the service, run `./run.sh --upgrade-sample`, then restart. This adds a new sample source/catalog revision without replacing existing documents, conversations or orders. The administrator can instead review and publish a real catalog with a validated `labPlanner` mapping. Special lab products use `salesChannel: "lab_planner"` and cannot be quoted directly without reviewed planner inputs.

Kalpana is no longer proxied by RoboQuest. It lives inside SkillShop (`SkillShop/kalpana`) and powers SkillShop's **Design My Lab** popup.

The CBSE space checks are sourced; the three-course category minimum is the user's business planning baseline and is labeled accordingly. Non-CBSE boards do not inherit CBSE checks. These are planning observations, not formal compliance certification. Site and curriculum review remain necessary.
