# Diet & Nutritionist Eleni - Portfolio & Meal Planning Platform

## Overview
A PHP-based dietitian website for Eleni featuring personalized meal plans, blog, portfolio, user portal, and admin dashboard.

## Project Architecture
- **Language**: PHP 8.2
- **Database**: PostgreSQL (Replit native)
- **Frontend**: TailwindCSS (CDN), GSAP animations
- **Server**: PHP built-in development server on port 5000

## Key Files
- `index.php` - Main landing page with hero, about, services sections
- `login.php`, `register.php` - User authentication
- `portfolio.php`, `blog.php` - Content pages
- `admin/` - Admin dashboard for managing content, users, payments
- `includes/config.php` - Database configuration (uses Replit PostgreSQL env vars)

## Database Tables
- users, site_settings, hero_slides, about_slides
- meal_plans, payment_options, payments
- blogs, portfolio_projects, progress_photos
- user_meal_plans, user_downloads

## Default Admin Credentials
- Email: admin@example.com
- Password: password (hashed with bcrypt)

## Running the Project
The PHP development server runs on port 5000:
```
php -S 0.0.0.0:5000
```

## Recent Changes
- January 2, 2026: Migrated to Replit environment, created PostgreSQL database schema