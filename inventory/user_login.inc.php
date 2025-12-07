<?php require_once('security.php'); ?>
<h2>User Login</h2>
<form action="index.php" method="post">
  <label>Email:</label><br>
  <input type="email" name="emailAddress" required><br><br>
  <label>Password:</label><br>
  <input type="password" name="password" required><br><br>
  <label style="display: flex; align-items: center; gap: 8px; font-size: 0.95rem; margin-bottom: 12px;">
    <input type="checkbox" name="remember_me" value="1" style="width: auto;">
    <span style="color: var(--vybe-muted);">Keep me signed in</span>
  </label>
  <?php csrf_field(); ?>
  <input type="hidden" name="content" value="validateuser">
  <input type="submit" value="Login">
</form>
<p style="font-size:0.9rem;color:var(--vybe-muted);">If you're new, <a href="index.php?content=newuser" class="accent-link">create an account</a>.</p>
