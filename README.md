## Installation Process

- open project and install the project using installer

## Kalpana (Design My Lab)

The lab planner behind **Design My Lab** lives in [`kalpana/`](kalpana/README.md). It is a separate Node service (Node 22.13+) on `127.0.0.1:8787`, served by SkillShop at `/kalpana/`:

- Local: `./run-local.sh` starts it with the shop and logs to `.local/logs/kalpana.log`; `scripts/local-router.php` forwards `/kalpana/` to it.
- Apache: `.htaccess` and `public/.htaccess` proxy `/kalpana/` when `mod_proxy` and `mod_proxy_http` are enabled (or use `ProxyPass /kalpana/ http://127.0.0.1:8787/` in the vhost). Run `kalpana/run.sh` as a service (systemd or similar).
- Configure `kalpana/.env` (`PUBLIC_URL=https://<shop>/kalpana/`, Gemini key, admin token) and keep `LABDESIGN_API_BASE` / club-shop `kalpana.baseURL` pointing at `http://127.0.0.1:8787`.
