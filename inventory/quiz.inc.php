<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href=\"index.php?content=user_login\">log in</a> or <a href=\"index.php?content=newuser\">create an account</a> to take the quiz.</p>";
    return;
}
?>
<style>
.quiz-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
    margin-bottom: 60px;
}
.quiz-container h2 {
    color: var(--vybe-orange);
    font-size: 1.8rem;
    margin-bottom: 10px;
}
.quiz-container p {
    color: var(--vybe-muted);
    margin-bottom: 25px;
}
.quiz-question {
    margin-bottom: 25px;
    padding: 15px;
    background: var(--vybe-card);
    border-radius: 8px;
    border-left: 4px solid var(--vybe-orange);
}
.quiz-question label {
    display: block;
    color: var(--vybe-text);
    margin-bottom: 10px;
    font-size: 1rem;
}
.quiz-question select {
    width: 100%;
    padding: 10px;
    background: var(--vybe-bg);
    color: var(--vybe-text);
    border: 1px solid var(--vybe-navy);
    border-radius: 6px;
    font-size: 0.95rem;
}
.quiz-submit {
    background: linear-gradient(135deg, var(--vybe-orange) 0%, var(--vybe-accent) 100%);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
    cursor: pointer;
    margin-top: 20px;
    box-shadow: 0 4px 15px rgba(199,185,255,0.25);
    transition: all 0.3s ease;
}
.quiz-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(199,185,255,0.35);
}
</style>
<div class="quiz-container">
<h2>Vybe Scent Quiz</h2>
<p>Answer these questions to find your perfect scent match from our core collection!</p>
<form action="index.php" method="post">
  <div class="quiz-question">
    <label><strong>Q1.</strong> What kind of vibe describes you best?</label>
    <select name="q1">
      <option value="A">Calm & collected</option>
      <option value="B">Focused & driven</option>
      <option value="C">Fresh & energetic</option>
      <option value="D">Warm & comforting</option>
      <option value="E">Fun & social</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q2.</strong> When you wake up for class, what's your morning style?</label>
    <select name="q2">
      <option value="A">Slow & peaceful</option>
      <option value="B">Need a mental jump-start</option>
      <option value="C">Ready to go, bright & upbeat</option>
      <option value="D">Ease into the day with something cozy</option>
      <option value="E">Chaotic but fun</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q3.</strong> Which study environment is your go-to?</label>
    <select name="q3">
      <option value="A">Quiet corner with soft music</option>
      <option value="B">Library grind mode</option>
      <option value="C">Outdoors / sunny spot</option>
      <option value="D">Café vibes</option>
      <option value="E">Study group</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q4.</strong> Pick a color that feels like "you."</label>
    <select name="q4">
      <option value="A">Lavender</option>
      <option value="B">Mint</option>
      <option value="C">Yellow</option>
      <option value="D">Amber</option>
      <option value="E">Peach</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q5.</strong> How strong should your scent be in class?</label>
    <select name="q5">
      <option value="A">Soft / subtle</option>
      <option value="B">Clean-fresh but noticeable</option>
      <option value="C">Light but uplifting</option>
      <option value="D">Warm + long lasting</option>
      <option value="E">Sweet + playful</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q6.</strong> Pick a mood you want your scent to give off.</label>
    <select name="q6">
      <option value="A">Balanced</option>
      <option value="B">Focused</option>
      <option value="C">Clear + refreshing</option>
      <option value="D">Warm + confident</option>
      <option value="E">Flirty + fun</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q7.</strong> What season matches your vibe the most?</label>
    <select name="q7">
      <option value="A">Spring</option>
      <option value="B">Winter</option>
      <option value="C">Summer</option>
      <option value="D">Fall</option>
      <option value="E">Late-summer / early fall</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q8.</strong> Where would you go for a quick mental reset?</label>
    <select name="q8">
      <option value="A">Botanical garden</option>
      <option value="B">Fresh air walk</option>
      <option value="C">Beach</option>
      <option value="D">Coffee shop</option>
      <option value="E">Sunset overlook</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q9.</strong> What's your personality type socially?</label>
    <select name="q9">
      <option value="A">Calm observer</option>
      <option value="B">Disciplined planner</option>
      <option value="C">The high-energy friend</option>
      <option value="D">The chill, mature one</option>
      <option value="E">The extroverted one</option>
    </select>
  </div>
  
  <div class="quiz-question">
    <label><strong>Q10.</strong> Pick a spontaneous weekend plan.</label>
    <select name="q10">
      <option value="A">Reset & recharge</option>
      <option value="B">Finish overdue assignments</option>
      <option value="C">Beach day or hike</option>
      <option value="D">Movie night or café date</option>
      <option value="E">Social hangout or event</option>
    </select>
  </div>
  
  <?php require_once('security.php'); csrf_field(); ?>
  <input type="hidden" name="content" value="savequiz">
  <input type="submit" class="quiz-submit" value="Find My Scent Match">
</form>
</div>
