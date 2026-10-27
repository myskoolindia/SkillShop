# Kalpana: standalone launch and JSON APIs

Run `./run.sh` in the `Kalpana/` folder using Node 22.13+. Open **http://localhost:8787/lab-planner**. Kalpana owns its application, Gemini configuration, catalog and SQLite database. RoboQuest is separate and no longer includes a Kalpana proxy or navigation links. `GET /api/health` checks the running service.

## Connect another application

1. As the Kalpana administrator, create a separate integration key for each consuming application:

   ```http
   POST /api/admin/integrations
   Authorization: Bearer <ADMIN_TOKEN>
   Content-Type: application/json

   {"name":"My school website"}
   ```

   Response `201`: `{ "client": { "id": "...", "name": "My school website", "enabled": true, "createdAt": "..." }, "apiKey": "..." }`. The key is returned once; only its hash is stored. Keep it on the consuming application's backend. It cannot administer documents, prices or other integrations. All integrations use this deployment's merchant/catalog; they are not separate merchants.

2. On a button click, the consuming backend calls:

   ```http
   POST /api/v1/sessions
   Authorization: Bearer <integration apiKey>
   Content-Type: application/json

   {"reference":"your-internal-school-or-request-id"}
   ```

   Response `201`:

   ```json
   {
     "sessionId": "generated-session-id",
     "reference": "your-internal-school-or-request-id",
     "launchUrl": "http://localhost:8787/lab-planner?session=generated-session-id#launch=single-use-secret",
     "launchExpiresAt": "ISO timestamp: ten minutes after creation",
     "expiresAt": "ISO timestamp: 24 hours after creation",
     "resultUrl": "http://localhost:8787/api/v1/sessions/generated-session-id/result",
     "quoteUrl": "http://localhost:8787/api/v1/sessions/generated-session-id/quote"
   }
   ```

   Store `sessionId` against the authenticated user/request in your application. Open `launchUrl` in a new tab. Kalpana exchanges the single-use fragment token for a browser conversation token, removes the fragment, and keeps the token in that tab's session storage. No integration key is sent to the browser. Keep `launchUrl` private until used. Do not prefetch it or share it with multiple users. Reloading the same tab retains its session. Reopening a consumed link in a different tab requires creating a new session.

3. Once the user generates the recommendation, your backend fetches **the full plan and quotation JSON**:

   ```http
   GET /api/v1/sessions/<sessionId>/result
   Authorization: Bearer <integration apiKey>
   ```

   Response `200` has `apiVersion: "v1"`, `sessionId`, `reference`, `amountUnit: "minor"`, and:

   | Status | plan / quotation | Meaning |
   | --- | --- | --- |
   | `pending` | `null` | No complete plan generated yet |
   | `ready` | Full JSON objects | Current, valid plan and quotation |
   | `needs_refresh` | `null` | Requirements changed, quote expired, or catalog changed/expired; `reason` explains |
   | `expired` | `null` | Session reached its 24-hour lifetime |

   Poll only while needed (e.g. every ten seconds, stop on ready/expired). Integration calls are limited to 60/minute per client; direct generation is additionally limited to 15/minute. General POST requests are limited to 120/minute per IP. Status `ready` means a current plan exists; inspect `quotation.purchasable`, planning observations and `ai.status` before presenting a purchase action. Editing or regenerating a plan changes the latest result. There is no payment charge or provider checkout in these APIs.

4. For **only the itemized quotation**, including exactly the line amounts displayed in the UI:

   ```http
   GET /api/v1/sessions/<sessionId>/quote
   Authorization: Bearer <integration apiKey>
   ```

   Response `200`: `{ "apiVersion":"v1", "sessionId":"...", "reference":"...", "amountUnit":"minor", "quotation": { ... } }`. Returns `409` if the result is not ready. The full result always includes the same quotation at `plan.quote` and `quotation`.

## Generate the output directly, without opening the UI

`GET /api/v1/options` with the integration key returns the current board/setup/skill IDs, live courses, tiers and catalog revision. Use those IDs; do not hard-code sample catalog IDs in a live integration.

