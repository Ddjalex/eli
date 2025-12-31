# Meal Plan File Upload & Preview System - Setup Complete

## Issue Fixed
The file upload system is now fully functional. Files uploaded by admins through the admin panel will now be accessible to approved users in their dashboard.

## What Was Done
1. **Created Database Tables** - All required tables (users, meal_plans, payments, testimonials, etc.) are now initialized
2. **Created Required Directories**:
   - `/protected_files/` - Stores secure PDF meal plans
   - `/uploads/previews/` - Stores meal plan preview images
   - `/uploads/site/` - Stores site images (hero, about section)
   - `/uploads/receipts/` - Stores payment receipts
   - `/uploads/slides/` - Stores hero carousel slides

3. **Set Up Sample Meal Plans** - Three test plans are available:
   - Weight Loss Plan (499.99 Birr)
   - Muscle Gain Plan (599.99 Birr)
   - Sports Performance (699.99 Birr)

## How It Works

### For Admins
1. Log in at `/login.php` with admin credentials
2. Go to Admin Panel → Manage Packages
3. Create a new package or update existing ones
4. Upload PDF file for the meal plan
5. Upload preview image (JPG/PNG recommended)
6. Click "Save Changes"
7. Files are automatically saved to protected directories and database is updated

### For Users
1. Register at `/register.php`
2. Complete payment through the payment system
3. After payment is approved by admin, access dashboard
4. View meal plans and click "View Plan" to access the PDF
5. Use "Download PDF" to save locally

## File Storage Paths

| File Type | Storage Directory | Access |
|-----------|-----------------|--------|
| Meal Plans (PDF) | `/protected_files/` | Via preview.php/download.php |
| Preview Images | `/uploads/previews/` | Via user dashboard |
| Site Images | `/uploads/site/` | Via homepage/admin |
| Receipts | `/uploads/receipts/` | Via admin approval |
| Slides | `/uploads/slides/` | Via homepage carousel |

## Testing the System

**Admin Credentials:**
- Email: `admin@example.com`
- Password: `admin123`

**Test User Account Created:**
- Email: `test@example.com`
- Password: (same bcrypt hash as admin)
- Status: Approved
- Can access all meal plans

## Troubleshooting

If files still don't appear for users:
1. Verify file was uploaded (check admin panel confirmation message)
2. Ensure user status is "approved" in admin panel
3. Check that file path in database matches actual file location
4. Verify folder permissions: `chmod 755 protected_files uploads/`

## Database Schema

### meal_plans table
- `id` (serial) - Plan ID
- `title` (varchar) - Display name
- `package_type` (varchar) - slug (e.g., "weight_loss")
- `price` (decimal) - Cost in Birr
- `file_url` (text) - Path to PDF file
- `preview_image` (text) - Path to preview image
- `created_at` (timestamp) - Creation date

### users table
- `id` (serial) - User ID
- `email` (varchar) - Unique email
- `password` (varchar) - Hashed password
- `status` (varchar) - "approved" or "pending"
- `role` (varchar) - "user" or "admin"
- `package` (varchar) - Selected plan type
- Other fields: name, age, weight, height, goal

## Key Files
- `admin/manage_plans.php` - Admin package management
- `user/dashboard.php` - User meal plan access
- `preview.php` - PDF viewer for logged-in users
- `download.php` - PDF download handler
- `includes/config.php` - Database connection
