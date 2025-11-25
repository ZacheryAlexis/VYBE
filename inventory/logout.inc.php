<?php
// Clear both admin and user session values on logout
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['login'])) {
  unset($_SESSION['login']);
}
if (isset($_SESSION['user_id'])) {
  unset($_SESSION['user_id']);
}
if (isset($_SESSION['user_name'])) {
  unset($_SESSION['user_name']);
}
// Optionally destroy the session entirely
// session_unset(); session_destroy();

if (headers_sent()) {
  echo "Click <a href=\"index.php\"><strong>here</strong></a> to return home.";
} else {
  header("Location: index.php");
}
?>
