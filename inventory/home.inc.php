<?php
// Public home page for Vybe
// Check if user just completed quiz and show their match
$showQuizResult = false;
if (!empty($_SESSION['matched_scent']) && !empty($_SESSION['quiz_completed'])) {
    $showQuizResult = true;
    $matchedScent = $_SESSION['matched_scent'];
    $matchedItemID = $_SESSION['suggested_itemID'] ?? null;
}
?>

<?php if ($showQuizResult): ?>
<div class="panel" style="margin-bottom:16px; border: 2px solid var(--vybe-orange);">
  <h2 style="margin-top:0; color:var(--vybe-orange);">🎉 Your Vybe Match: <?php echo htmlspecialchars($matchedScent); ?></h2>
  <p style="color:var(--vybe-text);">Based on your answers, this scent matches your personality perfectly!</p>
  <?php if ($matchedItemID): ?>
    <p><a class="accent-link" href="index.php?content=displayitem&itemID=<?php echo $matchedItemID; ?>" style="font-size:1.1rem; font-weight:600;">View Your Match →</a></p>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="panel" style="margin-bottom:16px;">
  <h1 style="margin-top:0;">Find Your Vybe</h1>
  <p style="color:var(--vybe-muted);">Vybe makes fragrances for college life — light, travel-friendly, and bold scents for evenings. Take our short quiz to get personalized suggestions.</p>
  <p><a class="accent-link" href="index.php?content=quiz">Take the Quiz</a> &nbsp;•&nbsp; <a class="accent-link" href="index.php?content=listitems">Browse All Scents</a></p>
</div>

<div class="panel">
  <h2 style="margin-top:0;color:var(--vybe-orange);">Featured Picks</h2>
  <ul style="list-style:none;padding-left:0;margin:8px 0 0 0;color:var(--vybe-muted);">
    <li><a class="accent-link" href="index.php?content=displayitem&itemID=2000">Vybe Essence — Signature</a> — Bright & approachable</li>
    <li><a class="accent-link" href="index.php?content=displayitem&itemID=2001">Vybe Pocket Mist — Travel</a> — Compact refresh</li>
    <li><a class="accent-link" href="index.php?content=displayitem&itemID=2002">Vybe Night Swipe — Limited</a> — Bold for evenings</li>
  </ul>
</div>
