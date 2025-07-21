# 📖 Project Files Explanation

This document provides a detailed explanation of each file in the User Registration & Profile Management System and how they work together.

## 📂 File Structure Overview

```
log_peggy/
├── 🌐 Frontend Files
├── 🔧 Backend API Files
├── 🗄️ Database & Configuration
├── 📊 Data Storage
└── 📋 Documentation
```

---

## 🌐 Frontend Files

### `index.html` - Main Entry Point
**Purpose**: Single Page Application (SPA) container and entry point.

**Key Features**:
- Bootstrap 5 CDN integration for responsive design
- jQuery 3.7.1 CDN for AJAX operations
- SPA container with dynamic content loading
- Navigation menu with logout functionality

**Code Structure**:
```html
<!-- Bootstrap CSS for responsive design -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<!-- Main SPA container -->
<div id="app-container"></div>

<!-- jQuery and core JavaScript -->
<script src="js/main.js"></script>
```

**How it works**:
1. Loads as the main page when user visits the application
2. Checks localStorage for existing session
3. Dynamically loads appropriate page (register/login/profile)
4. Provides navigation structure for the entire application

---

### `css/style.css` - Custom Styling
**Purpose**: Custom CSS styles and Bootstrap overrides for consistent UI design.

**Key Features**:
- Responsive design enhancements
- Custom form styling
- Loading states and animations
- Brand colors and typography

**Major Styles**:
```css
/* Custom form styling */
.form-container {
    max-width: 400px;
    margin: 2rem auto;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Button enhancements */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Loading states */
.loading {
    pointer-events: none;
    opacity: 0.6;
}
```

**Integration**:
- Works with Bootstrap classes
- Provides consistent visual theme
- Enhances user experience with smooth animations

---

### `pages/register.html` - Registration Form
**Purpose**: User registration form template for new user signup.

**Key Components**:
- **Name Input**: Full name field with validation
- **Email Input**: Email field with HTML5 validation
- **Password Input**: Password field with security requirements
- **Submit Button**: AJAX form submission
- **Navigation Link**: Link to login page

**Form Structure**:
```html
<form id="registerForm">
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Register</button>
</form>
```

**Validation**:
- Client-side HTML5 validation
- Server-side PHP validation
- Real-time feedback to users
- Security: Password hashing on backend

**User Flow**:
1. User fills registration form
2. JavaScript validates input
3. AJAX call to `backend/register.php`
4. Success: Redirect to login page
5. Error: Display error message

---

### `pages/login.html` - Login Form
**Purpose**: User authentication form for existing users.

**Key Components**:
- **Email Input**: Registered email address
- **Password Input**: User password
- **Remember Session**: Implicit through localStorage
- **Submit Button**: AJAX authentication
- **Registration Link**: Navigation to signup

**Authentication Flow**:
```html
<form id="loginForm">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
```

**Security Features**:
- Password field is masked
- Credentials sent via AJAX POST
- Session token received and stored
- Automatic redirect on success

**Integration**:
- Works with `js/login.js` for functionality
- Connects to `backend/login.php` for authentication
- Stores session data in localStorage and Redis

---

### `pages/profile.html` - Profile Management
**Purpose**: User profile form for viewing and updating additional user information.

**Key Components**:
- **Personal Info**: Age, Date of Birth
- **Contact Details**: Phone, City, Address
- **Professional Info**: Occupation, Company
- **Action Buttons**: Update Profile, Logout

**Profile Fields**:
```html
<form id="profileForm">
    <!-- Personal Information -->
    <input type="number" name="age" placeholder="Age">
    <input type="date" name="dob" placeholder="Date of Birth">
    
    <!-- Contact Information -->
    <input type="tel" name="contact" placeholder="Phone Number">
    <input type="text" name="city" placeholder="City">
    <textarea name="address" placeholder="Address"></textarea>
    
    <!-- Professional Information -->
    <input type="text" name="occupation" placeholder="Occupation">
    <input type="text" name="company" placeholder="Company">
    
    <button type="submit">Update Profile</button>
</form>
```

**Features**:
- Auto-populates existing profile data
- Real-time AJAX updates
- Form validation
- Success/error feedback
- Secure logout functionality

---

## 💻 JavaScript Files

### `js/main.js` - SPA Core & Navigation
**Purpose**: Single Page Application router and core functionality manager.

**Key Functions**:

1. **`loadPage(page)`** - Dynamic page loading
   ```javascript
   function loadPage(page) {
       // Fetch HTML template
       fetch(`pages/${page}.html`)
           .then(response => response.text())
           .then(html => {
               // Inject HTML into container
               document.getElementById('app-container').innerHTML = html;
               // Load corresponding JavaScript
               loadScript(`js/${page}.js`);
           });
   }
   ```

2. **`loadScript(src)`** - Dynamic script injection
   ```javascript
   function loadScript(src) {
       // Remove old scripts to prevent conflicts
       const oldScript = document.querySelector(`script[src="${src}"]`);
       if (oldScript) oldScript.remove();
       
       // Inject new script
       const script = document.createElement('script');
       script.src = src;
       document.head.appendChild(script);
   }
   ```

3. **Session Management**:
   ```javascript
   function checkSession() {
       const sessionData = localStorage.getItem('sessionData');
       if (sessionData) {
           const session = JSON.parse(sessionData);
           return session.session_token;
       }
       return null;
   }
   ```

**Navigation Flow**:
1. Application starts → Check session
2. Session exists → Load profile page
3. No session → Load register page
4. User navigates → Dynamic page loading
5. Scripts loaded → Page-specific functionality active

---

### `js/register.js` - Registration Form Handler
**Purpose**: Handles user registration form submission and validation.

**Key Functions**:

1. **Form Event Binding**:
   ```javascript
   setTimeout(() => {
       const form = document.getElementById('registerForm');
       if (form) {
           form.addEventListener('submit', handleRegistration);
       }
   }, 100); // Delay to ensure DOM is ready
   ```

2. **Registration Handler**:
   ```javascript
   function handleRegistration(e) {
       e.preventDefault();
       
       const formData = new FormData(e.target);
       
       $.ajax({
           url: 'backend/register.php',
           type: 'POST',
           data: formData,
           processData: false,
           contentType: false,
           success: function(response) {
               const data = JSON.parse(response);
               if (data.status === 'success') {
                   alert('Registration successful! Redirecting to login...');
                   loadPage('login');
               }
           },
           error: function() {
               alert('Registration failed. Please try again.');
           }
       });
   }
   ```

**Validation**:
- Client-side form validation
- AJAX error handling
- User feedback with alerts
- Automatic redirection on success

**Security**:
- FormData for secure transmission
- No client-side password storage
- Immediate form clearing after submission

---

### `js/login.js` - Login Form Handler
**Purpose**: Handles user authentication and session establishment.

**Key Functions**:

1. **Login Handler**:
   ```javascript
   function handleLogin(e) {
       e.preventDefault();
       
       const formData = new FormData(e.target);
       
       $.ajax({
           url: 'backend/login.php',
           type: 'POST',
           data: formData,
           processData: false,
           contentType: false,
           success: function(response) {
               const data = JSON.parse(response);
               if (data.status === 'success') {
                   // Store session data
                   localStorage.setItem('sessionData', JSON.stringify({
                       session_token: data.session_token,
                       user: data.user
                   }));
                   
                   alert('Login successful!');
                   loadPage('profile');
               }
           }
       });
   }
   ```

**Session Management**:
- Stores session token in localStorage
- Stores user data for profile display
- Automatic redirection to profile
- Error handling for failed logins

**Security Features**:
- Credentials never stored in localStorage
- Session token used for API authentication
- Automatic session validation

---

### `js/profile.js` - Profile Management Handler
**Purpose**: Manages profile data display, updates, and user logout.

**Key Functions**:

1. **Profile Loading**:
   ```javascript
   function loadProfile() {
       const sessionData = JSON.parse(localStorage.getItem('sessionData'));
       
       $.ajax({
           url: 'backend/get_profile.php',
           type: 'POST',
           data: {
               session_token: sessionData.session_token,
               user_id: sessionData.user.id
           },
           success: function(response) {
               const data = JSON.parse(response);
               if (data.status === 'success' && data.profile) {
                   populateForm(data.profile);
               }
           }
       });
   }
   ```

2. **Profile Update**:
   ```javascript
   function handleProfileUpdate(e) {
       e.preventDefault();
       
       const sessionData = JSON.parse(localStorage.getItem('sessionData'));
       const formData = new FormData(e.target);
       formData.append('session_token', sessionData.session_token);
       formData.append('user_id', sessionData.user.id);
       
       $.ajax({
           url: 'backend/update_profile.php',
           type: 'POST',
           data: formData,
           processData: false,
           contentType: false,
           success: function(response) {
               const data = JSON.parse(response);
               alert(data.message);
           }
       });
   }
   ```