```http
POST /api/v1/plans
Authorization: Bearer <integration apiKey>
Content-Type: application/json

{
  "reference": "your-request-id",
  "profile": {
    "board": "CBSE",
    "setup": "infrastructure_materials",
    "layout": "single",
    "rooms": [{"areaSqFt":600}],
    "classes": [6,7,8,9,10],
    "totalStudents": 120,
    "batchSize": 30,
    "skillIds": ["<current skill ID>","<current skill ID>","<current skill ID>"],
    "tier": "premium",
    "budgetMinor": null,
    "goals": "Hands-on skills for our school",
    "readiness": {},
    "optionalProductIds": [],
    "courses": []
  }
}
```

Response `201` combines the session creation response and the `ready` result, including the full `plan`, `quotation`, and a launch link to review/edit it. Allow up to 60 seconds for Gemini. Optional courses are `{ "productId": "<options.courses productId>", "participants": 20 }` (up to 12 distinct courses). A generation request creates a new session; it is not an idempotent purchase call.

`plan` contains `profile`, `assessment`, `schedule`, `requiredItems`, `items`, `optionalUpgrades`, courses, `ai` (status/explanation/next steps), `demoCatalog`, and `quote`. The quotation includes `id`, `expiresAt`, `catalogRevision`, `currency`, `lines`, `subtotal`, `tax`, `delivery`, `total`, `purchasable`, budget/requirement observations, and source documents. Each line has `productId`, `name`, `quantity`, `unitAmount`, `lineAmount`, `tax` and source references. Amounts are integer **minor units**: INR `45407800` means ₹454,078.00. Format by dividing by 100; use integers for calculations. Taxes are separate and follow the approved catalog. Quotes last 30 minutes and become invalid when the requirements or catalog change. `ai.status` distinguishes Gemini responses from explicitly labeled unavailable/not-configured responses; calculations still use approved catalog prices.

## Runnable button example and Postman

Create an ignored `.env.integration` file in `Kalpana/`, with permissions `600`:

```dotenv
KALPANA_URL=http://localhost:8787
INTEGRATION_API_KEY=<the integration key returned by the administrator>
```

Run with Node 22.13+:

```sh
node --env-file=.env.integration examples/integration/server.mjs
```

Open **http://localhost:9091**. Click **Open Kalpana**, generate a plan in the new tab, then click **Fetch plan and quotation JSON** in the original tab. The example keeps its key on the backend and maps each browser's cookie to its own Kalpana session. Adapt these routes to your application's existing authentication and persistent session storage for deployment.

Import `postman/Kalpana_Integration.postman_collection.json`. Set the collection variables `baseUrl` and `adminToken`; run create client, options, create session, result. The collection stores the integration key/session ID locally when those requests succeed. For direct generation, update the profile's skill IDs using the options response. Clear secrets before exporting or sharing a used collection.

## Access, revocation and hosting

`GET /api/admin/integrations` lists client metadata without keys. `POST /api/admin/integrations/<clientId>/revoke` disables a client and expires its linked conversations and launch links. Integration A cannot retrieve Integration B's sessions (`404`). Missing/invalid/revoked credentials return `401`; invalid JSON/profile returns `400` (or `415` for missing JSON content type), rate limiting `429`, no published catalog `503`. JSON errors have `{ "error": "description" }`.

For another computer, host Kalpana at a reachable HTTPS origin and set `PUBLIC_URL` to that origin. Set `HOST=0.0.0.0` when listening beyond localhost and configure your reverse proxy/firewall. `PUBLIC_URL` controls absolute launch and result URLs; it must be an origin, with no path/query/fragment. This API is designed for server-to-server calls: browser CORS calls with integration keys are intentionally unsupported. A normal button opening a new tab needs no CORS configuration. If embedding instead, add the consuming site's exact origin to `EMBED_ORIGINS` and allow microphone only if needed. Localhost URLs work on this computer; they are not public hosting.
