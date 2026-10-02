# NOKSHI — Clothing e-commerce (Laravel 11)

Dhaka-born clothing storefront with COD + SSLCommerz, bilingual EN/BN, and a full custom admin panel.

**Tagline:** Wear the story.

## Stack

- **Backend:** Laravel 11, MySQL (XAMPP), Spatie Permission, Intervention Image
- **Frontend:** Blade, Alpine.js, Tailwind CSS, Motion, Swiper, Chart.js
- **Payments:** Cash on Delivery + SSLCommerz (sandbox ready)
- **Locale:** English / বাংলা (`/locale/en`, `/locale/bn`)
- **Currency:** BDT (৳)

## Requirements

- PHP 8.2+, Composer, Node 18+
- MySQL / MariaDB (XAMPP works)
- Apache/Nginx or `php artisan serve`

## Setup

```bash
cd nokshi
composer install
cp .env.example .env   # or use the existing .env
php artisan key:generate

# Create DB `nokshi` in phpMyAdmin, then:
php artisan migrate:fresh --seed --force
php artisan storage:link

npm install
npm run build          # or: npm run dev

php artisan serve --host=127.0.0.1 --port=8000
```

Open http://127.0.0.1:8000

## Demo logins

| Role | Email | Password |
|------|-------|----------|
| Super admin | `admin@nokshi.test` | `password` |
| Manager | `manager@nokshi.test` | `password` |
| Customer | `customer@nokshi.test` | `password` |

- **Storefront account:** http://127.0.0.1:8000/login  
- **Admin panel:** http://127.0.0.1:8000/admin/login  

## Coupons (seeded)

- `WELCOME10` — 10% off (max ৳500), min ৳1,000  
- `EID500` — ৳500 off over ৳5,000  
- `FREESHIP` — free shipping over ৳1,500  

## SSLCommerz

Set in `.env`:

```
SSLCOMMERZ_STORE_ID=testbox
SSLCOMMERZ_STORE_PASSWORD=qwerty
SSLCOMMERZ_SANDBOX=true
SSLCOMMERZ_SUCCESS_URL="${APP_URL}/payment/sslcommerz/success"
SSLCOMMERZ_FAIL_URL="${APP_URL}/payment/sslcommerz/fail"
SSLCOMMERZ_CANCEL_URL="${APP_URL}/payment/sslcommerz/cancel"
SSLCOMMERZ_IPN_URL="${APP_URL}/payment/sslcommerz/ipn"
```

Sandbox credentials are defaults. Switch `SSLCOMMERZ_SANDBOX=false` and use live store ID/password for production. CSRF is exempted for SSLCommerz callbacks.

## Motion / animation

The storefront uses the **Motion** npm package (`motion`) for scroll reveals, staggered product grids, split headlines, and magnetic CTAs — wired in `resources/js/app.js`. Rebuild assets after JS/CSS changes (`npm run build`).

## Key URLs

| Page | Path |
|------|------|
| Home | `/` |
| Shop | `/shop` |
| Product | `/product/{slug}` |
| Cart / Checkout | `/cart`, `/checkout` |
| Track order | `/track` |
| Journal | `/blog` |
| Contact / FAQ / About | `/contact`, `/faq`, `/about` |
| Admin | `/admin` |

## Project layout

```
app/Http/Controllers/Shop|Account|Auth|Admin
app/Models, Services, Support/helpers.php
resources/views/shop|account|auth|admin
resources/js/app.js, admin.js
resources/css/app.css, admin.css
lang/bn.json
database/migrations, seeders
```

## Notes

- Guest cart merges into the user cart on login.
- Product images in seeders use Unsplash URLs (absolute `http` paths).
- Free delivery threshold and most site copy live in **Admin → Settings**.
- Bangla UI strings are in `lang/bn.json`; switch language from the header.

## License

Private project — all rights reserved.