3. **Logout Functionality**:
   ```javascript
   function handleLogout() {
       const sessionData = JSON.parse(localStorage.getItem('sessionData'));
       
       $.ajax({
           url: 'backend/logout.php',
           type: 'POST',
           data: { session_token: sessionData.session_token },
           complete: function() {
               localStorage.removeItem('sessionData');
               loadPage('register');
           }
       });
   }
   ```

**Features**:
- Automatic profile data loading
- Real-time form updates
- Secure logout with session cleanup
- User feedback for all operations

---

## 🔧 Backend API Files

### `backend/register.php` - User Registration API
**Purpose**: Handles new user registration with secure password storage.

**Key Functions**:

1. **Input Validation**:
   ```php
   // Validate required fields
   if (empty($username) || empty($email) || empty($password)) {
       echo json_encode(["status" => "error", "message" => "All fields are required"]);
       exit;
   }
   
   // Validate email format
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       echo json_encode(["status" => "error", "message" => "Invalid email format"]);
       exit;
   }
   ```

2. **Email Uniqueness Check**:
   ```php
   $check_stmt = $mysql_conn->prepare("SELECT id FROM users WHERE email = ?");
   $check_stmt->bind_param("s", $email);
   $check_stmt->execute();
   $result = $check_stmt->get_result();
   
   if ($result->num_rows > 0) {
       echo json_encode(["status" => "error", "message" => "Email already registered"]);
       exit;
   }
   ```

3. **Secure Password Storage**:
   ```php
   // Hash password securely
   $hashed_password = password_hash($password, PASSWORD_DEFAULT);
   
   // Insert with prepared statement
   $stmt = $mysql_conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
   $stmt->bind_param("sss", $username, $email, $hashed_password);
   ```

**Security Features**:
- Prepared statements prevent SQL injection
- Password hashing with salt
- Email validation and uniqueness
- Error handling without data exposure

**Database Schema**:
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### `backend/login.php` - Authentication API
**Purpose**: Authenticates users and creates secure sessions.

**Key Functions**:

1. **User Authentication**:
   ```php
   // Get user data with prepared statement
   $stmt = $mysql_conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
   $stmt->bind_param("s", $email);
   $stmt->execute();
   $result = $stmt->get_result();
   $user = $result->fetch_assoc();
   
   // Verify password
   if (password_verify($password, $user['password'])) {
       // Authentication successful
   }
   ```

2. **Session Creation**:
   ```php
   // Generate cryptographically secure token
   $session_token = bin2hex(random_bytes(32));
   
   // Store session data
   $session_data = [
       'user_id' => $user['id'],
       'email' => $user['email'],
       'username' => $user['username'],
       'login_time' => time()
   ];
   
   // Store in Redis with 24-hour expiry
   redis_set($session_token, $session_data, 86400);
   ```

3. **Response Generation**:
   ```php
   echo json_encode([
       'status' => 'success', 
       'session_token' => $session_token,
       'user' => [
           'id' => $user['id'],
           'username' => $user['username'],
           'email' => $user['email']
       ]
   ]);
   ```

**Security Features**:
- Password verification with PHP's secure functions
- Cryptographically secure session tokens
- Session expiry (24 hours)
- No sensitive data in response

---

### `backend/get_profile.php` - Profile Retrieval API
**Purpose**: Retrieves user profile data from MongoDB with session validation.

**Key Functions**:

1. **Session Validation**:
   ```php
   $session_valid = false;
   if ($redis) {
       $session_data = redis_get($session_token);
       if ($session_data && isset($session_data['user_id']) && $session_data['user_id'] == $user_id) {
           $session_valid = true;
       }
   }
   ```

2. **Profile Data Retrieval**:
   ```php
   // Get profile from MongoDB (or fallback file storage)
   $profiles = mongodb_find('user_profiles', ['user_id' => intval($user_id)]);
   
   if (!empty($profiles)) {
       $profile = $profiles[0];
       $profile_data = [
           'age' => $profile['age'] ?? '',
           'dob' => $profile['dob'] ?? '',
           'contact' => $profile['contact'] ?? '',
           'city' => $profile['city'] ?? '',
           'address' => $profile['address'] ?? '',
           'occupation' => $profile['occupation'] ?? '',
           'company' => $profile['company'] ?? ''
       ];
   }
   ```

