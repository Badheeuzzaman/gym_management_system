-- FlexFit Gym Management System Schema
CREATE DATABASE IF NOT EXISTS gym_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gym_management;

-- Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','manager','staff') DEFAULT 'admin',
    full_name VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO users (id, username, email, password, role, full_name) VALUES
(1, 'admin', 'admin@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Super Admin');
-- password: password

-- Plans
CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    duration_days INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    features TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Trainers
CREATE TABLE IF NOT EXISTS trainers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(150),
    specialization VARCHAR(100),
    experience INT,
    salary DECIMAL(10,2),
    status ENUM('active','inactive') DEFAULT 'active',
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Members
CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    phone VARCHAR(20) NOT NULL,
    gender ENUM('male','female','other') DEFAULT 'male',
    dob DATE,
    join_date DATE NOT NULL,
    photo VARCHAR(255),
    status ENUM('active','inactive','expired','pending') DEFAULT 'active',
    address TEXT,
    emergency_contact VARCHAR(50),
    height DECIMAL(5,2),
    weight DECIMAL(5,2),
    goal VARCHAR(100),
    trainer_id INT,
    membership_status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE SET NULL
);


-- Memberships
CREATE TABLE IF NOT EXISTS memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('paid','pending','partial') DEFAULT 'paid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id)
);

-- Leads
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150),
    source VARCHAR(100),
    interested_plan VARCHAR(100),
    status ENUM('new','contacted','trial','converted','lost') DEFAULT 'new',
    followup_date DATE,
    assigned_to INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Workouts
CREATE TABLE IF NOT EXISTS workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    difficulty ENUM('beginner','intermediate','advanced') DEFAULT 'beginner',
    description TEXT,
    duration INT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Member Workouts
CREATE TABLE IF NOT EXISTS member_workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    workout_id INT NOT NULL,
    assigned_by INT,
    assigned_date DATE NOT NULL,
    notes TEXT,
    sets INT,
    reps INT,
    status ENUM('active','completed','pending') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (workout_id) REFERENCES workouts(id) ON DELETE CASCADE
);

-- Attendance
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    date DATE NOT NULL,
    check_in TIME,
    check_out TIME,
    method ENUM('qr','biometric','manual') DEFAULT 'manual',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member_date (member_id, date)
);

-- Payments
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    membership_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','card','bank','upi') DEFAULT 'cash',
    payment_date DATE NOT NULL,
    type ENUM('membership','store','personal_training','other') DEFAULT 'membership',
    status ENUM('completed','pending','failed') DEFAULT 'completed',
    transaction_id VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Expenses
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    expense_date DATE NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'cash',
    description TEXT,
    receipt VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bank Accounts
CREATE TABLE IF NOT EXISTS bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(150) NOT NULL,
    account_name VARCHAR(150) NOT NULL,
    account_number VARCHAR(100),
    balance DECIMAL(12,2) DEFAULT 0,
    type ENUM('cash','bank','online') DEFAULT 'bank',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transfers
CREATE TABLE IF NOT EXISTS transfers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_account INT NOT NULL,
    to_account INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transfer_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Day Closing
CREATE TABLE IF NOT EXISTS day_closing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    closing_date DATE UNIQUE NOT NULL,
    opening_cash DECIMAL(10,2) DEFAULT 0,
    total_income DECIMAL(10,2) DEFAULT 0,
    total_expense DECIMAL(10,2) DEFAULT 0,
    closing_cash DECIMAL(10,2) DEFAULT 0,
    closed_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Closing Checklist
CREATE TABLE IF NOT EXISTS closing_checklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task VARCHAR(255) NOT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    completed_at DATETIME,
    closing_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Store Items
CREATE TABLE IF NOT EXISTS store_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    sku VARCHAR(50) UNIQUE,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Store Sales
CREATE TABLE IF NOT EXISTS store_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    member_id INT,
    total DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    final_total DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'cash',
    sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS store_sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    item_id INT NOT NULL,
    qty INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES store_sales(id) ON DELETE CASCADE
);

-- POS Payments
CREATE TABLE IF NOT EXISTS pos_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(50),
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    approved_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Classes
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    trainer_id INT,
    time_slot VARCHAR(50),
    days VARCHAR(100),
    capacity INT,
    enrolled INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Diet Templates
