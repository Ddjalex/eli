# Health & Nutrition Website - Eleni Mekuria

## Overview
A professional dietitian and nutritionist website for Eleni Mekuria, built with PHP and PostgreSQL. The site features meal plans, blog posts, portfolio, testimonials, and an admin dashboard for content management.

## Project Structure
```
health.neodigitalsolutions.com/
├── admin/                    # Admin dashboard pages
│   ├── index.php            # Admin home/dashboard
│   ├── manage_blogs.php     # Blog management
│   ├── manage_case_studies.php
│   ├── manage_credentials.php
│   ├── manage_hero.php      # Hero slider management
│   ├── manage_images.php    # Site images/settings
│   ├── manage_payments.php  # Payment options
│   ├── manage_plans.php     # Meal plans
│   ├── manage_portfolio.php
│   ├── manage_progress.php
│   ├── manage_testimonials.php
│   ├── manage_users.php
│   └── user_analytics.php
├── includes/
│   ├── config.php           # Database connection (PostgreSQL)
│   └── footer.php           # Shared footer component
├── user/                    # User portal pages
│   ├── dashboard.php
│   ├── logout.php
│   └── payment.php
├── uploads/                 # User uploaded files
├── attached_assets/         # Site assets and images
├── index.php               # Homepage
├── login.php               # User login
├── register.php            # User registration
├── blog.php                # Blog listing
├── blog-detail.php         # Single blog post
├── portfolio.php           # Portfolio page
└── service-detail.php      # Service details
```

## Tech Stack
- **Backend**: PHP 8.2
- **Database**: PostgreSQL (Replit Neon)
- **Frontend**: HTML, Tailwind CSS (CDN)
- **Server**: PHP built-in development server

## Database Tables
- `users` - User accounts and authentication
- `site_settings` - Key-value site configuration
- `hero_slides` - Homepage hero slider
- `meal_plans` - Subscription meal plans
- `payments` - Payment transactions
- `user_plan_access` - User plan subscriptions
- `progress_photos` - User progress tracking
- `testimonials` - Client testimonials
- `payment_options` - Available payment methods
- `case_studies` - Clinical case studies
- `credentials` - Professional credentials
- `portfolio_projects` - Portfolio items
- `about_slides` - About section images
- `user_analytics` - User health metrics
- `blogs` - Blog posts

## Default Admin Access
- Email: admin@example.com
- Password: password (hashed with bcrypt)

## Running the Project
The PHP development server runs on port 5000, serving from the `health.neodigitalsolutions.com` directory.

## Recent Changes
- January 2, 2026: Initial migration to Replit environment
  - Set up PostgreSQL database with all required tables
  - Fixed PHP syntax errors in footer.php (quote escaping)
  - Verified site is functional and accessible
