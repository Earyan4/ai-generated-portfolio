# 🚀 Portfolio System Setup Guide

## Quick Setup (Windows)

### Option 1: Using XAMPP (Recommended)

1. **Download and Install XAMPP**
   - Go to https://www.apachefriends.org/download.html
   - Download XAMPP for Windows
   - Install it (usually in `C:\xampp`)

2. **Start Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

3. **Deploy the Project**
   - Copy all project files to `C:\xampp\htdocs\portfolio-builder\`
   - Open your browser and go to `http://localhost/portfolio-builder/`

4. **Set Up Database**
   - Go to `http://localhost/phpmyadmin/`
   - Create a new database called `portfolio_system`
   - Import the SQL file from `api/database/schema.sql`

### Option 2: Using WAMP

1. **Download and Install WAMP**
   - Go to https://www.wampserver.com/
   - Download and install WAMP

2. **Deploy the Project**
   - Copy all project files to `C:\wamp64\www\portfolio-builder\`
   - Start WAMP services
   - Go to `http://localhost/portfolio-builder/`

### Option 3: Using MAMP (Mac)

1. **Download and Install MAMP**
   - Go to https://www.mamp.info/
   - Download and install MAMP

2. **Deploy the Project**
   - Copy all project files to `/Applications/MAMP/htdocs/portfolio-builder/`
   - Start MAMP services
   - Go to `http://localhost:8888/portfolio-builder/`

## Manual Database Setup

If you prefer to set up the database manually:

1. **Create Database**
   ```sql
   CREATE DATABASE portfolio_system;
   USE portfolio_system;
   ```

2. **Import Schema**
   - Open `api/database/schema.sql`
   - Copy and paste the contents into your MySQL client
   - Execute the SQL commands

3. **Update Configuration**
   - Edit `api/config/database.php`
   - Update the database credentials if needed

## Testing the System

1. **Open the Application**
   - Go to `http://localhost/portfolio-builder/` (or your server URL)
   - You should see the main landing page

2. **Create a Test Portfolio**
   - Click "Create My Portfolio"
   - Fill out the form with test data
   - Click "Generate My Portfolio"
   - A new window should open with your portfolio

3. **View Portfolios**
   - Go to "View Portfolios"
   - Enter User ID "1" (or any existing user ID)
   - Click "View Portfolio"

## Troubleshooting

### Common Issues

**"Database connection error"**
- Check if MySQL is running
- Verify database credentials in `api/config/database.php`
- Make sure the database exists

**"Portfolio not loading"**
- Check browser console for errors
- Verify API endpoints are working
- Check if user data exists in database

**"File upload not working"**
- Check if `uploads/` directory exists and is writable
- Verify PHP file upload settings
- Check file size limits

### Debug Mode

To enable debug mode, add this to the top of `api/index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Production Deployment

### Shared Hosting

1. Upload all files to your web server
2. Create a MySQL database
3. Import the schema from `api/database/schema.sql`
4. Update database credentials in `api/config/database.php`
5. Set proper file permissions (755 for directories, 644 for files)

### VPS/Cloud Server

1. Install LAMP/LEMP stack
2. Clone the repository
3. Set up virtual host
4. Configure SSL certificate
5. Set up database
6. Configure file permissions

## File Structure

```
portfolio-builder/
├── index.html                 # Main landing page
├── premium-form.html          # Portfolio builder form
├── portfolio-viewer.html      # Portfolio viewer
├── portfolio-builder.html     # Alternative builder
├── api/                       # Backend API
│   ├── config/database.php    # Database config
│   ├── controllers/           # API controllers
│   ├── models/                # Data models
│   ├── database/schema.sql    # Database schema
│   ├── upload.php             # File upload handler
│   └── index.php              # API router
├── uploads/                   # File uploads directory
├── .htaccess                  # Apache configuration
├── deploy.php                 # Deployment script
└── README.md                  # Documentation
```

## Support

If you encounter any issues:

1. Check the troubleshooting section above
2. Verify all requirements are met
3. Check error logs
4. Test with sample data

## Next Steps

Once the system is running:

1. Create your first portfolio
2. Customize templates if needed
3. Set up your domain (for production)
4. Configure SSL certificate
5. Set up regular backups

---

**Happy Portfolio Building! 🎉**

