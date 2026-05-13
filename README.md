# WORKUPX

Professional crypto community and referral investment education platform for **WORKUPX.COM**.

## Stack
- Frontend: HTML5, CSS3, Vanilla JavaScript
- Backend: PHP 8+
- Database: MySQL
- No framework

## Highlights
- Premium dark neon/glassmorphism responsive UI
- Secure authentication (hashed passwords, CSRF, brute-force lock)
- Educational/simulated trade signals with **admin-configurable estimated results**
- Manual deposit flow (USDT BEP20 / USDC + screenshot + WhatsApp confirmation)
- Withdrawal requests with admin approval and status tracking
- Referral links, counts, earnings, and reward milestones
- Admin panel for users, deposits, withdrawals, signals, announcements, and settings
- Legal pages: Privacy, Terms, Risk Disclaimer, AML, Contact

## Folder Structure
- `/assets` UI resources
- `/admin` super admin pages
- `/includes` shared auth/layout/utilities
- `/config` application config
- `/database` MySQL schema
- `/uploads` payment screenshot uploads

## Setup
1. Create MySQL database: `workupx`
2. Import schema: `database/schema.sql`
3. Configure DB credentials in `config/config.php`
4. Serve project with PHP (example):
   ```bash
   php -S 127.0.0.1:8000
   ```
5. Open `http://127.0.0.1:8000`

## Seed Admin
- Email: `admin@workupx.com`
- Password: `Workupx@Admin123`
- Admin login page: `/admin/login.php`

## Security Notes
- Uses PDO prepared statements throughout
- Escaping helper for output
- CSRF token validation for forms
- Session hardening and remember-me token hashing
- Admin route protection with role checks

## Important Transparency
WORKUPX is not a brokerage and does not guarantee profits.
All trade outcomes are **Estimated Educational Results** only.
