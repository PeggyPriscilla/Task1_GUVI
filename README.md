# User Registration & Profile Management System

A complete full-stack web application with user registration, authentication, and profile management built with PHP, MySQL, MongoDB, and Redis.

## 🎯 Project Requirements

### Problem Statement
Create a signup page where a user can register and a login page to log in with the details provided during registration. Successful login should redirect to a profile page which should contain additional details such as age, DOB, contact, etc. The user can update these details.

**Flow**: Register → Login → Profile

### Technical Requirements
- ✅ HTML, JS, CSS, and PHP code in separate files (no code co-existence)
- ✅ Only jQuery AJAX for backend interaction (no form submissions)
- ✅ Bootstrap CSS for responsive design
- ✅ MySQL for storing registered data with prepared statements only
- ✅ MongoDB for storing user profile details
- ✅ Browser localStorage for session management (no PHP sessions)
- ✅ Redis for backend session storage

### Tech Stack
- **Frontend**: HTML5, CSS3, JavaScript, jQuery, Bootstrap 5
- **Backend**: PHP 8.4+
- **Databases**: MySQL, MongoDB
- **Cache**: Redis
- **Session**: Browser localStorage + Redis backend

## 🏗️ Project Structure

```
log_peggy/
├── backend/                    # PHP backend API endpoints
│   ├── register.php           # User registration endpoint
│   ├── login.php             # User authentication endpoint
│   ├── get_profile.php       # Profile data retrieval
│   ├── update_profile.php    # Profile data update
│   └── logout.php            # Session termination
├── css/
│   └── style.css             # Custom styles and Bootstrap overrides
├── db/
│   ├── config.php            # Database configurations and connections
│   └── setup.sql             # Database schema creation
├── js/                       # JavaScript files for each page
│   ├── main.js               # SPA navigation and core functionality
│   ├── register.js           # Registration form handling
│   ├── login.js              # Login form handling
│   └── profile.js            # Profile management
├── pages/                    # HTML page templates
│   ├── register.html         # Registration form
│   ├── login.html            # Login form
│   └── profile.html          # Profile management form
├── data/                     # MongoDB fallback storage (demo)
│   └── user_profiles.json    # Profile data storage
├── index.html                # Main application entry point
├── status.php                # System status checker
└── composer.json             # PHP dependencies (optional)
```

## 🚀 Installation & Setup

### Prerequisites

#### For Mac:
1. **Homebrew** (package manager)
   ```bash
   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
   ```

2. **PHP 8.4+**
   ```bash
   brew install php
   ```

3. **MySQL**
   ```bash
   brew install mysql
   brew services start mysql
   ```

4. **MongoDB**
   ```bash
   brew tap mongodb/brew
   brew install mongodb-community
   brew services start mongodb/brew/mongodb-community
   ```

5. **Redis**
   ```bash
   brew install redis
   brew services start redis
   ```

#### For Windows:

1. **XAMPP** (includes PHP, MySQL)
   - Download from: https://www.apachefriends.org/
   - Install and start Apache + MySQL services

2. **MongoDB**
   - Download from: https://www.mongodb.com/try/download/community
   - Install as Windows Service
   - Start MongoDB service

3. **Redis**
   - Download from: https://github.com/microsoftarchive/redis/releases
   - Extract and run `redis-server.exe`
   - Or use Redis on WSL2

### Database Setup

1. **Create MySQL Database**
   ```bash
   # Mac/Linux
   mysql -u root -p < db/setup.sql
   
   # Windows (XAMPP)
   # Use phpMyAdmin or MySQL command line:
   # mysql -u root < db/setup.sql
   ```

2. **Verify Services**
   ```bash
   # Check if all services are running
   curl http://localhost:8000/status.php
   ```

### Application Setup

1. **Clone/Download the Project**
   ```bash
   git clone https://github.com/PeggyPriscilla/log_peggy
   cd log_peggy
   ```

2. **Start PHP Development Server**
   ```bash
   # Mac/Linux
   php -S localhost:8000
   
   # Windows (if PHP in PATH)
   php -S localhost:8000
   
   # Windows (XAMPP - copy project to htdocs folder)
   # Access via: http://localhost/log_peggy
   ```

3. **Access Application**
   - Open browser: http://localhost:8000
   - You should see the registration page

## 🔧 Configuration

### Database Configuration (`db/config.php`)

```php
// MySQL Configuration
$mysql_host = "localhost";
$mysql_user = "root";
$mysql_password = "";
$mysql_database = "log_peggy";

// MongoDB Configuration (with fallback)
$mongodb_connection_string = "mongodb://localhost:27017";
$mongodb_database = "log_peggy";

// Redis Configuration
$redis_host = "localhost";
$redis_port = 6379;
```

