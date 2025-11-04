# Kampus Susu Bergoyang Information System

This is a web-based information system for Kampus Susu Bergoyang (Pijil, Sumogawe, Getasan Subdistrict, Semarang Regency, Central Java, Indonesia), designed to manage product catalogs, handle booking schedules, and provide business information to visitors.

This project was developed based on the Software Requirements Specification (SRS) by Giska Claudia Yani, Adellia Pujaningtyas, and Alfa Jalu Wicaksono.

The main user-facing application is `tes.php`, which acts as a single-page interface for users to browse products, schedules, and contact information.

# Core Features
1. Product Management: An admin panel (`admin.php`) allows for full CRUD (Create, Read, Update, Delete) operations on dairy products.
2. Booking System: Users can book reservations for the "joglo" (traditional building) for educational activities via a form on `tes.php`.
3. Dynamic Schedule: The booking form dynamically fetches available dates and time slots from the `available_schedules` table.
4. Admin Dashboard: A secure login area (`admin.php`) for managing products, available booking slots, and viewing new reservations.
5. WhatsApp Integration: Product ordering (`produk.php`) and booking confirmations (`booking.php`) are handled by redirecting users to WhatsApp.

# Technology Stack
Backend: PHP
Database: MySQL
Frontend: HTML, CSS, JavaScript

# Getting Started: Installation & Setup

This project requires a PHP server environment (like XAMPP, WAMP, or MAMP) and a MySQL database.

1. Clone the repository:
    ```sh
    git clone [YOUR_REPOSITORY_URL]
    cd [YOUR_PROJECT_FOLDER]
    ```

2. Database Setup:
    - In your MySQL admin tool (like phpMyAdmin), create a new database named `kampus_susu`.
    - Import the `schema.sql` file into your `kampus_susu` database. This will create all 7 required tables: `admin_users`, `available_schedules`, `bookings`, `contacts`, `maps`, `products`, and `schedules`.
    - This will also import the initial admin user.

3. Configure Environment: This project's database credentials are stored in config.php. This file is not included in the repository for security.

- Find the file named config.example.php.
- Create a copy of it and rename the copy to config.php.
- Open config.php and fill in the DB_USER and DB_PASS values to match your local MySQL setup
- Content for config.example.php: (This is your config.php file but with placeholder values.)

4. Running the Application:
- Place the project folder in your web server's root directory (e.g., `htdocs` for XAMPP).
- Access the main site at: `http://localhost/[YOUR_PROJECT_FOLDER]/tes.php`
- Access the admin panel at: `http://localhost/[YOUR_PROJECT_FOLDER]/admin.php`


# Project File Structure

* `tes.php`: Main application file. This is the public-facing, single-page website.
* `admin.php`: Admin dashboard for managing products, bookings, and schedules.
* `config.php`: Contains all database connection settings.
* `schema.sql`: The database structure file used for installation.
* `booking.php`: Server-side script that handles booking form submissions from `tes.php`.
* `get_available_times.php`: API endpoint called by JavaScript on `tes.php` to find open booking slots.
* `produk.php`: A standalone page that displays the product catalog.
* `logout.php`: Destroys the admin session and logs the admin out.
* `SRS - kampus susu bergoyang.pdf`: The full academic Software Requirements Specification document.
* `check_availability.php`: **(Obsolete)**. This file appears to be unused. The correct booking logic is in `get_available_times.php` and `booking.php`, which use the `available_schedules` table, not `check_availability.php`.