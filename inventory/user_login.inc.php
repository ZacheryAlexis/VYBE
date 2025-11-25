<h2>User Login</h2>
<form action="index.php" method="post">
  <label>Email:</label><br>
  <input type="email" name="emailAddress" required><br><br>
  <label>Password:</label><br>
  <input type="password" name="password" required><br><br>
  <input type="hidden" name="content" value="validateuser">
  <input type="submit" value="Login">
</form>
<p style="font-size:0.9rem;color:var(--vybe-navy);">If you're new, <a href="index.php?content=newuser">create an account</a>.</p>