### Fallback Systems

The application includes robust fallback mechanisms:
- **MongoDB**: Uses file-based storage if MongoDB extension unavailable
- **Redis**: Uses command-line interface if PHP extension unavailable
- **Session**: Browser localStorage with server-side validation

## 📱 Usage

### 1. User Registration
1. Navigate to http://localhost:8000
2. Fill in the registration form:
   - Full Name
   - Email Address
   - Password
3. Click "Register"
4. Success redirects to login page

### 2. User Login
1. Enter registered email and password
2. Click "Login"
3. Success redirects to profile page

### 3. Profile Management
1. After login, access profile page
2. Fill/update profile information:
   - Age
   - Date of Birth
   - Contact Number
   - City
   - Address
   - Occupation
   - Company
3. Click "Update Profile"
4. Data is stored in MongoDB

### 4. Session Management
- Sessions persist across browser refreshes
- Automatic logout after 24 hours
- Manual logout available
- Session data stored in Redis backend

## 🛠️ API Endpoints

### POST /backend/register.php
**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securepassword123"
}
```
**Response:**
```json
{
  "status": "success",
  "message": "User registered successfully"
}
```

### POST /backend/login.php
**Request:**
```json
{
  "email": "john@example.com",
  "password": "securepassword123"
}
```
**Response:**
```json
{
  "status": "success",
  "session_token": "abc123...",
  "user": {
    "id": 1,
    "username": "John Doe",
    "email": "john@example.com"
  }
}
```

### POST /backend/update_profile.php
**Request:**
```json
{
  "session_token": "abc123...",
  "user_id": 1,
  "age": 25,
  "dob": "1998-05-15",
  "contact": "555-1234",
  "city": "New York",
  "address": "123 Main St",
  "occupation": "Developer",
  "company": "Tech Corp"
}
```

### POST /backend/get_profile.php
**Request:**
```json
{
  "session_token": "abc123...",
  "user_id": 1
}
```

## 🔒 Security Features

- **Password Hashing**: PHP `password_hash()` with salt
- **SQL Injection Prevention**: Prepared statements only
- **Session Security**: Cryptographically secure tokens
- **Input Validation**: Server-side validation for all inputs
- **CSRF Protection**: Session token validation

## 🧪 Testing

### Manual Testing Flow
1. **Registration Test**
   ```bash
   curl -X POST -d "name=TestUser&email=test@example.com&password=testpass123" \
     http://localhost:8000/backend/register.php
   ```

2. **Login Test**
   ```bash
   curl -X POST -d "email=test@example.com&password=testpass123" \
     http://localhost:8000/backend/login.php
   ```

3. **Profile Update Test**
   ```bash
   curl -X POST -d "session_token=TOKEN&user_id=1&age=25&contact=555-1234" \
     http://localhost:8000/backend/update_profile.php
   ```

### System Status Check
```bash
curl http://localhost:8000/status.php
```

## 🐛 Troubleshooting

### Common Issues

**1. "MySQL connection failed"**
- Ensure MySQL is running: `brew services list` (Mac) or check XAMPP (Windows)
- Verify database credentials in `db/config.php`
- Create database: `mysql -u root -p < db/setup.sql`

**2. "MongoDB not available"**
- Start MongoDB service: `brew services start mongodb-community` (Mac)
- Application works with file-based fallback if MongoDB unavailable

**3. "Redis not available"**
- Start Redis: `brew services start redis` (Mac) or `redis-server.exe` (Windows)
- Application uses fallback session validation if Redis unavailable

**4. "Permission denied" (Mac)**
- Ensure proper file permissions: `chmod -R 755 log_peggy/`

**5. "Port already in use"**
- Kill existing process: `lsof -ti:8000 | xargs kill -9`
- Or use different port: `php -S localhost:8001`

### Debug Mode

Enable PHP error reporting by adding to `db/config.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## 📊 Performance Features

- **SPA Architecture**: Fast page transitions without full reloads
- **AJAX Communication**: No page refreshes for form submissions  
- **Session Caching**: Redis backend for fast session retrieval
- **Prepared Statements**: Optimized database queries
- **Bootstrap CDN**: Fast CSS/JS loading

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -am 'Add feature'`
4. Push branch: `git push origin feature-name`
5. Submit pull request

## 📄 License

This project is created for internship demonstration purposes.

## 👨‍💻 Author

**PEGGY PRISCILLA MARIE J** - Developer Internship Project @GUVI

---

## 📞 Support

For issues or questions:
1. Check the troubleshooting section
2. Verify system status with `status.php`
3. Check PHP error logs
4. Ensure all services (MySQL, MongoDB, Redis) are running 
