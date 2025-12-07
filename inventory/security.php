<?php
/**
 * Security Helper Functions
 * Provides CSRF protection, session security, and password utilities
 */

/**
 * Initialize secure session settings
 * Call this before session_start()
 */
function init_secure_session() {
    // Prevent JavaScript access to session cookie
    ini_set('session.cookie_httponly', 1);
    
    // Only send cookie over HTTPS in production (disable for local dev)
    // ini_set('session.cookie_secure', 1);
    
    // Prevent session fixation attacks
    ini_set('session.use_strict_mode', 1);
    
    // Use stronger session ID
    ini_set('session.sid_length', 48);
    ini_set('session.sid_bits_per_character', 6);
    
    // Session expires when browser closes (don't persist across restarts)
    ini_set('session.cookie_lifetime', 0);
    
    // Session data expires after 30 minutes of inactivity
    ini_set('session.gc_maxlifetime', 1800);
}

/**
 * Regenerate session ID on login to prevent session fixation
 */
function regenerate_session_on_login() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Generate CSRF token and store in session
 * @return string The generated token
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from POST request
 * @return bool True if valid, false otherwise
 */
function validate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $token = $_POST['csrf_token'] ?? '';
    
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    // Use hash_equals to prevent timing attacks
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output hidden CSRF token field for forms
 */
function csrf_field() {
    $token = generate_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Hash password using bcrypt
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password against hash
 * @param string $password Plain text password
 * @param string $hash Stored password hash
 * @return bool True if password matches
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if password hash needs rehashing (if algorithm or cost changes)
 * @param string $hash Stored password hash
 * @return bool True if needs rehashing
 */
function needs_rehash($hash) {
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Validate and sanitize email
 * @param string $email Email address
 * @return string|false Sanitized email or false if invalid
 */
function sanitize_email($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Rate limiting check (simple file-based for development)
 * @param string $identifier User identifier (IP or email)
 * @param int $max_attempts Maximum attempts allowed
 * @param int $time_window Time window in seconds
 * @return bool True if allowed, false if rate limited
 */
function check_rate_limit($identifier, $max_attempts = 5, $time_window = 900) {
    // Use local directory for AFS compatibility
    $dir = __DIR__ . '/temp';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    $file = $dir . '/rate_' . md5($identifier);
    
    $attempts = [];
    if (file_exists($file)) {
        $attempts = json_decode(file_get_contents($file), true) ?: [];
    }
    
    // Remove old attempts outside time window
    $current_time = time();
    $attempts = array_filter($attempts, function($timestamp) use ($current_time, $time_window) {
        return ($current_time - $timestamp) < $time_window;
    });
    
    // Check if exceeded limit
    if (count($attempts) >= $max_attempts) {
        return false;
    }
    
    // Add new attempt
    $attempts[] = $current_time;
    file_put_contents($file, json_encode($attempts));
    
    return true;
}

/**
 * Clear rate limit for identifier (call on successful login)
 */
function clear_rate_limit($identifier) {
    $dir = __DIR__ . '/temp';
    $file = $dir . '/rate_' . md5($identifier);
    if (file_exists($file)) {
        @unlink($file);
    }
}
?>
