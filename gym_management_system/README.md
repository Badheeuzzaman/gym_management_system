# FlexFit Gym Management System - PHP + MySQL

Complete Gym Management System with 40+ modules 

## Features Overview (Exactly as Requested)

**Main**
- Dashboard - Analytics, Revenue, Attendance, Expiring Memberships

**Members**
- Members (CRUD + Search + Status)
- Leads / Trials (Lead pipeline + Convert to Member)
- Assign Workout (Assign workouts to members)
- Birthdays (Today / Month filter)
- Attendance (Daily attendance with %)
- QR Scanner (HTML5 QR scanner for check-in)
- Biometric Device (ZKTeco integration page with config)
- Memberships (Plan assignment, Days left calculation)

**Finance**
- Payments (Income tracking)
- Expenses (Expense tracking)
- Day End Closing (Daily opening/closing cash)
- Cash & Bank (Transfers between accounts with balance update)
- Bank Accounts (Cash, Bank, Online accounts)
- Closing Checklist (Daily tasks with progress %)

**Store**
- New Sale (POS) - Product grid, cart, discount, checkout
- Items (Store inventory)
- Sales History (Invoice + Items)
- Payment Approvals (Online payment verification)

**Gym**
- Trainers (Profiles, clients count, salary)
- Classes (Time slot, capacity, enrollment bar)
- Workouts (Workout library)
- Diet Templates (Meal templates JSON)
- Plans (Membership plans with features)

**Inventory & Assets**
- Inventory (Qty, Min stock, Supplier)
- Suppliers (Contact management)
- Equipment (Purchase, maintenance, status)

**Tools**
- Reports (Income vs Expense charts, Analytics)
- Reminders (Follow-ups, renewals)
- SMS Center (Bulk SMS, templates, logs - mock)

**Staff & HR**
- Staff (Staff directory)
- My Store Account (Staff sales stats)
- Salary & Payroll (Deductions, Bonus, Net Pay)
- Shift Timings (Start/End)
- Duty Roster (Staff × Shift × Date)
- Staff Attendance (Check-in/out, hours)
- Bonuses (Rewards)

**Admin**
- Form Builder (Custom fields for forms)
- Settings (Gym info, SMS API, ZKTeco IP)
- Help & Docs (Guide + Integration steps)
- Sign Out

## Tech Stack
- PHP 8+ (PDO, no framework)
- MySQL (schema in sql/schema.sql)
- Bootstrap 5.3, FontAwesome 6, Chart.js
- HTML5 QR Code library for scanner
- Modern dark sidebar + light content UI

## Installation

1. **Database**
   ```sql
   - Create database gym_management
   - Import sql/schema.sql
   -- Or auto-created by config/database.php on first load
   ```

2. **Configure DB**
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'gym_management');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Run**
   - Place folder in `htdocs` / `www` or `php -S localhost:8000`
   - Visit `http://localhost/gym_management_system/`
   - Login: `admin / password`

## Default Login
- Username: `admin`
- Email: `admin@gym.com`
- Password: `password` (hashed in DB)

## File Structure
```
gym_management_system/
├── index.php (Login)
├── dashboard.php
├── logout.php
├── config/
│   ├── database.php (PDO + auto-create DB)
│   └── init.php
├── includes/
│   ├── header.php
│   ├── sidebar.php (All 40+ menu links)
│   ├── footer.php
│   ├── auth.php
│   └── functions.php
├── assets/
│   ├── css/style.css (Modern UI)
│   └── js/main.js
├── sql/
│   └── schema.sql (All tables + dummy data)
└── admin/
    ├── members.php
    ├── leads.php
    ├── assign_workout.php
    ├── birthdays.php
    ├── attendance.php
    ├── attendance_scan.php (QR)
    ├── zkteco.php (Biometric)
    ├── memberships.php
    ├── payments.php
    ├── expenses.php
    ├── day_end.php
    ├── transfers.php
    ├── banks.php
    ├── closing_checklist.php
    ├── store_pos.php (Full POS)
    ├── store.php
    ├── store_sales.php
    ├── pos_payments.php
    ├── trainers.php
    ├── classes.php
    ├── workouts.php
    ├── diet_templates.php
    ├── plans.php
    ├── inventory.php
    ├── inventory_suppliers.php
    ├── equipment.php
    ├── reports.php
    ├── reminders.php
    ├── sms.php
    ├── staff.php
    ├── my_store_account.php
    ├── staff_salary.php
    ├── staff_shifts.php
    ├── staff_duty.php
    ├── staff_attendance.php
    ├── staff_bonus.php
    ├── form_fields.php
    ├── settings.php
    └── help.php
```

## Screenshots Flow
- Login → Dashboard (stats + charts + recent payments + expiring)
- Members → Add / Edit / Search
- QR Scanner → Live check-in simulation
- POS → Add to cart → Checkout → Sales History
- Finance → Income/Expense tracking with Day Closing
- All modules included and interlinked

## Future Improvements
- Real ZKTeco SDK integration (using php-zklib)
- Member mobile app API
- SMS Gateway integration (Twilio / local provider)
- PDF Invoices
- QR Member Cards printable

## License
For educational / single gym commercial use.

Built with ❤️ for modern gyms.
