<?php
// Clear both admin and user session values on logout
if (session_status() === PHP_SESSION_NONE) session_start();

// Save cart to database before logout if user is logged in
if (!empty($_SESSION['user_id']) && !empty($_SESSION['cart'])) {
  require_once('cart_db.php');
  saveCartToDB($_SESSION['user_id'], $_SESSION['cart']);
}

if (isset($_SESSION['login'])) {
  unset($_SESSION['login']);
}
if (isset($_SESSION['user_id'])) {
  unset($_SESSION['user_id']);
}
if (isset($_SESSION['user_name'])) {
  unset($_SESSION['user_name']);
}
if (isset($_SESSION['is_admin'])) {
  unset($_SESSION['is_admin']);
}

// Clear cart from session on logout
if (isset($_SESSION['cart'])) {
  unset($_SESSION['cart']);
}

// Clear quiz results from session
if (isset($_SESSION['quiz_completed'])) {
  unset($_SESSION['quiz_completed']);
}
if (isset($_SESSION['matched_scent'])) {
  unset($_SESSION['matched_scent']);
}
if (isset($_SESSION['suggested_itemID'])) {
  unset($_SESSION['suggested_itemID']);
}

// Clear remember me cookies
if (isset($_COOKIE['vybe_remember'])) {
  setcookie('vybe_remember', '', time() - 3600, '/', '', false, true);
  setcookie('vybe_user_id', '', time() - 3600, '/', '', false, true);
  setcookie('vybe_is_admin', '', time() - 3600, '/', '', false, true);
}

// Optionally destroy the session entirely
// session_unset(); session_destroy();

if (headers_sent()) {
  echo "Click <a href=\"index.php\"><strong>here</strong></a> to return home.";
} else {
  header("Location: index.php");
}
?>
