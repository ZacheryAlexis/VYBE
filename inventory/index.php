<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
ob_start();

// Initialize secure session settings
require_once('security.php');
init_secure_session();

// Get current process ID before starting session
$currentPID = getmypid();

session_start();

// Check if user has "remember me" cookie and auto-login
if (empty($_SESSION['user_id']) && isset($_COOKIE['vybe_remember']) && isset($_COOKIE['vybe_user_id'])) {
    $cookieUserID = $_COOKIE['vybe_user_id'];
    $isAdmin = isset($_COOKIE['vybe_is_admin']) && $_COOKIE['vybe_is_admin'] === '1';
    
    require_once('database.php');
    $db = getDB();
    
    if ($isAdmin) {
        $stmt = $db->prepare("SELECT adminID, firstName, lastName FROM admins WHERE adminID = ?");
        $stmt->bind_param('i', $cookieUserID);
        $stmt->execute();
        $stmt->bind_result($adminID, $first, $last);
        if ($stmt->fetch()) {
            $_SESSION['user_id'] = $adminID;
            $_SESSION['user_name'] = trim($first . ' ' . $last);
            $_SESSION['is_admin'] = true;
        }
        $stmt->close();
    } else {
        $stmt = $db->prepare("SELECT userID, firstName, lastName FROM users WHERE userID = ?");
        $stmt->bind_param('i', $cookieUserID);
        $stmt->execute();
        $stmt->bind_result($userID, $first, $last);
        if ($stmt->fetch()) {
            $_SESSION['user_id'] = $userID;
            $_SESSION['user_name'] = trim($first . ' ' . $last);
            $_SESSION['is_admin'] = false;
            
            // Load cart for auto-logged-in user
            require_once('cart_db.php');
            syncCartOnLogin($userID);
        }
        $stmt->close();
    }
    $db->close();
}

// Check if session has a different process ID (from previous server instance)
$sessionPID = $_SESSION['process_id'] ?? null;

if ($sessionPID !== null && $sessionPID !== $currentPID) {
    // Different process ID means server was restarted
    // Check if user has remember me cookie
    if (!isset($_COOKIE['vybe_remember'])) {
        // No remember me cookie - clear session on restart
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['process_id'] = $currentPID;
    } else {
        // Has remember me cookie - just update process ID
        $_SESSION['process_id'] = $currentPID;
    }
} elseif ($sessionPID === null) {
    // New session, set the process ID
    $_SESSION['process_id'] = $currentPID;
}

// Check for session timeout (30 minutes)
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // Session expired, clear it
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['server_instance'] = $currentInstance;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity timestamp

include("category.php");
include("item.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vybe — Campus Scents</title>
    <link rel="stylesheet" type="text/css" href="ih_styles.css">
    <link rel="icon" type="image/png" href="images/VYBELogoWhite.png">
</head>
<body>
   <header>
       <?php include("header.inc.php"); ?>
   </header>
   <section style="min-height: 500px; height: auto;">
       <nav style="float: left; height: 100%;">
           <?php include("nav.inc.php"); ?>
       </nav>
       <main>
           <?php
           try {
               if (isset($_REQUEST['content'])) {
                   include($_REQUEST['content'] . ".inc.php");
               } else {
                   include("home.inc.php");
               }
           } catch (Throwable $e) {
               // Log the exception and show a friendly message instead of a blank page
               error_log('Unhandled exception rendering page: ' . $e->getMessage());
               echo '<div style="max-width:800px;margin:40px auto;padding:20px;background:#141417;border-radius:10px;border:1px solid rgba(199,185,255,0.06);">';
               echo '<h2 style="color: var(--vybe-orange);">Server Error</h2>';
               echo '<p style="color: var(--vybe-muted);">We encountered a server error while loading this page. Please try again later.</p>';
               echo '</div>';
           }
           ?>
       </main>
       <aside>
               <?php include("aside.inc.php"); ?>
       </aside>

   </section>
   <footer>
       <?php include("footer.inc.php"); ?>
   </footer>
</body>
</html>
<?php
ob_end_flush();
?>

