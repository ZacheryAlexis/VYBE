<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href=\"index.php?content=user_login\">log in</a> to view your profile.</p>";
    return;
}

require_once('database.php');

$db = getDB();
$stmt = $db->prepare("SELECT emailAddress, firstName, lastName, quizResults, preferences FROM users WHERE userID = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($email, $firstName, $lastName, $quizJson, $prefJson);
$fetched = $stmt->fetch();
$stmt->close();
$db->close();

if (!$fetched) {
    echo "<p>Unable to load profile.</p>";
    return;
}

$quizData = !empty($quizJson) ? json_decode($quizJson, true) : null;
$preferences = !empty($prefJson) ? json_decode($prefJson, true) : [];
?>

<style>
.profile-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 30px;
}
.profile-header {
    background: var(--vybe-card);
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    border-left: 4px solid var(--vybe-orange);
}
.profile-header h1 {
    color: var(--vybe-orange);
    margin: 0 0 10px 0;
    font-size: 2rem;
}
.profile-header p {
    color: var(--vybe-muted);
    margin: 0;
}
.profile-section {
    background: var(--vybe-card);
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 1px solid rgba(199,185,255,0.2);
}
.profile-section h2 {
    color: var(--vybe-text);
    margin: 0 0 20px 0;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 10px;
}
.profile-form {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 15px;
    align-items: center;
}
.profile-form label {
    color: var(--vybe-text);
    font-weight: 500;
}
.profile-form input {
    padding: 10px;
    background: var(--vybe-bg);
    color: var(--vybe-text);
    border: 1px solid rgba(199,185,255,0.3);
    border-radius: 6px;
    font-size: 1rem;
}
.profile-form input:focus {
    outline: none;
    border-color: var(--vybe-orange);
    box-shadow: 0 0 0 3px rgba(199,185,255,0.15);
}
.btn-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
.btn-primary {
    background: var(--vybe-orange);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-primary:hover {
    background: #b8a3ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(199,185,255,0.35);
}
.btn-secondary {
    background: rgba(199,185,255,0.2);
    color: var(--vybe-text);
    padding: 12px 30px;
    border: 1px solid rgba(199,185,255,0.4);
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}
.btn-secondary:hover {
    background: rgba(199,185,255,0.3);
}
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: var(--vybe-muted);
    font-size: 0.95rem;
}
.info-value {
    color: var(--vybe-text);
    font-weight: 500;
}
.quiz-badge {
    display: inline-block;
    background: rgba(199,185,255,0.15);
    color: var(--vybe-orange);
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 600;
}
</style>

<div class="profile-container">
    <div class="profile-header">
        <h1>👤 My Profile</h1>
        <p>Manage your account information and preferences</p>
    </div>

    <div class="profile-section">
        <h2>📧 Account Information</h2>
        <form method="post" action="index.php?content=updateprofile">
            <?php require_once('security.php'); csrf_field(); ?>
            <div class="profile-form">
                <label>Email:</label>
                <input type="email" name="emailAddress" value="<?php echo htmlspecialchars($email ?? ''); ?>" readonly style="background: rgba(255,255,255,0.05); cursor: not-allowed;">
                
                <label>First Name:</label>
                <input type="text" name="firstName" value="<?php echo htmlspecialchars($firstName ?? ''); ?>" required>
                
                <label>Last Name:</label>
                <input type="text" name="lastName" value="<?php echo htmlspecialchars($lastName ?? ''); ?>" required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="index.php?content=changepassword" class="btn-secondary">Change Password</a>
            </div>
        </form>
    </div>

    <?php if ($quizData): ?>
    <div class="profile-section">
        <h2>🎯 Quiz Profile</h2>
        <div class="info-row">
            <span class="info-label">Your Match:</span>
            <span class="quiz-badge"><?php echo htmlspecialchars($quizData['matched_scent'] ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Personality Type:</span>
            <span class="info-value"><?php echo htmlspecialchars($quizData['matched_letter'] ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Completed:</span>
            <span class="info-value"><?php echo isset($quizData['completed_at']) ? date('F j, Y', strtotime($quizData['completed_at'])) : 'N/A'; ?></span>
        </div>
        <div class="btn-group">
            <a href="index.php?content=quizresults" class="btn-secondary">View Full Results</a>
            <a href="index.php?content=quiz" class="btn-secondary">Retake Quiz</a>
        </div>
    </div>
    <?php else: ?>
    <div class="profile-section">
        <h2>🎯 Quiz Profile</h2>
        <p style="color: var(--vybe-muted); margin: 0 0 15px 0;">You haven't taken the scent quiz yet. Discover your perfect match!</p>
        <a href="index.php?content=quiz" class="btn-primary" style="text-decoration: none; display: inline-block;">Take the Quiz</a>
    </div>
    <?php endif; ?>

    <div class="profile-section">
        <h2>🔐 Security</h2>
        <div class="info-row">
            <span class="info-label">Password:</span>
            <span class="info-value">••••••••</span>
        </div>
        <div class="btn-group">
            <a href="index.php?content=changepassword" class="btn-primary" style="text-decoration: none;">Change Password</a>
        </div>
    </div>
</div>
