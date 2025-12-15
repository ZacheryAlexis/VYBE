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
    <p>
      <a class="accent-link" href="index.php?content=quizresults" style="font-size:1.1rem; font-weight:600;">View Full Results →</a>
      &nbsp;•&nbsp;
      <a class="accent-link" href="index.php?content=displayitem&itemID=<?php echo $matchedItemID; ?>" style="font-size:1.1rem; font-weight:600;">Shop Your Match →</a>
    </p>
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

<?php
// Compact product showcase: display up to 4 base items (50ml) as small cards
require_once('item.php');
$items = Item::getItems() ?: array();
$showItems = array_slice($items, 0, 4);
?>
<div class="panel" style="margin-top:16px;">
  <h2 style="margin-top:0;color:var(--vybe-text);">Store Picks</h2>
  <style>
    .home-showcase { display:flex; gap:16px; flex-wrap:wrap; }
    .home-card { background: var(--vybe-card); border-radius:10px; padding:10px; width:180px; border:1px solid rgba(199,185,255,0.06); box-shadow:none; }
    .home-card img { width:100%; height:140px; object-fit:contain; border-radius:6px; background:#111; padding:6px; border:6px solid white; }
    .home-card h4 { margin:8px 0 4px 0; font-size:1rem; color:var(--vybe-text); }
    .home-card .price { color:var(--vybe-orange); font-weight:700; }
    .home-card a { text-decoration:none; color:inherit; }
  </style>
  <div class="home-showcase">
    <?php foreach ($showItems as $it):
        $baseName = str_replace(' ', '_', $it->itemName);
        $pngPathFs = __DIR__ . '/images/' . $baseName . '.png';
        $svgPathFs = __DIR__ . '/images/' . $baseName . '.svg';
        if (file_exists($pngPathFs)) {
            $img = 'images/' . $baseName . '.png';
        } elseif (file_exists($svgPathFs)) {
            $img = 'images/' . $baseName . '.svg';
        } else {
            $img = 'images/items.png';
        }
    ?>
    <div class="home-card">
      <a href="index.php?content=displayitem&itemID=<?php echo $it->itemID; ?>">
        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($it->itemName); ?>">
        <h4><?php echo htmlspecialchars($it->itemName); ?></h4>
        <div class="price">$<?php echo number_format($it->listPrice,2); ?></div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</div>
