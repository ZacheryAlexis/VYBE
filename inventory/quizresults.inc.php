<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href=\"index.php?content=user_login\">log in</a> to view your quiz results.</p>";
    return;
}

require_once('database.php');

// Get user's quiz results from database
$db = getDB();
$stmt = $db->prepare("SELECT quizResults, preferences FROM users WHERE userID = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($quizJson, $prefJson);
$fetched = $stmt->fetch();
$stmt->close();
$db->close();

if (!$fetched || empty($quizJson)) {
?>
    <div class="panel" style="max-width: 700px; margin: 0 auto;">
        <h2 style="color: var(--vybe-orange); margin-top: 0;">No Quiz Results Yet</h2>
        <p style="color: var(--vybe-muted);">You haven't taken the scent quiz yet. Take it now to find your perfect match!</p>
        <p><a class="accent-link" href="index.php?content=quiz" style="font-size: 1.1rem; font-weight: 600;">Take the Quiz →</a></p>
    </div>
<?php
    return;
}

$quizData = json_decode($quizJson, true);
$preferences = !empty($prefJson) ? json_decode($prefJson, true) : [];

// Profile descriptions based on matched letter
$profiles = [
    'A' => [
        'title' => 'The Calm Collector',
        'personality' => 'You embody balance and tranquility. You\'re the friend who stays composed during finals week, finding peace in quiet corners and soft music. Your energy is grounding and reassuring to those around you.',
        'style' => 'Your morning routine is slow and intentional. You prefer peaceful environments like botanical gardens for mental resets. Socially, you\'re the calm observer who brings stability to any group.',
        'scent_why' => 'Lavender brings that peaceful morning energy you crave, while pear adds a subtle sweetness that reflects your balanced nature. This scent works perfectly in those quiet study corners and peaceful moments you treasure.',
        'color' => 'rgba(147, 112, 219, 0.15)'
    ],
    'B' => [
        'title' => 'The Focused Achiever',
        'personality' => 'You\'re driven, disciplined, and always in grind mode. The library is your second home, and you thrive on mental clarity and sharp focus. Your dedication inspires others to level up their game.',
        'style' => 'Mornings require a mental jump-start for you. Fresh air walks help you reset, and you approach everything with a planner\'s mindset. You\'re the disciplined friend who keeps everyone on track.',
        'scent_why' => 'Peppermint provides that crisp mental awakening you need, while eucalyptus enhances your natural clarity and focus. This scent is built for those long library sessions and intense study marathons.',
        'color' => 'rgba(152, 251, 152, 0.15)'
    ],
    'C' => [
        'title' => 'The Energetic Optimist',
        'personality' => 'You\'re the high-energy friend who\'s always ready to go. Bright, upbeat, and naturally enthusiastic, you bring sunshine to every situation. Your positive vibes are contagious and uplifting.',
        'style' => 'You wake up ready to tackle the day. Outdoor spots and sunny locations are your happy place. You\'re the friend who suggests beach days and brings energy to every study group.',
        'scent_why' => 'Citrus captures your bright, uplifting energy perfectly, while green tea adds that fresh, revitalizing quality you embody. This scent matches your outdoor adventures and sunny disposition.',
        'color' => 'rgba(255, 223, 0, 0.15)'
    ],
    'D' => [
        'title' => 'The Warm Confidant',
        'personality' => 'You\'re the mature, reliable friend everyone turns to. Warm, cozy, and confident, you create comfortable spaces wherever you go. Your presence is like a favorite café - inviting and reassuring.',
        'style' => 'You ease into your day with something cozy. Café vibes and warm environments are where you thrive. You\'re the friend who suggests coffee dates and brings depth to conversations.',
        'scent_why' => 'Cedar provides that warm, grounded confidence you exude, while amber adds a cozy richness that lasts. This scent embodies those late-night study sessions and meaningful café conversations.',
        'color' => 'rgba(210, 105, 30, 0.15)'
    ],
    'E' => [
        'title' => 'The Social Butterfly',
        'personality' => 'Fun, sweet, and naturally extroverted - you\'re the life of every campus event. Youthful energy flows through everything you do, and your playful spirit makes every gathering more memorable.',
        'style' => 'Your mornings are chaotic but fun. Social hangouts and events are your element. You\'re the friend who knows everyone, suggests spontaneous plans, and turns ordinary moments into adventures.',
        'scent_why' => 'Peach brings that sweet, approachable charm you naturally have, while hibiscus adds a playful, flirty edge. This scent captures your youthful energy and social magnetism perfectly.',
        'color' => 'rgba(255, 182, 193, 0.15)'
    ]
];

$matchedLetter = $quizData['matched_letter'] ?? 'A';
$profile = $profiles[$matchedLetter];
$matchedScent = $quizData['matched_scent'] ?? 'VYBE Signature';
$matchedItemID = $quizData['matched_itemID'] ?? 2000;
$letterCounts = $quizData['letter_counts'] ?? [];

// Sort letter counts
arsort($letterCounts);
?>

<style>
.quiz-results-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 30px;
}
.results-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 30px;
    background: linear-gradient(135deg, <?php echo $profile['color']; ?>, rgba(199,185,255,0.1));
    border-radius: 16px;
    border: 2px solid var(--vybe-orange);
}
.results-header h1 {
    color: var(--vybe-orange);
    font-size: 2.5rem;
    margin: 0 0 10px 0;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.results-header h2 {
    color: var(--vybe-text);
    font-size: 1.8rem;
    margin: 0 0 15px 0;
    font-weight: 600;
}
.results-header p {
    color: var(--vybe-muted);
    font-size: 1.1rem;
    margin: 0;
}
.profile-section {
    background: var(--vybe-card);
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 25px;
    border-left: 4px solid var(--vybe-orange);
}
.profile-section h3 {
    color: var(--vybe-orange);
    font-size: 1.5rem;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.profile-section p {
    color: var(--vybe-text);
    line-height: 1.8;
    font-size: 1.05rem;
    margin: 0;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin: 30px 0;
}
.stat-card {
    background: var(--vybe-card);
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid rgba(199,185,255,0.2);
}
.stat-card .letter {
    font-size: 2rem;
    font-weight: 700;
    color: var(--vybe-orange);
    margin-bottom: 5px;
}
.stat-card .count {
    font-size: 1.5rem;
    color: var(--vybe-text);
    font-weight: 600;
}
.stat-card .label {
    font-size: 0.9rem;
    color: var(--vybe-muted);
    margin-top: 5px;
}
.cta-section {
    background: linear-gradient(135deg, var(--vybe-orange) 0%, var(--vybe-accent) 100%);
    padding: 40px;
    border-radius: 16px;
    text-align: center;
    margin-top: 40px;
}
.cta-section h3 {
    color: white;
    font-size: 1.8rem;
    margin: 0 0 20px 0;
}
.cta-button {
    display: inline-block;
    background: white;
    color: var(--vybe-orange);
    padding: 15px 40px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.2rem;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}
</style>

<div class="quiz-results-container">
    <div class="results-header">
        <h1><?php echo htmlspecialchars($profile['title']); ?></h1>
        <h2>Your Perfect Match: <?php echo htmlspecialchars($matchedScent); ?></h2>
        <p><?php echo htmlspecialchars($preferences['notes'] ?? ''); ?></p>
    </div>

    <div class="profile-section">
        <h3>✨ Your Personality Profile</h3>
        <p><?php echo $profile['personality']; ?></p>
    </div>

    <div class="profile-section">
        <h3>🎯 Your Vibe & Style</h3>
        <p><?php echo $profile['style']; ?></p>
    </div>

    <div class="profile-section">
        <h3>🌿 Why This Scent Matches You</h3>
        <p><?php echo $profile['scent_why']; ?></p>
    </div>

    <h3 style="color: var(--vybe-text); text-align: center; margin: 40px 0 20px 0;">Your Quiz Breakdown</h3>
    <div class="stats-grid">
        <?php foreach ($letterCounts as $letter => $count): ?>
        <div class="stat-card">
            <div class="letter"><?php echo $letter; ?></div>
            <div class="count"><?php echo $count; ?></div>
            <div class="label">answers</div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="cta-section">
        <h3>Ready to Experience Your Match?</h3>
        <a href="index.php?content=displayitem&itemID=<?php echo $matchedItemID; ?>" class="cta-button">
            Shop <?php echo htmlspecialchars($matchedScent); ?> →
        </a>
        <p style="color: rgba(255,255,255,0.9); margin-top: 20px; font-size: 0.95rem;">
            <a href="index.php?content=quiz" style="color: white; text-decoration: underline;">Retake the quiz</a> or 
            <a href="index.php?content=listitems" style="color: white; text-decoration: underline;">browse all scents</a>
        </p>
    </div>
</div>
