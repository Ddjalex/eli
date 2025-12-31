# cPanel MySQL Setup Instructions

## Step 1: Get Your MySQL Credentials from cPanel

1. Log in to your cPanel
2. Go to **MySQL Databases** (or **MySQL Database Wizard**)
3. Create a new database or note your existing one
4. Create a new MySQL user or use existing credentials
5. Assign the user to the database with **ALL PRIVILEGES**
6. Note these details:
   - **Host**: Usually `localhost`
   - **Database Name**: `your_username_dbname`
   - **MySQL User**: `your_username_user`
   - **MySQL Password**: (the password you set)

## Step 2: Run the SQL Statements

### Option A: Using cPanel phpMyAdmin (Easiest)
1. In cPanel, click **phpMyAdmin**
2. Select your database from the left sidebar
3. Click the **SQL** tab
4. Copy all the SQL from **MYSQL_SETUP.sql** file
5. Paste it into the SQL editor
6. Click **Go** to execute

### Option B: Using MySQL Command Line (Advanced)
```bash
mysql -h localhost -u your_mysql_user -p your_database_name < MYSQL_SETUP.sql
```
When prompted, enter your MySQL password.

## Step 3: Update Your Application Config

1. Download `includes/config-mysql.php`
2. Edit it with your MySQL credentials:
   ```php
   $host = 'localhost';
   $username = 'your_mysql_user';    // From cPanel
   $password = 'your_mysql_pass';    // From cPanel
   $database = 'your_db_name';       // From cPanel
   ```
3. Rename or replace the existing `includes/config.php` with this file
4. Save and upload to your server

## Step 4: Verify Database Setup

1. Upload all application files to your cPanel public_html folder
2. Visit your domain in browser
3. You should see the homepage load
4. Try admin login with:
   - Email: `admin@example.com`
   - Password: `admin123`

## Database Credentials Summary

| Item | Value |
|------|-------|
| Host | localhost |
| Database Name | [From cPanel] |
| MySQL User | [From cPanel] |
| MySQL Password | [From cPanel] |
| Admin Email | admin@example.com |
| Admin Password | admin123 |

## Tables Created

- **users** - User accounts and authentication
- **meal_plans** - Subscription packages
- **payments** - Payment records and receipts
- **payment_options** - Bank/payment methods
- **site_settings** - Site configuration
- **hero_slides** - Homepage carousel
- **testimonials** - Client reviews
- **certificates** - Professional credentials

## Important Notes

✅ All tables are properly created  
✅ Admin user is pre-inserted  
✅ 3 sample meal plans included  
✅ 3 payment methods configured  
✅ Ready for users to register and pay  

## Troubleshooting

**Error: "Table already exists"**
- If running SQL a second time, it's safe to ignore (uses IF NOT EXISTS)

**Error: "Connection refused"**
- Check your host is `localhost` (not IP address)
- Verify credentials in cPanel are correct
- Make sure database user is assigned to database

**Error: "Access denied"**
- Verify MySQL user has ALL PRIVILEGES on database
- Check password spelling carefully

## Support

If you need help after setup:
1. Check cPanel MySQL databases page for correct credentials
2. Test connection using phpMyAdmin
3. Verify file permissions on server (755 for folders, 644 for files)
