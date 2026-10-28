# Local SkillShop and Club Shop

Start both apps from the SkillShop directory:

```sh
./run-local.sh
```

- Portal: http://localhost:8083/
- Product browsing: http://localhost:8083/shop
- Full Club Shop: http://localhost:8083/club-shop/
- Club Shop catalog API: http://localhost:8083/club-shop/api/products

The local launcher uses the existing project-local PHP 8.1 runtime, MySQL server on loopback port 33307, and two PHP workers. The worker on port 8082 handles Laravel's internal shop API requests, avoiding a single-worker nested HTTP deadlock. Both web listeners are loopback-only. Stop the launcher with Ctrl+C; the local database process remains available.

## Restored exports

The supplied `u291565911_devskillvation.sql` belongs to the Laravel portal (119 tables, 9 stored courses). `u291565911_devskillshop.sql` belongs to CodeIgniter Club Shop (103 tables, 92 stored products, 88 published through the catalog API, 18 categories).

Each restore creates new timestamped databases and separate restricted database accounts. It does not overwrite the original local databases. Private connection metadata and configuration backups are in ignored `.local/restored-connections.json` and `.local/backups/`. Environment files contain the generated local credentials; do not commit them.

To restore a different pair of exports:

```sh
.local/bin/php scripts/restore-local-databases.php /absolute/path/skillvation.sql /absolute/path/skillshop.sql
.local/bin/php scripts/configure-restored-local.php
.local/bin/php artisan optimize:clear
.local/bin/php artisan migrate --path=database/migrations/2026_10_24_000001_add_api_course_id_to_courses_table.php --force
./run-local.sh
```

Stop the current web launcher before restarting it. The database service must already be running for the restore command. Do not run all pending migrations blindly: the supplied export already contains several tables whose historical migrations are not recorded as applied. The guarded course-link migration is the only migration applied during this restoration.

The generated Club Shop database config comes from `scripts/templates/club-database.php`. Club Shop uses its own ignored `.env`. Its missing optional purchase-code configuration is handled as an empty string; the existing license comparison is unchanged. Purchase/license configuration remains the owner's responsibility.

The main portal now uses the local Club Shop API instead of the temporary remote catalog bridge. Its separate MySkool course API remains configured independently; the 9 locally stored courses and courses returned by MySkool are different data sources. Imported users/password hashes are retained. No accounts/passwords were reset and no orders or payments were submitted during verification. Local Laravel mail uses the log mailer.

SQL exports contain database records, not uploaded images/videos/documents. The Club Shop uploads directory was not provided, so media referenced in the restored database still needs the matching uploads backup. The portal's existing local test login may not exist in the restored accounts.

## Verify

With the apps running:

```sh
.local/bin/php scripts/check-local-setup.php
```

This performs read-only checks of page delivery, sign-in forms, private-file blocking, all catalog pages, product prices against the restored database, product details, and portal-to-shop API requests. It does not test a purchase or contact a payment provider.

## RoboQuest school handoff

The portal defaults to port 8083; set SKILLVATION_PORT to override the launcher port. See [school commerce setup](../RoboQuest_Learning_Lab/docs/SCHOOL_COMMERCE.md) for per-school keys, one-time launch, paid course assignment, test accounts and platform-owned Zoom events. Apply the new RoboQuest connection migration explicitly when updating a restored database. The local demo fixture is separate from real purchases and never charges a payment provider.
