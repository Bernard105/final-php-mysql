
## Dev environment (quick)
1) Copy `.env.example` to `.env` and edit DB + SITE_URL
2) Run composer install (to enable PSR-4 autoload + helpers)
3) Point your server document root to `public/`

> The app will still run without `.env` (it falls back to `config/constants.php`).
