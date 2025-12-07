<?php
require_once('database.php');
require_once('security.php');

// Initialize secure session
init_secure_session();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validate CSRF token
if (!validate_csrf_token()) {
    die('<h3>Security error. Please try again.</h3><a href="index.php?content=user_login">Back</a>');
}

$email = filter_var($_POST['emailAddress'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';

if (!$email || !$password) {
    echo "<h3>Please enter valid credentials.</h3>";
    echo "<a href=\"index.php?content=user_login\">Back</a>";
    exit();
}

// Rate limiting check
if (!check_rate_limit($email, 5, 900)) {
    echo "<h3>Too many login attempts. Please try again in 15 minutes.</h3>";
    echo "<a href=\"index.php\">Home</a>";
    exit();
}

$db = getDB();
if (!$db || (isset($db->connect_errno) && $db->connect_errno)) {
    // log server-side, show friendly message to user
    error_log('DB connect error: ' . ($db->connect_error ?? 'unknown'));
    echo "<h3>Server error. Please try again later.</h3>";
    exit();
}

// Check if this is an admin login
$stmt = $db->prepare("SELECT adminID, firstName, lastName, password FROM admins WHERE emailAddress = ?");
if (!$stmt) {
    error_log('Prepare failed: ' . $db->error);
    echo "<h3>Login failed due to a server error.</h3>";
    exit();
}

$stmt->bind_param('s', $email);
if (!$stmt->execute()) {
    error_log('Execute failed: ' . $stmt->error);
    echo "<h3>Login failed due to a server error.</h3>";
    $stmt->close();
    exit();
}

$stmt->bind_result($adminID, $first, $last, $storedHash);
$isAdmin = $stmt->fetch();
$stmt->close();

if ($isAdmin && verify_password($password, $storedHash)) {
    // Admin login successful
    $_SESSION['user_id'] = $adminID;
    $_SESSION['user_name'] = trim($first . ' ' . $last) ?: $email;
    $_SESSION['is_admin'] = true;
    
    // Set persistent cookie if remember me is checked
    if ($rememberMe) {
        // Create a secure token for auto-login
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Store token in cookie (HttpOnly, Secure in production)
        setcookie('vybe_remember', $token, $expiry, '/', '', false, true);
        setcookie('vybe_user_id', $adminID, $expiry, '/', '', false, true);
        setcookie('vybe_is_admin', '1', $expiry, '/', '', false, true);
    }
    
    // Regenerate session ID
    regenerate_session_on_login();
    
    // Clear rate limit
    clear_rate_limit($email);
    
    header('Location: index.php');
    exit();
}

// Not an admin, check regular users table
$stmt = $db->prepare("SELECT userID, firstName, lastName, quizResults, password FROM users WHERE emailAddress = ?");
if (!$stmt) {
    error_log('Prepare failed: ' . $db->error);
    echo "<h3>Login failed due to a server error.</h3>";
    exit();
}

$stmt->bind_param('s', $email);
if (!$stmt->execute()) {
    error_log('Execute failed: ' . $stmt->error);
    echo "<h3>Login failed due to a server error.</h3>";
    $stmt->close();
    exit();
}

$stmt->bind_result($userID, $first, $last, $quizResults, $storedHash);
$fetched = $stmt->fetch();
$stmt->close();
$db->close();

if ($fetched && verify_password($password, $storedHash)) {
    // Regular user login successful
    $_SESSION['user_id'] = $userID;
    $_SESSION['user_name'] = trim($first . ' ' . $last) ?: $email;
    $_SESSION['is_admin'] = false;
    
    // Set persistent cookie if remember me is checked
    if ($rememberMe) {
        // Create a secure token for auto-login
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Store token in cookie (HttpOnly, Secure in production)
        setcookie('vybe_remember', $token, $expiry, '/', '', false, true);
        setcookie('vybe_user_id', $userID, $expiry, '/', '', false, true);
        setcookie('vybe_is_admin', '0', $expiry, '/', '', false, true);
    }
    
    // Regenerate session ID
    regenerate_session_on_login();
    
    // Clear rate limit
    clear_rate_limit($email);
    
    // Load saved cart from database
    require_once('cart_db.php');
    syncCartOnLogin($userID);
    
    if (empty($quizResults)) {
        header('Location: index.php?content=quiz');
        exit();
    } else {
        header('Location: index.php');
        exit();
    }
} else {
    echo "<h3>Login failed. Check email/password.</h3>";
    echo "<a href=\"index.php?content=user_login\">Try again</a>";
}
?>
