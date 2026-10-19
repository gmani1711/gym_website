# FitLife Gym - Gym Management Website

A full-stack gym management system built with PHP, MySQL, HTML, CSS, and JavaScript. Handles member registration, attendance tracking, workout/health logging, and an admin dashboard.

## Features

- Member registration with membership plans, emergency contact details, and medical info
- Login/session-based authentication with hashed passwords
- Member dashboard: attendance history, BMI/health tracking, workout logging, class booking
- Admin API for managing members and viewing activity
- Contact and feedback forms with database storage
- Responsive UI (Bootstrap 5, Font Awesome, Animate.css)

## Tech Stack

- **Frontend:** HTML, CSS, JavaScript, Bootstrap 5, Chart.js
- **Backend:** PHP (mysqli, prepared statements)
- **Database:** MySQL

## Setup

1. Create a MySQL database named `gym_website`.
2. Update credentials in `backend/config.php` if your local MySQL setup differs from the defaults (`root` / no password).
3. Serve the project root with PHP's built-in server:
   ```bash
   php -S localhost:8000
   ```
4. Visit `http://localhost:8000/index.html`. Tables are created automatically on first run.

## Project Structure

```
├── index.html, membership.html, about.php, ...   # Pages
├── assets/css, assets/js                         # Static assets
├── backend/
│   ├── config.php        # DB connection + shared helpers
│   ├── database.php      # DatabaseManager (singleton connection)
│   ├── register.php      # Member registration
│   ├── login.php / logout.php
│   ├── track.php         # Attendance/workout tracking
│   ├── admin_api.php     # Admin-only JSON endpoints
│   └── contact.php, feedback.php
```

## Notes

- Passwords are hashed with `password_hash()`; all DB queries use prepared statements to prevent SQL injection.
- Default DB credentials in `config.php` are for local development only - change them before deploying anywhere public.
