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

// Optionally destroy the session entirely
// session_unset(); session_destroy();

if (headers_sent()) {
  echo "Click <a href=\"index.php\"><strong>here</strong></a> to return home.";
} else {
  header("Location: index.php");
}
?>
