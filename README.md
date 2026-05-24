# WORKUPX

Professional investment platform with customer dashboard, admin panel, referral growth logic, and copy-trading simulation workflows for **WORKUPX.COM**.

## Stack
- Frontend: HTML5, CSS3, Vanilla JavaScript
- Backend: PHP 8+
- Database: MySQL
- No framework

## Highlights
- Premium dark neon/glassmorphism responsive UI
- Secure authentication (hashed passwords, CSRF, brute-force lock)
- Educational/simulated copy-trade signals with admin package targeting, one-time usage, and expiry controls
- Manual deposit flow (USDT TRC20 / USDT BEP20 + screenshot + WhatsApp confirmation)
- Withdrawal requests with 20% fee calculation, admin approval, and status tracking
- Referral links, count-based earning boost (+0.5% per referral), and salary milestone tracking
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
   - Or set environment variables: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_CHARSET`
   - Set `WHATSAPP_SUPPORT` to your real support number URL (the default is a placeholder).
4. Serve project with PHP (example):
   ```bash
   php -S 127.0.0.1:8000
   ```
5. Open `http://127.0.0.1:8000`

## Seed Admin
- Admin login page: `/admin/login.php`
- Create your first admin user after import:
  ```sql
  INSERT INTO users (full_name, email, phone, password_hash, role, referral_code, package_name)
  VALUES ('Super Admin', 'admin@workupx.com', '+10000000000', '<PASSWORD_HASH>', 'admin', 'WORKUPXADMIN', 'diamond');
  ```
  Replace the example phone with your real admin contact number.
  Generate `<PASSWORD_HASH>` with:
  ```bash
  php -r "echo password_hash('YOUR_STRONG_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
  ```

## Security Notes
- Uses PDO prepared statements throughout
- Escaping helper for output
- CSRF token validation for forms
- Session hardening and remember-me token hashing
- Admin route protection with role checks

## Important Transparency
WORKUPX is not a brokerage and does not guarantee profits.
All trade outcomes are **Estimated Educational Results** only.
