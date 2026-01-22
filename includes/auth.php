<?php
// includes/auth.php - VERSI DIPERBAIKI UNTUK MATHLine
// File ini digunakan untuk autentikasi di sistem MATHLine

class Auth {
    
    /**
     * Constructor - start session jika belum dimulai
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }
    
    /**
     * Check if user has specific role
     * @param string $role Role to check (admin, guru, siswa)
     * @return bool
     */
    public function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Check if user has any of the specified roles
     * @param array $roles Array of roles
     * @return bool
     */
    public function hasAnyRole($roles) {
        if (!isset($_SESSION['role'])) {
            return false;
        }
        
        return in_array($_SESSION['role'], $roles);
    }
    
    /**
     * Require user to be logged in
     * Redirects to appropriate login page if not logged in
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }
    }
    
    /**
     * Require specific role
     * @param string|array $required_role Single role or array of roles
     */
    public function requireRole($required_role) {
        $this->requireLogin();
        
        if (is_array($required_role)) {
            if (!$this->hasAnyRole($required_role)) {
                $this->redirectByRole();
            }
        } else {
            if (!$this->hasRole($required_role)) {
                $this->redirectByRole();
            }
        }
    }
    
    /**
     * Redirect to appropriate login page based on current location
     */
    private function redirectToLogin() {
        $current_uri = $_SERVER['REQUEST_URI'];
        $current_path = $_SERVER['PHP_SELF'];
        
        // Debug info
        if (isset($_GET['debug'])) {
            echo "<!-- DEBUG: Current URI: $current_uri -->";
            echo "<!-- DEBUG: Current Path: $current_path -->";
        }
        
        // Jika di folder admin atau teacher, redirect ke teacher_login.php
        if (strpos($current_uri, '/admin/') !== false || 
            strpos($current_path, '/admin/') !== false ||
            strpos($current_uri, '/teacher/') !== false ||
            strpos($current_path, '/teacher/') !== false) {
            
            // Cek apakah sudah di root atau perlu naik satu level
            if (strpos($current_path, '/admin/') !== false || 
                strpos($current_path, '/teacher/') !== false) {
                header('Location: ../teacher_login.php');
            } else {
                header('Location: teacher_login.php');
            }
        }
        // Jika di folder student, redirect ke student_login.php
        elseif (strpos($current_uri, '/student/') !== false || 
                strpos($current_path, '/student/') !== false) {
            
            if (strpos($current_path, '/student/') !== false) {
                header('Location: ../student_login.php');
            } else {
                header('Location: student_login.php');
            }
        }
        // Default untuk halaman di root atau tidak jelas
        else {
            header('Location: teacher_login.php');
        }
        
        exit();
    }
    
    /**
     * Redirect user based on their role
     */
    private function redirectByRole() {
        if (isset($_SESSION['role'])) {
            switch ($_SESSION['role']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'guru':
                    header('Location: teacher/dashboard.php');
                    break;
                case 'siswa':
                    header('Location: student/dashboard.php');
                    break;
                default:
                    header('Location: index.php');
            }
        } else {
            header('Location: index.php');
        }
        exit();
    }
    
    /**
     * Set user session after successful login
     * @param array $user User data array
     */
    public function setUserSession($user) {
        $_SESSION['user_id'] = $user['id'] ?? null;
        $_SESSION['username'] = $user['username'] ?? null;
        $_SESSION['role'] = $user['role'] ?? null;
        $_SESSION['full_name'] = $user['full_name'] ?? null;
        $_SESSION['email'] = $user['email'] ?? null;
        $_SESSION['nim_nis'] = $user['nim_nis'] ?? null;
        $_SESSION['login_time'] = time();
        $_SESSION['is_authenticated'] = true;
        
        // Log activity
        $this->logLogin($user['id'] ?? 0);
    }
    
    /**
     * Log user login activity
     * @param int $user_id User ID
     */
    private function logLogin($user_id) {
        if ($user_id > 0) {
            try {
                require_once 'db_connection.php';
                $db = Database::getInstance();
                $conn = $db->getConnection();
                
                $stmt = $conn->prepare("INSERT INTO logs (user_id, action, ip_address, user_agent) VALUES (?, 'login', ?, ?)");
                $stmt->bind_param("iss", $user_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                // Silent fail - logging is not critical
                error_log("Failed to log login: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Login user with username and password
     * @param string $username Username
     * @param string $password Password
     * @return bool|array Returns user data on success, false on failure
     */
    public function login($username, $password) {
        $username = trim($username);
        $password = trim($password);
        
        if (empty($username) || empty($password)) {
            return false;
        }
        
        try {
            require_once 'db_connection.php';
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $sql = "SELECT id, username, password, role, full_name, email, nim_nis 
                    FROM users WHERE username = ? LIMIT 1";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                return false;
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Update last login
                    $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    return $user;
                }
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get current user information
     * @return array|null User data or null if not logged in
     */
    public function getUserInfo() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'nim_nis' => $_SESSION['nim_nis'] ?? null,
            'login_time' => $_SESSION['login_time'] ?? null
        ];
    }
    
    /**
     * Get current user role
     * @return string|null Role or null if not logged in
     */
    public function getUserRole() {
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Get current user ID
     * @return int|null User ID or null if not logged in
     */
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Logout current user
     */
    public function logout() {
        // Log logout activity
        if (isset($_SESSION['user_id'])) {
            try {
                require_once 'db_connection.php';
                $db = Database::getInstance();
                $conn = $db->getConnection();
                
                $stmt = $conn->prepare("INSERT INTO logs (user_id, action, ip_address, user_agent) VALUES (?, 'logout', ?, ?)");
                $stmt->bind_param("iss", $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                // Silent fail
            }
        }
        
        // Clear session
        $_SESSION = array();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
        
        return true;
    }
    
    /**
     * Generate CSRF token
     * @return string CSRF token
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     * @param string $token Token to validate
     * @return bool True if valid
     */
    public function validateCSRF($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Alias for generateCSRFToken for compatibility
     */
    public function getCSRFToken() {
        return $this->generateCSRFToken();
    }
    
    /**
     * Check if current user is teacher (guru or admin)
     * @return bool
     */
    public function isTeacher() {
        return $this->hasRole('guru') || $this->hasRole('admin');
    }
    
    /**
     * Check if current user is student
     * @return bool
     */
    public function isStudent() {
        return $this->hasRole('siswa');
    }
    
    /**
     * Check if current user is admin
     * @return bool
     */
    public function isAdmin() {
        return $this->hasRole('admin');
    }
    
    /**
     * Quick check - require teacher access
     */
    public function requireTeacher() {
        $this->requireRole(['guru', 'admin']);
    }
    
    /**auth
     * Quick check - require admin access
     */
    public function requireAdmin() {
        $this->requireRole('admin');
    }
    
    /**
     * Quick check - require student access
     */
    public function requireStudent() {
        $this->requireRole('siswa');
    }
}
?>