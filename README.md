# BUS PASS MANAGEMENT SYSTEM

A complete, secure, modern web-based **Student Bus Pass Management System** built with **PHP 8+ (Procedural MySQLi)**, **MySQL**, **Bootstrap 5**, and **JavaScript**.

---

## 📌 Project Overview
The Bus Pass Management System replaces manual paper-based bus pass issuance with a digital workflow:
- **Students** can register, apply for transit passes across active routes and fleet buses, track application status (Pending, Approved, Rejected), view their digital pass, and print it with a dedicated print layout.
- **Administrators** can review student applications, approve/reject passes with remarks, view student academic profiles, and manage fleet buses and transit routes.

---

## 🛠️ Technology Stack
- **Frontend:** HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons, JavaScript (Vanilla ES6)
- **Backend:** PHP 8+ (Procedural PHP with MySQLi & Prepared Statements)
- **Database:** MySQL / MariaDB (UTF-8 / `utf8mb4`)
- **Server:** XAMPP (Apache + MySQL)
- **Development Tool:** Visual Studio Code

---

## 📁 Project Folder Structure

```text
bus_pass/
│
├── index.php                 # Public Landing Page & Features
├── login.php                 # Student / User Login
├── register.php              # Student Registration Form
├── logout.php                # Student Logout Handler
├── dashboard.php             # Student Dashboard (Stats & Actions)
├── apply_pass.php            # Apply for a Bus Pass
├── my_applications.php       # Student Application History & Status
├── my_pass.php               # Digital Bus Pass & Print Layout
├── profile.php               # Student Profile View & Edit
├── README.md                 # Complete Setup & Documentation Guide
│
├── config/
│   └── db.php                # Central MySQLi Database Connection
│
├── includes/
│   ├── header.php            # Global Header, CSS, & Flash Alerts
│   ├── navbar.php            # Dynamic Role-Based Navigation Bar
│   ├── footer.php            # Global Footer & Scripts
│   └── auth.php              # Role Authentication Helpers
│
├── admin/
│   ├── login.php             # Admin Authentication
│   ├── logout.php            # Admin Logout Handler
│   ├── dashboard.php         # Admin Dashboard (Analytics & Metrics)
│   ├── students.php          # Student Registry Management
│   ├── view_student.php      # Student Details & History View
│   ├── applications.php      # Pass Applications Management
│   ├── view_application.php  # Detailed Application Review
│   ├── approve_application.php # Pass Approval Action Handler
│   ├── reject_application.php  # Pass Rejection Action Handler
│   ├── buses.php             # Fleet Bus Management
│   ├── add_bus.php           # Add Fleet Bus Form
│   ├── edit_bus.php          # Edit Fleet Bus Form
│   ├── delete_bus.php        # Delete / Deactivate Bus Handler
│   ├── routes.php            # Route Management
│   ├── add_route.php         # Add Transit Route Form
│   ├── edit_route.php        # Edit Transit Route Form
│   ├── delete_route.php      # Safe Route Deletion Handler
│   └── includes/
│       ├── header.php        # Admin Console Header
│       ├── sidebar.php       # Admin Sidebar Navigation
│       └── footer.php        # Admin Console Footer
│
├── css/
│   └── style.css             # Custom Responsive Styles & Print CSS
│
├── js/
│   └── script.js             # Form Validations & Client Helpers
│
└── database/
    └── bus_pass_db.sql       # Database Schema & Seed Data
```

---

## ⚙️ Quick Setup Instructions (XAMPP)

Follow these simple steps to run the application on your computer:

### STEP 1: Open XAMPP Control Panel
Launch the **XAMPP Control Panel** from your Start menu or desktop shortcut.

### STEP 2: Start Apache & MySQL
- Click the **Start** button next to **Apache**.
- Click the **Start** button next to **MySQL**.
- Both modules should now show green status indicators.

### STEP 3: Place Project Files in XAMPP `htdocs`
Copy the entire `bus_pass` folder into your XAMPP web root directory:
```
C:\xampp\htdocs\bus_pass
```

