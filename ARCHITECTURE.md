# SIPP Architecture & System Design

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                             │
│                   (User's Web Browser)                          │
│  ┌──────────────┬──────────────┬──────────────┬──────────────┐  │
│  │   HTML5      │    CSS3      │ JavaScript   │  Chart.js    │  │
│  │  Templates   │   Bootstrap5 │  (Vanilla)   │  Library     │  │
│  └──────────────┴──────────────┴──────────────┴──────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              ↓ HTTP/HTTPS
┌─────────────────────────────────────────────────────────────────┐
│                    WEB SERVER LAYER                             │
│                   (Apache + PHP 7.4+)                           │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │                    PUBLIC PAGES                            │ │
│  │  index.php → register.php → login.php → logout.php        │ │
│  └────────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │              USER PROTECTED PAGES                          │ │
│  │  dashboard.php → test.php → test_result.php → results.php │ │
│  │                         ↓                                   │ │
│  │                   profile.php                              │ │
│  └────────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │              ADMIN PROTECTED PAGES                         │ │
│  │  admin/login.php → admin/dashboard.php                    │ │
│  │  admin/questions.php → admin/users.php → admin/profile.php│ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                   APPLICATION LAYER                             │
│                    (Core PHP Logic)                             │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │          CONTROLLERS (Request Handling)                    │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  AuthController.php                                │  │ │
│  │  │  - loginUser()                                     │  │ │
│  │  │  - loginAdmin()                                    │  │ │
│  │  │  - registerUser()                                  │  │ │
│  │  │  - logout()                                        │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │            MODELS (Business Logic)                        │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  User.php                                          │  │ │
│  │  │  - register()  - login()  - getUserById()          │  │ │
│  │  │  - validateUsername()  - validateEmail()           │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  Question.php                                      │  │ │
│  │  │  - getRandomQuestions()  - getAllQuestions()       │  │ │
│  │  │  - addQuestion()  - updateQuestion()               │  │ │
│  │  │  - deleteQuestion()  - getCorrectAnswer()          │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  Result.php                                        │  │ │
│  │  │  - saveResult()  - getUserResults()                │  │ │
│  │  │  - getDashboardStats()  - getWeakTopics()          │  │ │
│  │  │  - updateWeakTopic()  - getPerformanceData()       │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  Admin.php                                         │  │ │
│  │  │  - login()  - getAdminById()  - adminExists()      │  │ │
│  │  │  - createDefaultAdmin()                            │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │            CONFIGURATION (Database)                       │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  Database.php                                      │  │ │
│  │  │  - connect()  - prepare()  - escape()              │  │ │
│  │  │  Uses MySQLi with prepared statements             │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │             VIEWS (Presentation)                         │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  header.php  →  [Page Content]  →  footer.php       │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                              ↓ MySQLi
┌─────────────────────────────────────────────────────────────────┐
│                    DATABASE LAYER                               │
│                    (MySQL 5.7+)                                 │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  DATABASE: sipp                                            │ │
│  │  ┌──────────────┬──────────────┬──────────────────────┐   │ │
│  │  │  users       │  admin       │  questions           │   │ │
│  │  │  ├─ id       │  ├─ id       │  ├─ id               │   │ │
│  │  │  ├─ username │  ├─ username │  ├─ technology       │   │ │
│  │  │  ├─ email    │  ├─ email    │  ├─ topic            │   │ │
│  │  │  ├─ password │  ├─ password │  ├─ question_text    │   │ │
│  │  │  ├─ full_name│  ├─ full_name│  ├─ option_a/b/c/d   │   │ │
│  │  │  └─ timestamps   └─ timestamps   ├─ correct_answer  │   │ │
│  │  │                                  ├─ difficulty      │   │ │
│  │  │                                  └─ timestamps      │   │ │
│  │  ├──────────────┬──────────────┬──────────────────────┤   │ │
│  │  │  results     │ test_answers │  weak_topics         │   │ │
│  │  │  ├─ id       │  ├─ id       │  ├─ id               │   │ │
│  │  │  ├─ user_id  │  ├─ result_id│  ├─ user_id (FK)     │   │ │
│  │  │  ├─ technology│ ├─ question_id(FK) ├─ technology    │   │ │
│  │  │  ├─ score    │  ├─ user_answer    ├─ topic          │   │ │
│  │  │  ├─ accuracy │  ├─ is_correct     ├─ wrong_count    │   │ │
│  │  │  ├─ correct/wrong │  └─ created_at    ├─ correct_count   │ │
│  │  │  ├─ duration │                       └─ last_attempted   │ │
│  │  │  └─ test_date│                                      │   │ │
│  │  └──────────────┴──────────────┴──────────────────────┘   │ │
│  │  INDEXES:                                                  │ │
│  │  • users(username), users(email)                          │ │
│  │  • questions(technology), questions(difficulty)           │ │
│  │  • results(user_id), results(technology), results(test_date) │ │
│  │  • weak_topics(user_id), weak_topics(technology)          │ │
│  │  FOREIGN KEYS:                                            │ │
│  │  • results.user_id → users.id (CASCADE)                  │ │
│  │  • test_answers.result_id → results.id (CASCADE)         │ │
│  │  • test_answers.question_id → questions.id (CASCADE)     │ │
│  │  • weak_topics.user_id → users.id (CASCADE)             │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Diagrams

### 1. User Registration Flow

```
User fills form
      ↓
POST /register.php
      ↓
AuthController::registerUser()
      ↓
[Validate Input]
├─ Check username format
├─ Check email format
├─ Check password length
├─ Verify passwords match
      ↓
[Check Uniqueness]
├─ User.emailExists()
├─ User.userExists()
      ↓
[Hash Password]
├─ password_hash($password, PASSWORD_BCRYPT)
      ↓
[Save to Database]
├─ INSERT INTO users
      ↓
Success / Error Response
```

### 2. Test Taking Flow

```
User clicks "Take Test"
      ↓
Select Technology (PHP/Java/React)
      ↓
GET /test.php?technology=PHP&start=1
      ↓
[Check Session]
├─ Verify user logged in
      ↓
[Load Questions]
├─ Question.getRandomQuestions(technology, null, 10)
├─ Fetch 10 random questions
      ↓
[Display Test UI]
├─ Show timer (10 minutes)
├─ Display questions
├─ Show answer options
      ↓
User Answers Questions (10 minutes)
      ↓
[Time Expires OR User Submits]
├─ POST /submit_test.php
      ↓
[Process Answers]
├─ For each question:
│  ├─ Get correct answer
│  ├─ Compare with user answer
│  ├─ Update correct_count / wrong_count
│  ├─ Track weak topic
      ↓
[Save Result]
├─ Result.saveResult()
├─ INSERT INTO results
├─ INSERT INTO test_answers (each answer)
├─ UPDATE weak_topics
      ↓
[Display Result Page]
├─ Show score, accuracy, comparison
```

### 3. Weak Topic Detection

```
User answers question wrong
      ↓
Result.saveAnswer()
      ↓
Result.updateWeakTopic(user_id, technology, topic, false)
      ↓
[Check existing weak topic]
├─ SELECT FROM weak_topics WHERE user_id=? AND technology=? AND topic=?
      ↓
[Update or Insert]
├─ INSERT ... ON DUPLICATE KEY UPDATE
├─ Increment wrong_count
      ↓
[Check threshold]
├─ If wrong_count >= 3:
│  └─ This topic is weak
      ↓
[Display Recommendation]
├─ On dashboard & results page
├─ "You need improvement in [topic]"
```

### 4. Performance Dashboard Flow

```
User views dashboard
      ↓
GET /dashboard.php
      ↓
[Load Statistics]
├─ Result.getDashboardStats(user_id)
│  ├─ COUNT(*) - total tests
│  ├─ AVG(accuracy_percentage) - average accuracy
│  ├─ MAX(score) - best score
│  ├─ AVG(score) - average score
      ↓
[Load Chart Data]
├─ Result.getPerformanceData(user_id)
├─ Fetch last 20 results
├─ Extract: test_date, accuracy_percentage
      ↓
[Load Weak Topics]
├─ Result.getWeakTopics(user_id)
├─ Fetch topics with wrong_count >= 3
      ↓
[Render Page]
├─ Display stat cards
├─ Initialize Chart.js
├─ Show weak topics list
```

### 5. Admin Question Management

```
Admin clicks "Manage Questions"
      ↓
GET /admin/questions.php
      ↓
[Show Questions List]
├─ Question.getAllQuestions() with filters
├─ Display table with all questions
      ↓
Admin selects action:
│
├─ ADD QUESTION:
│  ├─ GET ?action=add
│  ├─ Show form
│  ├─ POST action=add
│  ├─ Question.addQuestion() [with validation]
│  ├─ INSERT INTO questions
│  └─ Redirect to list
│
├─ EDIT QUESTION:
│  ├─ GET ?action=edit&id=5
│  ├─ Fetch question data
│  ├─ Show pre-filled form
│  ├─ POST action=update
│  ├─ Question.updateQuestion()
│  ├─ UPDATE questions WHERE id=?
│  └─ Redirect to list
│
└─ DELETE QUESTION:
   ├─ Button click triggers modal
   ├─ POST action=delete&id=5
   ├─ Question.deleteQuestion()
   ├─ DELETE FROM questions WHERE id=?
   └─ Redirect to list
```

---

## Security Architecture

```
┌────────────────────────────────────────┐
│         INPUT VALIDATION LAYER         │
├────────────────────────────────────────┤
│ • Email regex validation               │
│ • Username pattern check               │
│ • Password length validation           │
│ • Required field checking              │
└────────────────────────────────────────┘
                    ↓
┌────────────────────────────────────────┐
│      PREPARED STATEMENTS LAYER         │
├────────────────────────────────────────┤
│ • MySQLi prepared statements           │
│ • Parameter binding                    │
│ • No string concatenation              │
│ • SQL injection prevention             │
└────────────────────────────────────────┘
                    ↓
┌────────────────────────────────────────┐
│         OUTPUT ENCODING LAYER          │
├────────────────────────────────────────┤
│ • htmlspecialchars() on all output     │
│ • XSS prevention                       │
│ • Safe HTML rendering                 │
└────────────────────────────────────────┘
                    ↓
┌────────────────────────────────────────┐
│         PASSWORD HASHING LAYER         │
├────────────────────────────────────────┤
│ • password_hash() with BCRYPT          │
│ • password_verify() for comparison     │
│ • No plaintext storage                 │
└────────────────────────────────────────┘
                    ↓
┌────────────────────────────────────────┐
│         SESSION MANAGEMENT LAYER       │
├────────────────────────────────────────┤
│ • session_start() on each page         │
│ • User/Admin type verification         │
│ • Session destroy on logout            │
│ • Access control checks                │
└────────────────────────────────────────┘
```

---

## Database Relationships

```
                    ┌─────────────┐
                    │   users     │
                    ├─────────────┤
                    │ id (PK)     │
                    │ username    │
                    │ email       │
                    │ password    │
                    │ full_name   │
                    └────┬────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ↓                ↓                ↓
   ┌─────────┐   ┌──────────────┐  ┌──────────────┐
   │ results │   │ weak_topics  │  │ test_answers │
   ├─────────┤   ├──────────────┤  ├──────────────┤
   │ id (PK) │   │ id (PK)      │  │ id (PK)      │
   │ user_id │──→│ user_id (FK) │  │ result_id ───┐
   │ score   │   │ technology   │  │ question_id  │
   │ accuracy│   │ topic        │  │ user_answer  │
   │ date    │   │ wrong_count  │  │ is_correct   │
   └────┬────┘   └──────────────┘  └──────────────┘
        │
        └─→ questions (referenced via test_answers)
            ┌──────────────┐
            │  questions   │
            ├──────────────┤
            │ id (PK)      │
            │ technology   │
            │ topic        │
            │ question...  │
            │ options      │
            │ correct_ans  │
            └──────────────┘

                ┌─────────────┐
                │    admin    │
                ├─────────────┤
                │ id (PK)     │
                │ username    │
                │ email       │
                │ password    │
                │ full_name   │
                └─────────────┘
```

---

## Authentication Flow

```
┌─ PUBLIC USER
│  ├─ index.php
│  ├─ register.php
│  ├─ login.php
│  └─ admin/login.php
│
├─ AUTHENTICATED USER
│  ├─ Check: isset($_SESSION['user_id'])
│  ├─ Check: $_SESSION['user_type'] === 'user'
│  ├─ Access:
│  │  ├─ dashboard.php
│  │  ├─ test.php
│  │  ├─ results.php
│  │  └─ profile.php
│  └─ Denied:
│     └─ admin/* pages
│
├─ AUTHENTICATED ADMIN
│  ├─ Check: isset($_SESSION['admin_id'])
│  ├─ Check: $_SESSION['user_type'] === 'admin'
│  ├─ Access:
│  │  ├─ admin/dashboard.php
│  │  ├─ admin/questions.php
│  │  ├─ admin/users.php
│  │  └─ admin/profile.php
│  └─ Denied:
│     └─ User pages
│
└─ NOT AUTHENTICATED
   └─ Redirect to login.php
```

---

## Class Hierarchy

```
┌─────────────────────────────────────┐
│      Database Connection             │
│  (config/Database.php)               │
│  ├─ connect()                        │
│  ├─ prepare()                        │
│  ├─ escape()                         │
│  └─ closeConnection()                │
└─────────────────────────────────────┘
            ↑
            │ (instantiated by)
            │
┌──────────────────────────────────────┐
│      Model Classes                   │
│  (models/*.php)                      │
│                                      │
│  ┌─ User                             │
│  │  ├─ register()                    │
│  │  ├─ login()                       │
│  │  └─ getUserById()                 │
│  │                                   │
│  ┌─ Question                         │
│  │  ├─ getRandomQuestions()          │
│  │  ├─ getAllQuestions()             │
│  │  ├─ addQuestion()                 │
│  │  └─ deleteQuestion()              │
│  │                                   │
│  ┌─ Result                           │
│  │  ├─ saveResult()                  │
│  │  ├─ getUserResults()              │
│  │  ├─ getDashboardStats()           │
│  │  └─ getWeakTopics()               │
│  │                                   │
│  └─ Admin                            │
│     ├─ login()                       │
│     └─ getAdminById()                │
└──────────────────────────────────────┘
            ↑
            │ (used by)
            │
┌──────────────────────────────────────┐
│      Controller Classes              │
│  (controllers/*.php)                 │
│                                      │
│  └─ AuthController                   │
│     ├─ registerUser()                │
│     ├─ loginUser()                   │
│     ├─ loginAdmin()                  │
│     └─ logout()                      │
└──────────────────────────────────────┘
            ↑
            │ (called by)
            │
┌──────────────────────────────────────┐
│      View Templates                  │
│  (views/*.php, *.php pages)          │
└──────────────────────────────────────┘
```

---

## File Access Permissions

```
Recommended XAMPP Permissions:
├─ Folders: 755 (rwxr-xr-x)
├─ PHP Files: 644 (rw-r--r--)
├─ Database: root user only
└─ Logs: 664 (rw-rw-r--)

Protected Directories:
├─ config/ - Connection strings
├─ models/ - Business logic
├─ controllers/ - Core logic
└─ assets/ - Public (readable)
```

---

## Performance Optimization Strategy

```
Database Level:
├─ Indexes on frequently queried columns
├─ Foreign keys for referential integrity
├─ Prepared statements reduce overhead
└─ Aggregate functions in MySQL

Application Level:
├─ Query results cached in memory
├─ Minimal database queries
├─ No N+1 query problems
└─ Proper error handling

Frontend Level:
├─ Bootstrap CSS from CDN
├─ Chart.js library from CDN
├─ Vanilla JavaScript (no jQuery)
└─ Responsive images

Browser Caching:
├─ Static CSS/JS files
├─ Image assets
└─ External library files
```

---

This architecture ensures:
✅ **Security** - Multiple security layers
✅ **Scalability** - Clean separation of concerns
✅ **Maintainability** - Well-organized code
✅ **Performance** - Optimized queries
✅ **Reliability** - Proper error handling
