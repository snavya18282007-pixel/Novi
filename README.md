# NOVI – Your Personal Study Companion

> **NOVI** is a modern, lightweight, and responsive study management application designed for students to organize subjects, manage tasks, track deadlines, monitor daily goals, and analyze study productivity.

---

## 📌 Project Objective

The primary objective of **NOVI** is to empower students with an intuitive, distraction-free study companion that bridges task management with productivity tracking. Unlike generic task managers, NOVI combines **subject-wise progress metrics**, **daily goal targets**, **overdue task detection**, and **weekly productivity stats** into a single clean dashboard without external framework bloat.

---

## 🚀 Key Features

* **Dashboard Command Center**:
  * Real-time metrics: Total, Completed, Pending, and Overdue tasks, total subjects, and overall completion %.
  * **NOVI Companion Greetings**: Dynamic advice based on current study progress and overdue alerts.
  * **Today's Focus List**: Direct task toggles and instant priority badges.
* **Smart Task Management**:
  * Full CRUD (Create, Read, Update, Delete) capabilities.
  * Quick status toggle (Pending $\leftrightarrow$ Completed).
  * Auto-overdue detection (`due_date < today AND status != Completed`).
  * Advanced filtering (All, Today, Upcoming, Pending, Overdue, Completed).
  * Real-time keyword search and multi-field sorting (Due Date, Priority, Status, Title).
* **Subject Organization**:
  * Subject-wise task counters and visual progress bars.
  * Custom color tagging for subjects.
  * Cascade management for linked tasks.
* **Daily Study Goals**:
  * Customizable daily target tasks tracker (e.g., `6 / 10` tasks completed today).
  * Visual progress indicator.
* **Productivity Visualizations**:
  * Pure CSS weekly productivity bar chart (Monday to Sunday completion trends).

---

## 🛠️ Technology Stack

* **Language**: PHP 8.2+
* **Database**: MySQL / MariaDB
* **Database Driver**: PHP PDO (PHP Data Objects) with prepared statements
* **Frontend**: HTML5, CSS3 (CSS Grid & Flexbox), Vanilla JavaScript
* **Environment**: XAMPP / WAMP / LAMP stack (No Composer or external frameworks required)

---

## 📂 Project Architecture

NOVI follows a lightweight, MVC-inspired modular architecture for maximum functionality and clean separation of concerns:

```text
Novi/
│
├── config/
│   └── database.php         # PDO connection & security helpers
│
├── app/
│   ├── controllers/
│   │   ├── DashboardController.php   # Dashboard metrics & NOVI greetings
│   │   ├── TaskController.php        # Task CRUD, filters, and status toggling
│   │   └── SubjectController.php     # Subject CRUD & progress metrics
│   │
│   ├── models/
│   │   ├── Task.php                  # Task data queries, search, & weekly stats
│   │   └── Subject.php               # Subject data queries & progress math
│   │
│   └── views/
│       ├── layout.php                # Master HTML layout & global modal dialogs
│       ├── dashboard.php             # Main dashboard view
│       ├── tasks.php                 # Task management view
│       └── subjects.php              # Subject overview grid view
│
├── public/
│   ├── index.php             # Public front controller & router
│   └── assets/
│       ├── style.css         # Modern design system stylesheet
│       └── script.js         # Vanilla JS modal & interactive controls
│
├── database/
│   └── novi.sql              # Database schema & demo data
│
├── index.php                 # Root entry point forwarding to public/index.php
└── README.md                 # Documentation
```

---

## 💻 XAMPP Setup & Database Instructions

### 1. Place Project Files
Ensure the project is located in your XAMPP `htdocs` directory:
```text
C:\xampp\htdocs\Novi
```

### 2. Start XAMPP Services
Open the **XAMPP Control Panel** and start:
* **Apache**
* **MySQL**

### 3. Import Database

#### Option A: Using phpMyAdmin (Web UI)
1. Open [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/) in your web browser.
2. Click **Import** in the top navigation bar.
3. Choose file: `C:\xampp\htdocs\Novi\database\novi.sql`.
4. Click **Import** at the bottom.

#### Option B: Using Command Prompt / Terminal
Run the following command in your terminal:
```bash
C:\xampp\mysql\bin\mysql.exe -u root < C:\xampp\htdocs\Novi\database\novi.sql
```

---

## 🌐 Running the Application

Open your browser and navigate to:

```text
http://localhost/Novi/
```

*(Alternatively: `http://localhost/Novi/public/`)*

---

## 🔮 Future Scope

1. **Pomodoro Study Timer**: Built-in 25-minute focus timer with sound alerts and session logging.
2. **Study Analytics Export**: Export study stats and completed tasks summary to PDF/CSV for academic reporting.
3. **Task Reminders & Web Notifications**: Browser notifications when tasks are due within 1 hour.
4. **Dark Mode Toggle**: Built-in toggle switch for dark mode preference.

---

## 📄 License & Credits

Developed as a college project submission for Web Development.  
Designed and built with clean code practices, security principles, and user-centric UI.