CREATE TABLE IF NOT EXISTS diet_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    goal VARCHAR(100),
    description TEXT,
    meals TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inventory
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    contact VARCHAR(50),
    email VARCHAR(150),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    quantity INT DEFAULT 0,
    unit VARCHAR(50),
    min_stock INT DEFAULT 5,
    supplier_id INT,
    cost DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    purchase_date DATE,
    cost DECIMAL(10,2),
    status ENUM('working','maintenance','broken') DEFAULT 'working',
    last_maintenance DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Staff
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    role VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(150),
    salary DECIMAL(10,2),
    join_date DATE,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS staff_shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS duty_roster (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    shift_id INT NOT NULL,
    duty_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS staff_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    date DATE NOT NULL,
    check_in TIME,
    check_out TIME,
    status ENUM('present','absent','late','half_day') DEFAULT 'present',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bonuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reason TEXT,
    bonus_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS staff_salary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    month VARCHAR(20) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    deductions DECIMAL(10,2) DEFAULT 0,
    bonus DECIMAL(10,2) DEFAULT 0,
    net_pay DECIMAL(10,2) NOT NULL,
    status ENUM('paid','pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reminders
CREATE TABLE IF NOT EXISTS reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    member_id INT,
    reminder_date DATE NOT NULL,
    type VARCHAR(50),
    message TEXT,
    status ENUM('pending','sent','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sms_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('sent','failed','pending') DEFAULT 'sent',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS form_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_name VARCHAR(100) NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_type VARCHAR(50) NOT NULL,
    is_required BOOLEAN DEFAULT FALSE,
    options TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('gym_name', 'FlexFit Gym'),
('currency', '$'),
('gym_email', 'info@flexfit.com'),
('gym_phone', '+1 234 567 890'),
('gym_address', '123 Fitness Street, Colombo, Sri Lanka'),
('sms_api_key', ''),
('zkteco_ip', '192.168.1.201'),
('zkteco_port', '4370');

-- Dummy Data
INSERT IGNORE INTO plans (id, name, duration_days, price, description, status) VALUES
(1, 'Monthly Basic', 30, 49.99, 'Access to gym floor & cardio', 'active'),
(2, 'Quarterly Pro', 90, 129.99, 'Gym + Group Classes + 2 PT Sessions', 'active'),
(3, 'Yearly Elite', 365, 399.99, 'All access + Diet + Unlimited PT', 'active');

INSERT IGNORE INTO trainers (id, name, phone, email, specialization, experience, salary, status) VALUES
(1, 'John Carter', '0771234567', 'john@flexfit.com', 'Strength Training', 5, 800, 'active'),
(2, 'Sarah Lee', '0777654321', 'sarah@flexfit.com', 'Yoga & Pilates', 4, 750, 'active');

INSERT IGNORE INTO members (id, member_code, name, email, phone, gender, dob, join_date, status, goal) VALUES
(1, 'GYM00001', 'Alex Perera', 'alex@example.com', '0711111111', 'male', '1995-05-15', '2024-01-10', 'active', 'Weight Loss'),
(2, 'GYM00002', 'Nisha Fernando', 'nisha@example.com', '0722222222', 'female', '1998-07-20', '2024-02-12', 'active', 'Muscle Gain'),
(3, 'GYM00003', 'David Silva', 'david@example.com', '0733333333', 'male', '1990-12-10', '2024-03-01', 'active', 'Fitness');

INSERT IGNORE INTO bank_accounts (id, bank_name, account_name, account_number, balance, type) VALUES
(1, 'Cash Drawer', 'Main Cash', 'CASH-001', 2500, 'cash'),
(2, 'Commercial Bank', 'FlexFit Gym Account', '1234567890', 15000, 'bank');

INSERT IGNORE INTO store_items (id, name, category, price, stock, sku) VALUES
(1, 'Whey Protein 1kg', 'Supplement', 45.00, 25, 'SUP-001'),
(2, 'Gym Gloves', 'Accessories', 15.00, 40, 'ACC-002'),
(3, 'Energy Drink', 'Beverage', 2.50, 100, 'BEV-003');

INSERT IGNORE INTO workouts (id, name, category, difficulty, duration) VALUES
(1, 'Full Body Blast', 'Strength', 'intermediate', 60),
(2, 'HIIT Cardio', 'Cardio', 'advanced', 45),
(3, 'Yoga Flow', 'Flexibility', 'beginner', 60);

INSERT IGNORE INTO classes (id, name, trainer_id, time_slot, days, capacity, enrolled) VALUES
(1, 'Morning Yoga', 2, '06:00-07:00 AM', 'Mon,Wed,Fri', 20, 12),
(2, 'CrossFit WOD', 1, '05:00-06:00 PM', 'Mon,Tue,Thu,Fri', 15, 10);

INSERT IGNORE INTO staff (id, name, role, phone, email, salary, join_date, status) VALUES
(1, 'Kamal Admin', 'Manager', '0710000001', 'kamal@flexfit.com', 1200, '2023-01-01', 'active'),
(2, 'Priya Reception', 'Receptionist', '0710000002', 'priya@flexfit.com', 600, '2023-06-15', 'active');
