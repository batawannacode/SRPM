---
# 🚀 Laravel Project Installation Guide

This guide will help you **clone**, **install**, and **run** this
Laravel project on your local machine.
---

## 📌 **Requirements**

Before starting, make sure your system has the following installed:

### **Server Requirements**

-   **PHP 8.2+**
-   **Composer 2.x**
-   **MySQL 8 / MariaDB 10+**
-   **Node.js 18+**
-   **NPM 9+** or **PNPM / Yarn** (optional)
-   **Laravel CLI** (optional)
-   **Git**

### **PHP Extensions Required**

Laravel requires the following PHP extensions:

-   OpenSSL\
-   PDO\
-   Mbstring\
-   Tokenizer\
-   XML\
-   Ctype\
-   JSON\
-   BCMath\
-   Fileinfo\
-   GD (for image processing)

---

## 📥 **1. Clone the Repository**

```bash
git clone https://github.com/Ahadon13/SRPM.git
```

Go inside the project:

```bash
cd your-repo
```

---

## 📦 **2. Install PHP Dependencies**

```bash
composer install
```

---

## 📁 **3. Create Environment File**

```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

---

## 🗄️ **4. Configure Database**

Update `.env`:

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=srpm_db
    DB_USERNAME=your_username
    DB_PASSWORD=your_password

Create the database:

```sql
CREATE DATABASE your_database;
```

---

## 🧱 **5. Run Migrations & Seeders (if available)**

```bash
php artisan migrate
```

If you want a fake data, you can (Note: Not Accurate Data);

```bash
php artisan migrate:fresh --seed
```

---

## 📦 **6. Install Frontend Dependencies**

```bash
npm install
```

---

## ▶️ **7. Start the Laravel Server**

Open your git bash terminal on your project and run this;

```bash
./dev.sh
```

Your app will run at:

    http://127.0.0.1:8000

---

## 🎉 Done!

Your Laravel project is now successfully installed and running.
