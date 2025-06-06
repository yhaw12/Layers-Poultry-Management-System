# 🐔 Poultry Farm Management System

A Laravel-based web application designed to manage and monitor poultry farm operations, including egg production, bird purchases, feed usage, employee payroll, and overall financial performance.

---

## 🚀 Features

- 📊 **Dashboard Overview** — Track profit, expenses, income, egg production, feed usage, and bird count.
- 🥚 **Egg Management** — Log production & sales with daily or monthly tracking.
- 🐥 **Bird Records** — Track chicks and hens, including purchases and mortality rate.
- 🌾 **Feed Management** — Record feed purchases and consumption.
- 💼 **Payroll Module** — Manage employees and monthly salary disbursements.
- 📈 **Reports** — Analyze trends based on date ranges.
- 🔐 **Role-Based Access** — Admin vs. regular user views.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2)
- **Frontend**: Blade + Tailwind CSS
- **Database**: MySQL

---

## 🧑‍💻 Installation

git clone https://github.com/yhaw12/Layers-Poultry-Management-System.git
cd poultry-farm-system
composer install
cp .env.example .env
php artisan key:generate
# Configure your database credentials in the .env file
php artisan migrate --seed
php artisan serve


📝 License
This project is open source and available under the MIT License.

🙌 Acknowledgements
Laravel
Tailwind CSS
Icons from Heroicons
Open source contributors
