# 🎨 Professional Portfolio Builder

A complete portfolio generation system that automatically creates stunning, professional portfolios from user form data. Perfect for developers, designers, doctors, photographers, and any professional looking to showcase their work.

## ✨ Features

- **Multi-Profession Support**: Templates for developers, doctors, photographers, video editors, marketers, designers, writers, and consultants
- **Automatic Portfolio Generation**: Fill out a form and get a complete portfolio instantly
- **Responsive Design**: All portfolios are mobile-friendly and modern
- **Real-time Preview**: See your portfolio as you build it
- **Easy Customization**: Multiple templates and styling options
- **File Upload Support**: Upload profile photos and project images
- **Database Integration**: All data is stored securely in MySQL
- **RESTful API**: Clean API for easy integration

## 🚀 Quick Start

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Installation

1. **Clone or download the project**
   ```bash
   git clone <repository-url>
   cd portfolio-builder
   ```

2. **Run the deployment script**
   ```bash
   php deploy.php
   ```
   Or visit `deploy.php` in your web browser.

3. **Configure database** (if needed)
   Edit `api/config/database.php` with your database credentials:
   ```php
   private $host = "localhost";
   private $db_name = "portfolio_system";
   private $username = "your_username";
   private $password = "your_password";
   ```

4. **Access the application**
   - Portfolio Builder: `premium-form.html`
   - Portfolio Viewer: `portfolio-viewer.html`

## 📁 Project Structure

```
portfolio-builder/
├── api/                          # Backend API
│   ├── config/
│   │   └── database.php          # Database configuration
│   ├── controllers/
│   │   ├── UserController.php    # User management
│   │   └── PortfolioController.php # Portfolio generation
│   ├── models/                   # Data models
│   │   ├── User.php
│   │   ├── Skill.php
│   │   ├── Experience.php
│   │   ├── Education.php
│   │   ├── Project.php
│   │   └── PortfolioTemplate.php
│   ├── database/
│   │   └── schema.sql            # Database schema
│   └── index.php                 # API router
├── uploads/                      # File uploads directory
├── premium-form.html             # Main portfolio builder form
├── portfolio-viewer.html         # Portfolio viewer and manager
├── portfolio-builder.html        # Alternative builder interface
├── .htaccess                     # Apache configuration
├── deploy.php                    # Deployment script
└── README.md                     # This file
```

## 🎯 How to Use

### Creating a Portfolio

1. **Open the Portfolio Builder**
   - Go to `premium-form.html` in your web browser

2. **Fill in Your Information**
   - Personal details (name, email, phone, etc.)
   - Professional summary
   - Skills (technical, soft skills, tools)
   - Work experience
   - Education
   - Projects and portfolio items

3. **Generate Your Portfolio**
   - Click "Generate My Portfolio"
   - Your portfolio will open in a new window
   - Download, print, or share as needed

### Viewing Portfolios

1. **Open the Portfolio Viewer**
   - Go to `portfolio-viewer.html`

2. **Load a Portfolio**
   - Enter a User ID
   - Select a template
   - Click "View Portfolio"

3. **Manage Your Portfolio**
   - Download as HTML
   - Print the portfolio
   - Share with others
   - Edit the original data

## 🎨 Available Templates

- **Developer**: Modern, tech-focused design with code highlighting
- **Doctor**: Clean, professional medical layout
- **Photographer**: Gallery-focused with image showcases
- **Video Editor**: Video-centric with showreel sections
- **Marketing**: Business-focused with case studies
- **Designer**: Creative, colorful design layout
- **Writer**: Elegant, text-focused design
- **Consultant**: Professional corporate layout

## 🔧 API Endpoints

### User Management
- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `GET /api/profile?id={id}` - Get user profile
- `PUT /api/profile?id={id}` - Update user profile
- `POST /api/save-profile` - Save complete profile data

### Portfolio Management
- `POST /api/generate-portfolio` - Generate portfolio HTML
- `GET /api/get-portfolio?id={id}` - Get portfolio data
- `GET /api/templates` - Get available templates

### Example API Usage

```javascript
// Generate a portfolio
const response = await fetch('api/index.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        action: 'generate-portfolio',
        user_id: 1,
        template: 'developer'
    })
});

const result = await response.json();
console.log(result.html); // Portfolio HTML
```

## 🗄️ Database Schema

The system uses MySQL with the following main tables:

- **users**: Basic user information
- **skills**: User skills categorized by type
- **experience**: Work experience entries
- **education**: Educational background
- **projects**: Portfolio projects
- **portfolio_templates**: Available templates
- **user_portfolios**: User portfolio configurations

## 🚀 Hosting Options

### Shared Hosting
1. Upload all files to your web server
2. Run `deploy.php` to set up the database
3. Update database credentials if needed
4. Access via your domain

### VPS/Dedicated Server
1. Set up LAMP/LEMP stack
2. Clone the repository
3. Run `php deploy.php`
4. Configure virtual host
5. Set up SSL certificate

### Cloud Hosting (AWS, DigitalOcean, etc.)
1. Create a server instance
2. Install PHP and MySQL
3. Deploy the application
4. Configure domain and SSL

## 🔒 Security Features

- SQL injection protection with prepared statements
- XSS protection with proper HTML escaping
- CORS headers for API security
- File upload validation
- Input sanitization

## 🎨 Customization

### Adding New Templates
1. Create a new template method in `PortfolioController.php`
2. Add template data to the database
3. Update the profession selection in forms

### Styling Customization
- Modify CSS in the template methods
- Add custom CSS classes
- Update color schemes and fonts

### Adding New Fields
1. Update the database schema
2. Modify the form HTML
3. Update the data collection JavaScript
4. Update the API controllers

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
- Check database credentials in `api/config/database.php`
- Ensure MySQL is running
- Verify database exists

**Portfolio Not Loading**
- Check browser console for errors
- Verify API endpoints are working
- Check database for user data

**File Upload Issues**
- Ensure `uploads/` directory exists and is writable
- Check file size limits in PHP configuration
- Verify file type restrictions

### Debug Mode
Enable debug mode by adding this to `api/index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📞 Support

For support and questions:
- Check the troubleshooting section above
- Review the API documentation
- Test with the provided examples

## 📄 License

This project is open source and available under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

---

**Happy Portfolio Building! 🎉**

