<?php
require_once('database.php');
require_once('security.php');

init_secure_session();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to save your quiz.</p>";
    exit();
}

// Validate CSRF token
if (!validate_csrf_token()) {
    die('<h3>Security error. Please try again.</h3><a href="index.php?content=quiz">Back</a>');
}

$userID = intval($_SESSION['user_id']);

// Collect all 10 answers
$answers = array();
for ($i = 1; $i <= 10; $i++) {
    $answers["q$i"] = $_POST["q$i"] ?? '';
}

// Count frequency of each letter (A, B, C, D, E)
$counts = array('A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0);
foreach ($answers as $answer) {
    if (isset($counts[$answer])) {
        $counts[$answer]++;
    }
}

// Find the letter with the highest count
arsort($counts);
$topLetter = key($counts);

// Map letter to scent name and itemID
// A = VYBE Signature (itemID 2000)
// B = Study Session (itemID 2001)
// C = Freshman Sunrise (itemID 2002)
// D = All-Nighter (itemID 2003)
// E = Dorm Crush (itemID 2004)
$scentMap = array(
    'A' => array('name' => 'VYBE Signature', 'notes' => 'Lavender + Pear', 'desc' => 'Calm, balanced', 'itemID' => 2000),
    'B' => array('name' => 'Study Session', 'notes' => 'Peppermint + Eucalyptus', 'desc' => 'Focus, clarity', 'itemID' => 2001),
    'C' => array('name' => 'Freshman Sunrise', 'notes' => 'Citrus + Green Tea', 'desc' => 'Energy, brightness', 'itemID' => 2002),
    'D' => array('name' => 'All-Nighter', 'notes' => 'Cedar + Amber', 'desc' => 'Warm, cozy, confident', 'itemID' => 2003),
    'E' => array('name' => 'Dorm Crush', 'notes' => 'Peach + Hibiscus', 'desc' => 'Fun, sweet, youthful', 'itemID' => 2004)
);

$matchedScent = $scentMap[$topLetter];

// Build quiz results JSON
$quiz = array(
    'answers' => $answers,
    'letter_counts' => $counts,
    'matched_letter' => $topLetter,
    'matched_scent' => $matchedScent['name'],
    'matched_itemID' => $matchedScent['itemID'],
    'completed_at' => date('c')
);

// Store suggested itemID in preferredCategories as an array
$preferred = array($matchedScent['itemID']);

$db = getDB();
$stmt = $db->prepare("UPDATE users SET quizResults = ?, preferences = ?, preferredCategories = ? WHERE userID = ?");
$quizJson = json_encode($quiz);
$prefJson = json_encode(array('scent' => $matchedScent['name'], 'notes' => $matchedScent['notes'], 'desc' => $matchedScent['desc']));
$catJson = json_encode($preferred);
$stmt->bind_param('sssi', $quizJson, $prefJson, $catJson, $userID);
$ok = $stmt->execute();
$stmt->close();
$db->close();

if ($ok) {
    $_SESSION['quiz_completed'] = true;
    $_SESSION['matched_scent'] = $matchedScent['name'];
    $_SESSION['suggested_itemID'] = $matchedScent['itemID'];
    // Redirect to the matched item's display page
    header('Location: index.php?content=displayitem&itemID=' . $matchedScent['itemID']);
    exit();
} else {
    echo "<p>Unable to save quiz. Please try again later.</p>";
}
?>