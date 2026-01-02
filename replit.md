# Diet & Nutritionist Eleni - Portfolio & Meal Planning Platform

## Overview
A comprehensive PHP-based website for a professional dietitian/nutritionist featuring:
- Public-facing portfolio and blog
- User registration and authentication system
- Meal plan management and purchases
- Admin dashboard for content management
- Progress tracking with before/after photos

## Project Architecture

### Tech Stack
- **Backend**: PHP 8.2 with PDO for database access
- **Database**: PostgreSQL (Neon-backed via Replit)
- **Frontend**: HTML, Tailwind CSS (via CDN), GSAP animations
- **Server**: PHP built-in development server on port 5000

### Directory Structure
```
/
├── admin/                 # Admin panel pages
│   ├── index.php         # Admin dashboard
│   ├── manage_users.php  # User management
│   ├── manage_plans.php  # Meal plan CRUD
│   ├── manage_blogs.php  # Blog management
│   ├── manage_hero.php   # Hero slider management
│   └── ...               # Other admin features
├── includes/
│   ├── config.php        # Database configuration
│   └── footer.php        # Shared footer component
├── user/
│   └── dashboard.php     # User dashboard
├── attached_assets/      # Uploaded images and files
│   ├── generated_images/
│   └── stock_images/
├── index.php             # Homepage
├── login.php             # User login
├── register.php          # User registration
├── blog.php              # Blog listing
├── portfolio.php         # Portfolio page
└── ...                   # Other public pages
```

### Database Tables
- `users` - User accounts (admin and regular users)
- `site_settings` - Key-value store for site configuration
- `hero_slides` - Homepage hero carousel content
- `about_slides` - About section image slider
- `meal_plans` - Available meal plan packages
- `payments` - Payment records
- `payment_options` - Payment method configurations
- `blogs` - Blog posts
- `portfolio_projects` - Portfolio items
- `progress_photos` - Before/after transformation photos
- `case_studies` - Clinical case study content
- `credentials` - Professional certifications
- `user_plan_access` - User meal plan subscriptions
- `user_analytics` - User health metrics tracking

### Default Admin Credentials
- Email: admin@example.com
- Password: password (hashed in database)

## Development Commands

### Run Development Server
```bash
php -S 0.0.0.0:5000
```

### Database Connection
The app uses PostgreSQL environment variables:
- `PGHOST`, `PGPORT`, `PGUSER`, `PGPASSWORD`, `PGDATABASE`
- These are automatically configured by Replit

## User Preferences
- Language comments in code may be in Amharic (Ethiopian language)
- Design uses dark theme with emerald/green accent colors
- Mobile-responsive design is important

## Recent Changes
- **2026-01-02**: Initial import to Replit environment, database schema created
