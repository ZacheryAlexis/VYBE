<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href=\"index.php?content=user_login\">log in</a> to change your password.</p>";
    return;
}

require_once('security.php');
?>

<style>
.password-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 30px;
}
.password-form {
    background: var(--vybe-card);
    padding: 30px;
    border-radius: 12px;
    border-left: 4px solid var(--vybe-orange);
}
.password-form h2 {
    color: var(--vybe-orange);
    margin: 0 0 20px 0;
    font-size: 1.8rem;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    color: var(--vybe-text);
    font-weight: 500;
    margin-bottom: 8px;
}
.form-group input {
    width: 100%;
    padding: 12px;
    background: var(--vybe-bg);
    color: var(--vybe-text);
    border: 1px solid rgba(199,185,255,0.3);
    border-radius: 6px;
    font-size: 1rem;
    box-sizing: border-box;
}
.form-group input:focus {
    outline: none;
    border-color: var(--vybe-orange);
    box-shadow: 0 0 0 3px rgba(199,185,255,0.15);
}
.password-hint {
    color: var(--vybe-muted);
    font-size: 0.9rem;
    margin-top: 5px;
}
.btn-group {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}
</style>

<div class="password-container">
    <div class="password-form">
        <h2>🔐 Change Password</h2>
        <p style="color: var(--vybe-muted); margin-bottom: 25px;">Enter your current password and choose a new one.</p>
        
        <form method="post" action="index.php?content=updatepassword">
            <?php csrf_field(); ?>
            
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" name="currentPassword" required>
            </div>
            
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword" minlength="8" required>
                <div class="password-hint">Must be at least 8 characters long</div>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" minlength="8" required>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn-primary">Update Password</button>
                <a href="index.php?content=profile" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
