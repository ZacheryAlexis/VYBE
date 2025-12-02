<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href=\"index.php?content=user_login\">log in</a> or <a href=\"index.php?content=newuser\">create an account</a> to take the quiz.</p>";
    return;
}
?>
<h2>Vybe Scent Quiz (Quick)</h2>
<p>This is a placeholder quiz — questions can be adjusted later. Answers will be stored on your profile and used to suggest scents.</p>
<form action="index.php" method="post">
  <label>1) What vibe do you prefer?</label><br>
  <select name="q1">
    <option value="fresh">Fresh / Citrus</option>
    <option value="woody">Woody</option>
    <option value="spicy">Spicy</option>
    <option value="sweet">Sweet / Gourmand</option>
  </select>
  <br><br>
  <label>2) Where will you wear it most?</label><br>
  <select name="q2">
    <option value="classes">Daytime / Classes</option>
    <option value="night">Evenings / Nights out</option>
    <option value="everyday">Everyday / Casual</option>
    <option value="travel">Travel / On-the-go</option>
  </select>
  <br><br>
  <label>3) Strength preference:</label><br>
  <select name="q3">
    <option value="light">Light (subtle)</option>
    <option value="moderate">Moderate</option>
    <option value="strong">Strong (statement)</option>
  </select>
  <br><br>
  <label>4) Favorite note family:</label><br>
  <select name="q4">
    <option value="citrus">Citrus</option>
    <option value="marine">Marine / Aquatic</option>
    <option value="amber">Amber / Musk</option>
    <option value="herbal">Herbal / Green</option>
  </select>
  <br><br>
  <label>5) Any occasion preference?</label><br>
  <select name="q5">
    <option value="gift">Gift</option>
    <option value="personal">Personal use</option>
    <option value="sport">Sport / Gym</option>
    <option value="study">Study focus</option>
  </select>
  <br><br>
  <input type="hidden" name="content" value="savequiz">
  <input type="submit" value="Save Quiz & Get Suggestions">
</form>
