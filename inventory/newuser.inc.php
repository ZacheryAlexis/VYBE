<h2>Create a Vybe Account</h2>
<form action="index.php" method="post">
  <label>Email:</label><br>
  <input type="email" name="emailAddress" required><br><br>
  <label>Password:</label><br>
  <input type="password" name="password" required><br><br>
  <label>First name:</label><br>
  <input type="text" name="firstName"><br><br>
  <label>Last name:</label><br>
  <input type="text" name="lastName"><br><br>
  <input type="hidden" name="content" value="createuser">
  <input type="submit" value="Create Account">
</form>
<p style="font-size:0.9rem;color:var(--vybe-navy);">Note: After creating an account you'll be guided through a short quiz to find suggested scents.</p>