**Data Flow**:
1. Validate session token in Redis
2. Check user authorization
3. Query MongoDB for profile data
4. Return formatted profile data
5. Handle empty profiles gracefully

---

### `backend/update_profile.php` - Profile Update API
**Purpose**: Updates user profile information in MongoDB with validation.

**Key Functions**:

1. **Data Collection & Validation**:
   ```php
   $profile_data = [
       'user_id' => intval($user_id),
       'age' => !empty($_POST['age']) ? intval($_POST['age']) : null,
       'dob' => $_POST['dob'] ?? null,
       'contact' => $_POST['contact'] ?? null,
       'city' => $_POST['city'] ?? null,
       'address' => $_POST['address'] ?? null,
       'occupation' => $_POST['occupation'] ?? null,
       'company' => $_POST['company'] ?? null,
       'updated_at' => date('Y-m-d H:i:s')
   ];
   
   // Remove empty values
   $profile_data = array_filter($profile_data, function($value) {
       return $value !== null && $value !== '';
   });
   ```

2. **MongoDB Operations**:
   ```php
   // Check if profile exists
   $existing = mongodb_find('user_profiles', ['user_id' => intval($user_id)]);
   
   if ($existing) {
       // Update existing profile
       $result = mongodb_update('user_profiles', ['user_id' => intval($user_id)], $profile_data);
   } else {
       // Insert new profile
       $result = mongodb_insert('user_profiles', $profile_data);
   }
   ```

**Features**:
- Upsert functionality (update or insert)
- Data validation and sanitization
- Timestamp management
- MongoDB integration with fallbacks

---

### `backend/logout.php` - Session Termination API
**Purpose**: Cleans up user sessions from Redis backend storage.

**Key Functions**:

1. **Session Cleanup**:
   ```php
   if (!empty($session_token) && $redis) {
       // Delete session from Redis
       $result = redis_del($session_token);
       if ($result) {
           echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
       }
   }
   ```

**Security**:
- Removes session from Redis
- Prevents session reuse
- Graceful handling of missing sessions
- Frontend clears localStorage

---

## 🗄️ Database & Configuration

### `db/config.php` - Database Configuration & Connections
**Purpose**: Centralized database configuration and connection management with fallback systems.

**Key Components**:

1. **MySQL Connection**:
   ```php
   $mysql_conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
   if ($mysql_conn->connect_error) {
       error_log("MySQL Connection failed: " . $mysql_conn->connect_error);
       $mysql_conn = null;
   }
   ```

2. **MongoDB Connection (with Fallback)**:
   ```php
   // Test MongoDB connection via HTTP
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, 'http://localhost:27017');
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_TIMEOUT, 2);
   $response = curl_exec($ch);
   
   if ($response !== false && strpos($response, 'MongoDB') !== false) {
       $mongodb = true; // Use fallback file storage
   }
   ```

3. **Redis Helper Functions**:
   ```php
   function redis_set($key, $value, $ttl = 3600) {
       $json_value = json_encode($value);
       $command = "redis-cli SETEX " . escapeshellarg($key) . " $ttl " . escapeshellarg($json_value);
       $output = shell_exec($command);
       return trim($output) === 'OK';
   }
   
   function redis_get($key) {
       $command = "redis-cli GET " . escapeshellarg($key);
       $output = shell_exec($command);
       if ($output && trim($output) !== '(nil)') {
           return json_decode(trim($output), true);
       }
       return null;
   }
   ```

4. **MongoDB Fallback Functions**:
   ```php
   function mongodb_insert($collection, $document) {
       $file = __DIR__ . "/../data/{$collection}.json";
       if (!file_exists(__DIR__ . "/../data/")) {
           mkdir(__DIR__ . "/../data/", 0755, true);
       }
       
       $data = [];
       if (file_exists($file)) {
           $data = json_decode(file_get_contents($file), true) ?? [];
       }
       
       $document['_id'] = uniqid();
       $document['created_at'] = date('Y-m-d H:i:s');
       $data[] = $document;
       
       return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
   }
   ```

**Fallback Strategy**:
- **MongoDB**: File-based JSON storage when extension unavailable
- **Redis**: Command-line interface when PHP extension unavailable
- **Graceful Degradation**: Application works regardless of extension availability

---

### `db/setup.sql` - Database Schema
**Purpose**: MySQL database schema creation and initial setup.

**Tables Created**:

