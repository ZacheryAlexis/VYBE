<style>
  .header-div {
       background: linear-gradient(90deg, #0033A0, #002060);
       padding: 10px 12px;
       display: flex;
       align-items: center;
       gap: 10px;
       border-bottom: 4px solid #FF6A00;
  }
  .header-div img {
      width: 50px;
      height: 50px;
      margin-right: 8px;
      border-radius: 8px;
      background-color: rgba(255,106,0,0.12);
      padding: 4px;
      border: 2px solid rgba(255,255,255,0.06);
  }
  .header-div h1 {
      color: #ffffff;
      margin: 0;
      font-family: 'Segoe UI', Roboto, Arial, sans-serif;
      letter-spacing: 0.5px;
  }
  .header-div h2 {
      color: #FFD8B5; /* light orange tint */
      margin: 0;
      font-size: 0.9rem;
      font-weight: 400;
  }
</style>
<div class="header-div">
  <img src="images/logo.png" alt="Vybe Logo">
  <div>
      <h1>Vybe</h1>
      <h2>Campus Scents — Fragrances for College Life</h2>
  </div>
</div>

<style>
  .top-actions {
    display:flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
  }
  .top-actions .left {
    display:flex;
    gap:8px;
    align-items:center;
  }
  .top-actions .right a {
    color: #fff;
    background: var(--vybe-navy);
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
  }
  .top-actions .right a.logout {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.12);
    color: var(--vybe-orange);
    padding:6px 10px;
  }
</style>

<?php
  // show account/login and signout links
  if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="top-actions">
  <div class="left">
    <a href="index.php" style="color: #fff; text-decoration:none; font-weight:600;">Home</a>
    <!-- Account login -->
    <?php if (empty(
      
      
      
      
      
      
      $_SESSION['user_id'])) { ?>
      <a href="index.php?content=user_login" class="btn-ghost" style="background:transparent;color: #fff;border:1px solid rgba(255,255,255,0.08)">Account</a>
    <?php } else { ?>
      <span style="color:#fff;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
    <?php } ?>
  </div>
  <div class="right">
    <?php if (!empty($_SESSION['user_id']) || !empty($_SESSION['login'])) { ?>
      <a href="index.php?content=logout" class="logout">Sign out</a>
    <?php } else { ?>
      <a href="index.php?content=user_login">Sign in</a>
    <?php } ?>
  </div>
</div>
