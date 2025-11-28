# Job Tracker Pro - Job Application Management System

A comprehensive web-based job application tracking system built with PHP and MySQL. This application helps job seekers organize, track, and manage their job applications efficiently with a modern, responsive interface.

## 🚀 Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.1.3
- **Icons**: Font Awesome 6.0.0
- **Server**: Apache/Nginx
- **Security**: PDO with prepared statements, password hashing

## 📋 Project Overview

Job Tracker Pro is a Content Management System (CMS) designed specifically for job seekers to manage their job application process. It provides a centralized platform to track applications, monitor progress, set follow-up reminders, and maintain detailed records of all job-related activities.

### Key Benefits
- **Centralized Management**: Keep all job applications in one place
- **Progress Tracking**: Monitor application status and progress
- **Follow-up Reminders**: Never miss important follow-up dates
- **Detailed Records**: Store comprehensive information about each application
- **Analytics**: View statistics and insights about your job search

## ✨ Key Features

### 🔐 User Management
- **Secure Registration & Login**: User authentication with password hashing
- **Profile Management**: Update personal information and account details
- **Session Management**: Secure user sessions with automatic logout

### 📝 Application Management
- **Add Applications**: Create detailed job application records
- **Edit Applications**: Modify existing application information
- **View Details**: Comprehensive view of each application
- **Delete Applications**: Remove applications from the tracker
- **Status Tracking**: Track application progress through various stages

### 📊 Dashboard & Analytics
- **Overview Dashboard**: Summary statistics and recent activities
- **Application Statistics**: Visual representation of application progress
- **Status Breakdown**: Distribution of applications by status
- **Recent Applications**: Quick access to latest applications
- **Upcoming Follow-ups**: Reminders for important dates

### 🔍 Search & Filter
- **Search Functionality**: Find applications by company, title, or location
- **Status Filtering**: Filter applications by current status
- **Advanced Filtering**: Combine search and status filters

### 📅 Follow-up Management
- **Follow-up Dates**: Set and track important follow-up dates
- **Overdue Alerts**: Visual indicators for overdue follow-ups
- **Reminder System**: Never miss important deadlines

### 🎨 User Interface
- **Responsive Design**: Works perfectly on all devices
- **Modern UI**: Clean, professional interface
- **Dark Header**: Professional dark theme header
- **Card-based Layout**: Easy-to-scan application cards
- **Status Badges**: Color-coded status indicators

## 👥 User Roles

### Job Seeker
- **Primary User**: Individual managing their job applications
- **Permissions**: 
  - Create, edit, view, and delete their own applications
  - Update profile information
  - Access dashboard and analytics
  - Manage follow-up dates and notes

## 📁 Project Structure

```
job_tracker_cms/
├── 📄 Core Files
│   ├── index.php                 # Landing page
│   ├── login.php                 # User authentication
│   ├── register.php              # User registration
│   ├── logout.php                # User logout
│   ├── dashboard.php             # Main dashboard
│   ├── profile.php               # User profile management
│   ├── applications.php          # Applications listing
│   ├── add_application.php       # Add new application
│   ├── edit_application.php      # Edit existing application
│   └── view_application.php      # View application details
│
├── 📁 includes/
│   ├── header.php                # Common header
│   ├── footer.php                # Common footer
│   └── functions.php             # Helper functions
│
├── 📁 config/
│   └── database.php              # Database configuration
│
├── 📁 assets/
│   ├── css/
│   │   └── style.css             # Custom styles
│   └── js/
│       └── script.js             # JavaScript functionality
│
├── 📄 database_setup.sql         # Database schema
└── 📄 README.md                  # Project documentation
```

## 🛠️ Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB
- Apache or Nginx web server
- PDO extension enabled

### Installation Steps

1. **Download/Clone the Project**
   ```bash
   git clone https://github.com/noah-s-dev/job_tracker_cms.git
   cd job_tracker_cms
   ```

2. **Database Setup**
   - Create a new MySQL database named `job_tracker_cms`
   - Import the database structure:
   ```bash
   mysql -u your_username -p job_tracker_cms < database_setup.sql
   ```

3. **Configure Database Connection**
   - Open `config/database.php`
   - Update the database settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'job_tracker_cms');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   
   // Base URL configuration - ensures redirects go to http://localhost/project_name (no port)
   define('BASE_URL', 'http://localhost/job_tracker_cms');
   ```
   - **Note**: Update `BASE_URL` if your project is in a different location or if you need to use a different domain. This ensures all redirects work correctly without port numbers.

4. **Web Server Configuration**
   - Place the project in your web server directory
   - Ensure the web server has read/write permissions
   - Configure your web server to point to the project directory

5. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost/job_tracker_cms/`
   - Register a new account or use the demo account

### Demo Account
- **Username**: `demo_user`
- **Password**: `demo123`
- **Email**: `demo@example.com`

## 📖 Usage

### Getting Started
1. **Register/Login**: Create an account or use the demo account
2. **Dashboard**: View your application statistics and recent activities
3. **Add Applications**: Start tracking your job applications
4. **Manage Applications**: Edit, view, and update application details
5. **Set Follow-ups**: Add important follow-up dates and reminders

### Application Management
- **Adding Applications**: Fill in company details, job information, and contact details
- **Status Updates**: Track progress from "Applied" to "Offer Received"
- **Follow-up Tracking**: Set and monitor important follow-up dates
- **Notes**: Add personal notes and observations for each application

### Best Practices
- **Regular Updates**: Keep application status current
- **Follow-up Dates**: Set realistic follow-up reminders
- **Detailed Notes**: Record important conversations and observations
- **Contact Information**: Store all relevant contact details

## 🎯 Intended Use

### Personal Use
- Individual job seekers managing their own applications
- Students tracking internship applications
- Career changers organizing their job search

### Educational Use
- Learning PHP and MySQL development
- Understanding web application architecture
- Studying user interface design principles

### Development Use
- Starting point for custom job tracking applications
- Reference for PHP/MySQL best practices
- Template for similar management systems

## 🔒 Security Features

- **Password Hashing**: Secure password storage using PHP's password_hash()
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **Session Security**: Secure session management
- **User Authentication**: Login required for all sensitive operations

## 📄 License

**License for RiverTheme**

RiverTheme makes this project available for demo, instructional, and personal use. You can ask for or buy a license from [RiverTheme.com](https://RiverTheme.com) if you want a pro website, sophisticated features, or expert setup and assistance. A Pro license is needed for production deployments, customizations, and commercial use.

**Disclaimer**

The free version is offered "as is" with no warranty and might not function on all devices or browsers. It might also have some coding or security flaws. For additional information or to get a Pro license, please get in touch with [RiverTheme.com](https://RiverTheme.com).

---

**Developed by RiverTheme**

