# Student Admissions Management System (SAMS)

A comprehensive web-based system for managing student admissions, including application processing, exam scheduling, interview management, and result tracking.

## 🌟 Features

- **User Management**
  - Multi-user roles (Admin, Program Head, Interviewer)
  - Secure authentication and authorization
  - User profile management

- **Application Processing**
  - Online application submission
  - Document upload and verification
  - Application status tracking
  - Program selection (Primary and Secondary choices)

- **Exam Management**
  - Exam schedule creation and management
  - Online exam registration
  - Exam result recording
  - Score calculation and ranking

- **Interview Management**
  - Interview schedule creation
  - Interviewer assignment
  - Interview scoring system
  - Interview status tracking

- **Results and Rankings**
  - Overall student rankings
  - Program-specific rankings
  - Cutoff management
  - Eligibility determination
  - Export functionality (PDF, Excel, CSV)

- **Reporting**
  - Comprehensive reports generation
  - Data export capabilities
  - Statistical analysis
  - Activity logging

## 🛠️ Technology Stack

- **Frontend**
  - HTML5, CSS3, JavaScript
  - Bootstrap 4
  - jQuery
  - DataTables
  - SweetAlert2
  - Font Awesome

- **Backend**
  - PHP 7.4+
  - MySQL 5.7+
  - Apache/Nginx

- **Development Tools**
  - XAMPP (Local Development)
  - Git (Version Control)
  - VS Code (Recommended IDE)

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)
- Web browser with JavaScript enabled

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/sams.git
   cd sams
   ```

2. **Database Setup**
   - Create a new MySQL database
   - Import the database schema from `database/tup_admissions-final.sql`
   ```bash
   mysql -u yourusername -p yourdatabase < database/tup_admissions-final.sql
   ```

3. **Configuration**
   - Copy `config/database.example.php` to `config/database.php`
   - Update database credentials in `config/database.php`
   ```php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'your_username');
   define('DB_PASSWORD', 'your_password');
   define('DB_NAME', 'your_database');
   ```

4. **Web Server Setup**
   - For Apache: Ensure mod_rewrite is enabled
   - For Nginx: Configure URL rewriting
   - Set document root to the project's public directory
   - Ensure proper permissions on storage directories

5. **Dependencies**
   - Install required PHP extensions
   - Set up proper file permissions
   ```bash
   chmod 755 -R /path/to/sams
   chmod 777 -R /path/to/sams/uploads
   ```

## 🔧 Configuration

### Environment Variables
Create a `.env` file in the root directory:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password

MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

### Security Settings
- Update `config/security.php` with appropriate settings
- Set secure session parameters
- Configure CORS if needed
- Set up SSL certificate

## 👥 User Roles

1. **Administrator**
   - Full system access
   - User management
   - Program management
   - System configuration

2. **Program Head**
   - Program-specific access
   - Interview scheduling
   - Result management
   - Program statistics

3. **Interviewer**
   - Interview management
   - Score submission
   - Applicant evaluation

4. **Applicant**
   - Application submission
   - Document upload
   - Status checking
   - Result viewing

## 📊 Database Structure

Key tables include:
- `users` - User accounts and authentication
- `applicants` - Applicant information
- `programs` - Academic programs
- `applications` - Application records
- `exam_schedules` - Exam scheduling
- `exam_results` - Exam scores
- `interviews` - Interview management
- `interview_results` - Interview scores
- `program_rankings` - Student rankings

## 🔐 Security Features

- Password hashing
- Session management
- SQL injection prevention
- XSS protection
- CSRF protection
- Input validation
- Secure file upload
- Activity logging

## 📝 API Documentation

The system provides RESTful API endpoints for:
- User authentication
- Application management
- Exam processing
- Interview handling
- Results and rankings

API documentation is available at `/api/docs` when running in development mode.

## 🧪 Testing

1. **Unit Tests**
   ```bash
   php vendor/bin/phpunit
   ```

2. **Integration Tests**
   ```bash
   php vendor/bin/phpunit --testsuite integration
   ```

3. **Browser Tests**
   ```bash
   php vendor/bin/phpunit --testsuite browser
   ```

## 📈 Performance Optimization

- Database indexing
- Query optimization
- Caching implementation
- Asset minification
- Image optimization
- Lazy loading

## 🔄 Deployment

1. **Production Setup**
   - Configure web server
   - Set up SSL certificate
   - Configure database
   - Set environment variables
   - Run database migrations

2. **Deployment Checklist**
   - [ ] Update configuration files
   - [ ] Set proper permissions
   - [ ] Configure error logging
   - [ ] Set up backup system
   - [ ] Test all features
   - [ ] Monitor performance

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. Commit your changes
   ```bash
   git commit -m 'Add some AmazingFeature'
   ```
4. Push to the branch
   ```bash
   git push origin feature/AmazingFeature
   ```
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Authors

- Your Name - Initial work - [YourGitHub](https://github.com/yourusername)

## 🙏 Acknowledgments

- [Bootstrap](https://getbootstrap.com/)
- [DataTables](https://datatables.net/)
- [SweetAlert2](https://sweetalert2.github.io/)
- [Font Awesome](https://fontawesome.com/)

## 📞 Support

For support, email support@yourdomain.com or create an issue in the repository.

## 🔄 Updates and Maintenance

Regular updates include:
- Security patches
- Bug fixes
- Feature enhancements
- Performance improvements
- Documentation updates

## 📚 Documentation

- [User Manual](docs/user-manual.md)
- [Admin Guide](docs/admin-guide.md)
- [API Documentation](docs/api-docs.md)
- [Development Guide](docs/development-guide.md)
- [Deployment Guide](docs/deployment-guide.md) 