### STEP 4: Open phpMyAdmin
Open Google Chrome and visit:
```
http://localhost/phpmyadmin/
```

### STEP 5: Import the Database
1. In phpMyAdmin, click on the **Import** tab at the top.
2. Click **Choose File** and select:
   ```
   C:\xampp\htdocs\bus_pass\database\bus_pass_db.sql
   ```
3. Click the **Import** button at the bottom.
4. Verify that the database `bus_pass_db` is created with tables:
   - `users`
   - `students`
   - `buses`
   - `routes`
   - `bus_pass_applications`

### STEP 6: Open the Application in Your Browser
Visit the following URL in Google Chrome:
```
http://localhost/bus_pass/
```

---

## 🔑 Default Login Credentials

### 🛡️ Administrator Account:
- **URL:** `http://localhost/bus_pass/admin/login.php`
- **Email:** `admin@buspass.com`
- **Password:** `Admin@123`

### 🎓 Sample Student Account:
You can register a new student account at:
- **URL:** `http://localhost/bus_pass/register.php`
- Or use any newly registered email and password on `http://localhost/bus_pass/login.php`.

---

## 🔄 User Workflows

### 🎓 Student Workflow:
1. **Register:** Go to `register.php`, fill in personal, academic, and contact details.
2. **Login:** Login with your email and password at `login.php`.
3. **Dashboard:** View summary cards and stats for your passes.
4. **Apply Pass:** Select Route, Bus, and Duration (Daily, Monthly, Quarterly, Semester) on `apply_pass.php`.
5. **Track Status:** View `my_applications.php` to monitor approval or remarks from the administrator.
6. **Digital Pass:** Once approved, view your official digital ID card on `my_pass.php` and click **Print Pass** to print a clean paper copy.

### 🛡️ Administrator Workflow:
1. **Login:** Navigate to `admin/login.php` with admin credentials.
2. **Dashboard:** Monitor total students, pending reviews, approved passes, buses, and routes.
3. **Applications:** Navigate to **Pass Applications** (`admin/applications.php`) to filter and review submissions.
4. **Approve / Reject:** Click **View** on an application to inspect the student's details, then Approve or Reject with remarks.
5. **Manage Fleet:** Add, edit, or deactivate transit buses under **Manage Buses**.
6. **Manage Routes:** Add, update, or remove routes and set fare rates under **Manage Routes**.

---

## 🔒 Security & Best Practices Implemented
- **Password Hashing:** Passwords are encrypted using PHP's native `password_hash()` (BCrypt).
- **SQL Injection Prevention:** All dynamic queries use MySQLi Prepared Statements (`mysqli_prepare`, `mysqli_stmt_bind_param`).
- **Database Transactions:** Student registration uses ACID transactions (`mysqli_begin_transaction`, `mysqli_commit`, `mysqli_rollback`) to prevent orphaned records.
- **Role-Based Authorization:** Strict session-based access control stops students from entering administrative panels and prevents unauthorized users from performing status updates.
- **XSS Protection:** User inputs rendered to HTML are escaped using `htmlspecialchars()`.
- **Referential Integrity:** Safe soft-deactivation is used for buses that are already referenced by student application histories.

---

## ❓ Common Errors & Troubleshooting

### 1. `Database Connection Error / Can't connect to MySQL server`
- **Cause:** MySQL is not running in XAMPP.
- **Solution:** Open XAMPP Control Panel and click **Start** next to MySQL.

### 2. `Table 'bus_pass_db.users' doesn't exist`
- **Cause:** The database SQL file has not been imported yet.
- **Solution:** Open `http://localhost/phpmyadmin/`, click **Import**, select `database/bus_pass_db.sql`, and click **Import**.

### 3. `Access Denied for user 'root'@'localhost'`
- **Cause:** Your local MySQL has a root password set.
- **Solution:** Open `config/db.php` and update `$password = "your_mysql_password";`.

---

## 📄 License
This project is built for institutional transit management and academic learning. Free to customize and extend.