1. **Users Table**:
   ```sql
   CREATE TABLE IF NOT EXISTS users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) UNIQUE NOT NULL,
       email VARCHAR(100) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

2. **Profiles Table (Fallback)**:
   ```sql
   CREATE TABLE IF NOT EXISTS profiles (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       age INT,
       dob DATE,
       contact VARCHAR(20),
       city VARCHAR(100),
       address TEXT,
       occupation VARCHAR(100),
       company VARCHAR(100),
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
       UNIQUE KEY unique_user_profile (user_id)
   );
   ```

**Features**:
- Foreign key relationships
- Unique constraints
- Automatic timestamps
- Cascading deletes

---

## 📊 Data Storage

### `data/user_profiles.json` - MongoDB Fallback Storage
**Purpose**: File-based storage for user profiles when MongoDB is unavailable.

**Structure**:
```json
[
    {
        "user_id": 1,
        "age": 25,
        "dob": "1998-05-15",
        "contact": "555-1234",
        "city": "TestCity",
        "occupation": "Developer",
        "company": "TestCorp",
        "updated_at": "2025-07-21 00:19:23",
        "_id": "687d878b27a0c",
        "created_at": "2025-07-21 00:19:23"
    }
]
```

**Features**:
- JSON format for easy parsing
- Unique ID generation
- Timestamp tracking
- Searchable by user_id

**Integration**:
- Used by `mongodb_*` helper functions
- Transparent to application logic
- Maintains data consistency
- Easy migration to real MongoDB

---

## 🔍 System Monitoring

### `status.php` - System Status Checker
**Purpose**: Provides real-time status of all system components.

**Checks Performed**:
1. **MySQL Connection**: Database connectivity
2. **MongoDB Availability**: Service status with fallback info
3. **Redis Connection**: Cache service status
4. **PHP Version**: Runtime environment info
5. **Timestamp**: Current server time

**Response Format**:
```json
{
    "mysql": true,
    "mongodb": true,
    "redis": true,
    "php_version": "8.4.10",
    "timestamp": "2025-07-21 00:16:55",
    "mongodb_status": "Connected (using fallback file storage for demo)",
    "redis_status": "Connected (using socket connection for demo)"
}
```

**Usage**:
- System health monitoring
- Debugging connection issues
- Deployment verification
- Service status dashboard

---

## 🔗 How Everything Works Together

### Application Flow:

1. **Initial Load**:
   ```
   Browser → index.html → main.js → checkSession() → loadPage()
   ```

2. **User Registration**:
   ```
   register.html → register.js → backend/register.php → MySQL → success/error
   ```

3. **User Login**:
   ```
   login.html → login.js → backend/login.php → MySQL + Redis → localStorage
   ```

4. **Profile Management**:
   ```
   profile.html → profile.js → backend/get_profile.php → MongoDB/Fallback
                            → backend/update_profile.php → MongoDB/Fallback
   ```

5. **Session Management**:
   ```
   localStorage (Frontend) ↔ Redis (Backend) ↔ Session Validation
   ```

### Data Flow:

1. **Registration Data**: `Frontend → PHP → MySQL`
2. **Authentication**: `Frontend → PHP → MySQL → Redis → localStorage`
3. **Profile Data**: `Frontend → PHP → MongoDB/Fallback`
4. **Session Data**: `Frontend localStorage ↔ Backend Redis`

### Security Chain:

1. **Input Validation**: Frontend + Backend
2. **SQL Injection**: Prepared statements
3. **Password Security**: Hashing + salting
4. **Session Security**: Secure tokens + expiry
5. **CSRF Protection**: Session validation

This architecture ensures a robust, scalable, and secure application that meets all internship requirements while providing fallback mechanisms for reliability.

---

## 🚀 Performance Optimizations

### Frontend:
- **SPA Architecture**: No full page reloads
- **CDN Resources**: Bootstrap and jQuery from CDN
- **Lazy Loading**: Scripts loaded only when needed
- **Caching**: Browser caching for static assets

### Backend:
- **Prepared Statements**: Optimized database queries
- **Session Caching**: Redis for fast session retrieval
- **Connection Pooling**: Efficient database connections
- **Error Handling**: Graceful error management

### Database:
- **Indexing**: Primary keys and unique constraints
- **Foreign Keys**: Data integrity and referential consistency
- **Timestamps**: Automatic timestamp management
- **Fallbacks**: File-based storage for reliability

This comprehensive architecture demonstrates professional full-stack development practices suitable for production environments. 