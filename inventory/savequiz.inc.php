<?php
require_once('database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to save your quiz.</p>";
    exit();
}

$userID = intval($_SESSION['user_id']);
$q1 = $_POST['q1'] ?? '';
$q2 = $_POST['q2'] ?? '';
$q3 = $_POST['q3'] ?? '';
$q4 = $_POST['q4'] ?? '';
$q5 = $_POST['q5'] ?? '';

$quiz = array(
    'q1' => $q1,
    'q2' => $q2,
    'q3' => $q3,
    'q4' => $q4,
    'q5' => $q5,
    'completed_at' => date('c')
);

// simple heuristic mapping to categoryIDs (10: Signature, 20: Travel, 30: Limited, 40: Budget)
$preferred = array();
if ($q1 === 'fresh' || $q4 === 'citrus') $preferred[] = 10;
if ($q2 === 'travel' || $q5 === 'gift') $preferred[] = 20;
if ($q2 === 'night' || $q3 === 'strong') $preferred[] = 30;
if ($q3 === 'light' || $q5 === 'study') $preferred[] = 40;
// ensure unique
$preferred = array_values(array_unique($preferred));

$db = getDB();
$stmt = $db->prepare("UPDATE users SET quizResults = ?, preferences = ?, preferredCategories = ? WHERE userID = ?");
$quizJson = json_encode($quiz);
$prefJson = json_encode(array('inferred'=>array('vibe'=>$q1,'strength'=>$q3)));
$catJson = json_encode($preferred);
$stmt->bind_param('sssi', $quizJson, $prefJson, $catJson, $userID);
$ok = $stmt->execute();
$stmt->close();
$db->close();

if ($ok) {
    $_SESSION['quiz_completed'] = true;
    // Optionally, set a session suggestion list — for now store suggested categoryIDs
    $_SESSION['suggested_categories'] = $preferred;
    header('Location: index.php');
    exit();
} else {
    echo "<p>Unable to save quiz. Please try again later.</p>";
}
?>