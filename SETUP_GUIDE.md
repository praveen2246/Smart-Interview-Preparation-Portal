# Smart Interview Preparation Portal (SIPP)
## Complete Setup & Installation Guide

### 📋 Table of Contents
1. [Requirements](#requirements)
2. [Installation Steps](#installation-steps)
3. [Database Setup](#database-setup)
4. [Credentials](#credentials)
5. [Features Overview](#features-overview)
6. [Project Structure](#project-structure)
7. [Security Features](#security-features)
8. [Troubleshooting](#troubleshooting)

---

## Requirements

### System Requirements
- **Windows 7 or higher**
- **XAMPP 7.4+ or similar (Apache + MySQL + PHP 7.4+)**
- **Modern web browser (Chrome, Firefox, Edge)**
- **Minimum 2GB RAM**
- **Internet connection (for Bootstrap and Chart.js CDN)**

### Required Software
- Apache Web Server
- PHP 7.4+
- MySQL 5.7+

---

## Installation Steps

### Step 1: Prepare XAMPP
1. **Download and Install XAMPP** from https://www.apachefriends.org/
2. **Start XAMPP Control Panel**
   - Start **Apache**
   - Start **MySQL**
3. **Access phpMyAdmin** at http://localhost/phpmyadmin

### Step 2: Extract Project Files
1. Extract the SIPP project to: `C:\xampp\htdocs\sipp`
2. Verify the folder structure:
   ```
   C:\xampp\htdocs\sipp\
   ├── admin/
   ├── assets/
   ├── config/
   ├── controllers/
   ├── models/
   ├── views/
   ├── index.php
   ├── database.sql
   └── sample_data.sql
   ```

### Step 3: Create Database
1. **Open phpMyAdmin**: http://localhost/phpmyadmin
2. **Create New Database**:
   - Click "New" button on left sidebar
   - Database name: `sipp`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

### Step 4: Import Database Schema
1. **Open the new `sipp` database**
2. **Click "Import" tab**
3. **Choose File**: Select `C:\xampp\htdocs\sipp\database.sql`
4. **Click "Go"** to import the schema
5. **Verify tables created**: Should see 7 tables

### Step 5: Import Sample Data (Optional)
1. **Still in phpMyAdmin with `sipp` database selected**
2. **Click "Import" tab**
3. **Choose File**: Select `C:\xampp\htdocs\sipp\sample_data.sql`
4. **Click "Go"** to import sample data
   - Creates 1 admin account
   - Creates 10 sample PHP questions
   - Creates 10 sample Java questions
   - Creates 10 sample React questions
   - Creates 2 sample users

### Step 6: Configure Database Connection (Optional)
The default configuration in `config/Database.php` is:
```php
private $host = 'localhost';
private $db_name = 'sipp';
private $db_user = 'root';
private $db_pass = '';
```

If you have a custom MySQL setup:
1. Edit `C:\xampp\htdocs\sipp\config\Database.php`
2. Update the connection details
3. Save the file

### Step 7: Start Using SIPP
1. **Access the application**: http://localhost/sipp
2. **Should see the homepage with login/register options**

---

## Database Setup

### Database Schema Overview

#### Users Table
- Stores regular user accounts
- Fields: id, username, email, password (hashed), full_name, created_at, updated_at

#### Questions Table
- Stores interview questions
- Fields: id, technology (PHP/Java/React), topic, question_text, options (A-D), correct_answer, difficulty (easy/medium/hard)
- Indexed by: technology, difficulty, topic

#### Results Table
- Stores test results
- Fields: id, user_id, technology, total_questions, correct_answers, wrong_answers, accuracy_percentage, score, test_duration_seconds, test_date
- Foreign key: user_id (references users.id)

#### Weak Topics Table
- Tracks areas where users need improvement
- Fields: id, user_id, technology, topic, wrong_count, correct_count, last_attempted
- Shows topics with 3+ wrong answers

#### Test Answers Table
- Detailed log of user answers
- Fields: id, result_id, question_id, user_answer, is_correct, created_at
- Foreign keys: result_id, question_id

#### Admin Table
- Admin user accounts
- Fields: id, username, email, password (hashed), full_name, created_at, updated_at

---

## Credentials

### Default Admin Account
- **Username**: `admin`
- **Password**: `admin123`
- **Access**: http://localhost/sipp/admin/login.php

### Sample User Accounts (if sample data imported)
- **User 1**:
  - Username: `john_dev`
  - Password: `password123`
  
- **User 2**:
  - Username: `sarah_coder`
  - Password: `password123`

### Create New Admin Account
Only one admin account can exist initially. To create new admin accounts:
1. Modify `models/Admin.php` if needed
2. Or use phpMyAdmin to insert directly:
```sql
INSERT INTO `admin` (`username`, `email`, `password`, `full_name`) VALUES 
('newadmin', 'admin@email.com', '$2y$10$[hashed_password]', 'Admin Name');
```

---

## Features Overview

### User Features
1. **Registration & Login**
   - Secure password hashing (bcrypt)
   - Email and username validation
   - Session management

2. **Take Tests**
   - Select technology (PHP/Java/React)
   - 10 random questions per test
   - 10-minute countdown timer
   - Real-time question progression

3. **Performance Dashboard**
   - View statistics (total tests, average accuracy, best score)
   - Line chart showing performance over time
   - Average accuracy calculation

4. **Weak Topic Recommendations**
   - Track topics with 3+ wrong answers
   - Automated recommendations for improvement
   - Visual alerts in dashboard

5. **Results Tracking**
   - Detailed results for each test
   - View all previous results
   - Performance history

### Admin Features
1. **Question Management**
   - Add new questions
   - Edit existing questions
   - Delete questions
   - Filter by technology and difficulty

2. **System Dashboard**
   - View total questions by technology
   - Monitor recent test results
   - System statistics

3. **User Analytics**
   - View all users and their statistics
   - Track user performance
   - Monitor test participation

---

## Project Structure

### Directory Layout
```
sipp/
├── admin/                    # Admin panel pages
│   ├── login.php            # Admin login
│   ├── dashboard.php        # Admin dashboard
│   ├── questions.php        # Question management
│   ├── users.php           # User analytics
│   └── profile.php         # Admin profile
│
├── assets/
│   ├── css/
│   │   └── style.css       # Custom styles
│   └── js/
│       └── main.js         # Custom JavaScript
│
├── config/
│   └── Database.php        # Database connection class
│
├── controllers/
│   └── AuthController.php  # Authentication logic
│
├── models/
│   ├── User.php           # User model with registration/login
│   ├── Question.php       # Question retrieval and management
│   ├── Result.php         # Results and scoring logic
│   └── Admin.php          # Admin authentication
│
├── views/
│   ├── header.php         # Navigation and header
│   └── footer.php         # Footer and closing tags
│
├── index.php              # Homepage/landing page
├── register.php           # User registration
├── login.php              # User login
├── logout.php             # Logout handler
├── dashboard.php          # User dashboard
├── test.php               # Take test (technology selection + questions)
├── submit_test.php        # Test submission processor
├── test_result.php        # Detailed test results
├── results.php            # Results history
├── profile.php            # User profile
├── database.sql           # Database schema
└── sample_data.sql        # Sample data for testing
```

### File Descriptions

#### Config Files
- **Database.php**: Handles MySQLi connection with prepared statements

#### Model Files
- **User.php**: Registration, login, validation, user data retrieval
- **Question.php**: Get random questions, filter, add/edit/delete (admin)
- **Result.php**: Save test results, calculate scores, track weak topics
- **Admin.php**: Admin authentication and management

#### Controller Files
- **AuthController.php**: Centralized authentication logic for both users and admins

#### View Files
- **header.php**: Navigation bar with dynamic menu based on user type
- **footer.php**: Footer with closing HTML tags and scripts

#### Page Files
- **index.php**: Landing page with feature overview
- **register.php**: User registration form
- **login.php**: User login form
- **dashboard.php**: Performance metrics and Chart.js visualization
- **test.php**: Test interface with timer and questions
- **test_result.php**: Detailed test analysis
- **results.php**: Historical results table
- **profile.php**: User profile and statistics
- **admin/**: Full admin panel with separate authentication

---

## Security Features

### 1. Password Security
- **Bcrypt Hashing**: Uses `password_hash()` with BCRYPT algorithm
- **Verification**: Uses `password_verify()` for secure comparison
- **Hash Cost**: 10 (default, can be increased)

### 2. SQL Injection Prevention
- **Prepared Statements**: All database queries use `mysqli->prepare()` and `bind_param()`
- **Input Validation**: All user inputs are validated before use
- **No String Concatenation**: Never concatenates user input into SQL queries

### 3. Session Management
- **Session Start**: `session_start()` at page beginning
- **User Verification**: All protected pages check session variables
- **Session Destruction**: Proper logout with `session_destroy()`
- **Redirect to Login**: Unauthorized access redirects to login

### 4. Input Validation
- **Email Validation**: Uses `filter_var()` with FILTER_VALIDATE_EMAIL
- **Username Validation**: Regex check for alphanumeric and underscore only
- **Password Requirements**: Minimum 6 characters enforced
- **XSS Prevention**: `htmlspecialchars()` on all output

### 5. CSRF Protection
- Forms use standard HTTP methods
- Token-like protection through session validation
- Admin actions require POST method

### 6. Access Control
- User and Admin have separate authentication flows
- Protected pages check user type in session
- Admin panel requires admin session

### 7. Data Protection
- Password fields never displayed
- Sensitive data (password hashes) never exposed
- User IDs used instead of sensitive identifiers where possible

---

## Troubleshooting

### Issue 1: "Connection Error: Connection refused"
**Cause**: MySQL is not running
**Solution**:
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait for it to fully start
4. Try again

### Issue 2: "Call to undefined function mysqli_..."
**Cause**: MySQLi extension not enabled in PHP
**Solution**:
1. Edit `C:\xampp\php\php.ini`
2. Find `;extension=mysqli`
3. Remove the semicolon to uncomment: `extension=mysqli`
4. Restart Apache and MySQL

### Issue 3: "XAMPP localhost not working"
**Solution**:
1. Check if port 80 is in use (try netstat -ano)
2. Stop other applications using port 80
3. Or change Apache port in XAMPP configuration

### Issue 4: "No questions appear in test"
**Cause**: Sample data not imported
**Solution**:
1. Import `sample_data.sql` from phpMyAdmin
2. Or add questions via Admin panel
3. Add at least 10 questions per technology

### Issue 5: "Timer not working"
**Cause**: JavaScript not loading properly
**Solution**:
1. Check browser console for errors (F12)
2. Verify all JavaScript files exist in `assets/js/`
3. Check CDN links for Chart.js and Bootstrap in header.php

### Issue 6: "Chart not displaying"
**Cause**: Chart.js CDN not loaded or chart canvas missing
**Solution**:
1. Verify internet connection (CDN loads from CDN)
2. Check if canvas with id exists
3. Verify JavaScript console for errors

### Issue 7: "Database errors when saving results"
**Cause**: Foreign key constraints or missing data
**Solution**:
1. Verify user_id exists in users table
2. Verify question_id exists in questions table
3. Check MySQL error logs in XAMPP/mysql/data/

### Issue 8: "Can't login with sample credentials"
**Cause**: Sample data not imported
**Solution**:
1. Import `sample_data.sql` file
2. Or use admin credentials: admin / admin123

### Issue 9: "Password hashing error"
**Cause**: PHP version too old (pre-5.5)
**Solution**:
1. Upgrade to PHP 7.0 or higher
2. Check XAMPP version (must be recent)

### Issue 10: "Session not persisting"
**Cause**: Browser cookies disabled or session path issue
**Solution**:
1. Enable cookies in browser
2. Check browser console for errors
3. Verify session directory permissions

---

## Performance Tips

1. **Database Optimization**
   - Indexes created on technology, difficulty, topic
   - Use LIMIT clauses for pagination
   - Archive old results periodically

2. **Caching**
   - Questions can be cached if not frequently updated
   - Chart data can be cached for 1 hour

3. **File Uploads**
   - Keep image sizes small
   - Use lazy loading for charts

4. **Code Optimization**
   - Reuse database connections
   - Minimize database queries
   - Use prepared statements (already implemented)

---

## Next Steps & Enhancements

### Suggested Features to Add
1. **Email Notifications**: Send test results via email
2. **Leaderboard**: Rank users by scores
3. **Question Categories**: More granular topic selection
4. **Discussion Forum**: Q&A forum for users
5. **Video Tutorials**: Link to learning resources
6. **Mobile App**: React Native mobile application
7. **API**: RESTful API for external integration
8. **Analytics Export**: Export reports as PDF/Excel
9. **User Badges**: Gamification with achievement badges
10. **Scheduled Tests**: Fixed test schedules

---

## Support & Contact

For issues or questions:
1. Check this documentation
2. Review troubleshooting section
3. Check database.sql for schema validation
4. Verify file permissions: `chmod 755` on folders, `644` on files

---

## License & Credits

**SIPP - Smart Interview Preparation Portal**
- Built with: PHP, MySQL, Bootstrap 5, Chart.js
- Created for educational purposes
- Feel free to modify and distribute

---

**Last Updated**: February 2026
**Version**: 1.0
