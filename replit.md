# Eleni Mekuria - Dietitian Portfolio & Meal Planning Platform

## Overview

A high-end, luxury-style portfolio and meal planning platform for Eleni Mekuria, a professional Dietitian/Nutritionist. The platform serves dual purposes: showcasing professional credentials and providing a monetized meal plan subscription service.

**Core Purpose:**
- Professional portfolio displaying certifications, media appearances, and client testimonials
- Meal plan marketplace with locked content requiring payment approval
- Admin-managed payment verification system for manual transaction approval

**Design Philosophy:**
- Luxury aesthetic inspired by MyDigiMenu.com
- Immersive full-screen sections with smooth scrolling animations
- Video background hero section featuring Afrihealth TV show content

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Backend Architecture

**Technology:** Pure PHP (CPanel-compatible deployment)
**Database:** PostgreSQL (Replit Managed)

**Folder Structure:**
```
/
├── /assets          # CSS, JS, images, uploaded files
├── /includes        # Shared PHP components (header, footer, db config)
├── /admin           # Admin panel pages and logic
├── /user            # User dashboard and meal plan pages
├── /uploads         # User uploaded receipts
├── config.php       # Database credentials using environment variables
└── index.php        # Main entry point
```

### Database Design (PostgreSQL)

**Core Tables:**
1. `users` - Registration data (name, email, password, age, weight, height, goal, package, status, role)
2. `meal_plans` - Plan content with lock status
3. `payments` - Bank receipt uploads and approval status
4. `testimonials` - Client reviews for portfolio
5. `certificates` - Professional credentials

### Current State
- [x] Basic directory structure established
- [x] Database schema initialized
- [x] User registration & Login implemented
- [x] User Dashboard with lock system
- [x] Payment receipt upload system
- [x] Admin panel for user approval
- [x] Landing page with GSAP & Lenis smooth scrolling

## Recent Changes
- **2025-12-31:** Initial platform setup including authentication, dashboard, admin panel, and landing page.
