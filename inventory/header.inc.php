<style>
  .header-div {
       background: #141417;
       padding: 20px 30px;
       display: flex;
       align-items: center;
       gap: 16px;
       border-bottom: 2px solid var(--vybe-orange);
       box-shadow: 
         0 8px 24px rgba(0,0,0,0.6), 
         0 0 20px rgba(199,185,255,0.15),
         0 4px 20px rgba(199,185,255,0.1);
       position: relative;
       width: 100%;
       box-sizing: border-box;
       overflow: visible;
  }
  .header-div img {
      height: 160px;
      width: auto;
      filter: drop-shadow(0 2px 8px rgba(0,0,0,0.4));
      margin: -30px 0;
  }
  .header-div h1 {
      color: #ffffff;
      margin: 0;
      font-family: 'Avenir', 'Avenir Next', sans-serif;
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: 1px;
  }
  .header-div h2 {
      color: var(--vybe-muted);
      margin: 4px 0 0 0;
      font-size: 0.95rem;
      font-weight: 400;
  }
  .search-container {
      position: relative;
      min-width: 320px;
  }
  .search-form {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(199,185,255,0.3);
      border-radius: 8px;
      padding: 8px 12px;
      transition: all 0.2s;
  }
  .search-form:focus-within {
      background: rgba(255,255,255,0.08);
      border-color: var(--vybe-orange);
      box-shadow: 0 0 0 3px rgba(199,185,255,0.15);
  }
  .search-suggestions {
      position: absolute;
      top: calc(100% + 8px);
      left: 0;
      right: 0;
      background: #141417;
      border: 1px solid rgba(199,185,255,0.3);
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.5), 0 0 20px rgba(199,185,255,0.15);
      max-height: 400px;
      overflow-y: auto;
      z-index: 1000;
      display: none;
  }
  .suggestion-item {
      display: block;
      padding: 12px 16px;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      text-decoration: none;
      transition: all 0.2s;
      cursor: pointer;
  }
  .suggestion-item:last-child {
      border-bottom: none;
  }
  .suggestion-item:hover {
      background: rgba(199,185,255,0.1);
  }
  .suggestion-name {
      color: var(--vybe-text);
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 4px;
  }
  .suggestion-meta {
      color: var(--vybe-muted);
      font-size: 0.85rem;
  }
  .search-form input[type="text"] {
      background: transparent;
      border: none;
      color: var(--vybe-text);
      font-size: 0.95rem;
      outline: none;
      flex: 1;
      font-family: 'Avenir', 'Avenir Next', sans-serif;
  }
  .search-form input[type="text"]::placeholder {
      color: var(--vybe-muted);
  }
  .search-form button {
      background: transparent;
      border: none;
      color: var(--vybe-orange);
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
  }
  .search-form button:hover {
      color: #b8a3ff;
      transform: scale(1.1);
  }
</style>
<div class="header-div">
  <a href="index.php" style="line-height: 0; display: flex; align-items: center;">
    <img src="images/VYBELogoWhite.png" alt="Vybe Logo">
  </a>
  <div style="flex: 1;">
      <h2>Campus Scents — Fragrances for College Life</h2>
  </div>
  <div class="search-container">
    <form method="get" action="index.php" class="search-form" id="searchForm">
      <input type="hidden" name="content" value="search">
      <input type="text" name="q" id="searchInput" placeholder="Search scents..." autocomplete="off">
      <button type="submit">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.35-4.35"></path>
        </svg>
      </button>
    </form>
    <div id="searchSuggestions" class="search-suggestions"></div>
  </div>
  <script>
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      const query = this.value.trim();
      
      if (query.length < 2) {
        searchSuggestions.style.display = 'none';
        return;
      }
      
      searchTimeout = setTimeout(() => {
        fetch('searchsuggest.php?q=' + encodeURIComponent(query))
          .then(response => response.json())
          .then(data => {
            if (data.length > 0) {
              searchSuggestions.innerHTML = data.map(item => 
                `<a href="index.php?content=displayitem&itemID=${item.itemID}" class="suggestion-item">
                  <div class="suggestion-name">${item.itemName}</div>
                  <div class="suggestion-meta">${item.categoryName} • $${item.listPrice}</div>
                </a>`
              ).join('');
              searchSuggestions.style.display = 'block';
            } else {
              searchSuggestions.style.display = 'none';
            }
          })
          .catch(err => console.error('Search error:', err));
      }, 300);
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.search-container')) {
        searchSuggestions.style.display = 'none';
      }
    });

    // Show suggestions when clicking input
    searchInput.addEventListener('focus', function() {
      if (this.value.trim().length >= 2 && searchSuggestions.innerHTML) {
        searchSuggestions.style.display = 'block';
      }
    });
  </script>
</div>

<style>
  .top-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 30px;
    background: var(--vybe-card);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
  .top-actions .left {
    display: flex;
    gap: 16px;
    align-items: center;
  }
  .top-actions .left a {
    color: var(--vybe-text);
    text-decoration: none;
    font-weight: 500;
    padding: 8px 14px;
    border-radius: 6px;
    transition: all 0.2s;
  }
  .top-actions .left a:hover {
    background: rgba(199,185,255,0.08);
    color: var(--vybe-orange);
  }
  .top-actions .left span {
    color: var(--vybe-muted);
    font-size: 0.95rem;
  }
  .top-actions .right a {
    color: white;
    background: var(--vybe-orange);
    padding: 8px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
  }
  .top-actions .right a:hover {
    background: #b8a3ff;
    box-shadow: 0 4px 12px rgba(199,185,255,0.25);
  }
  .top-actions .right a.logout {
    background: transparent;
    border: 1px solid rgba(199,185,255,0.4);
    color: var(--vybe-orange);
  }
  .top-actions .right a.logout:hover {
    background: rgba(199,185,255,0.1);
    box-shadow: none;
  }
</style>

<?php
  // show account/login and signout links
  if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="top-actions">
  <div class="left">
    <?php if (empty($_SESSION['user_id'])) { ?>
      <a href="index.php?content=user_login">Account</a>
    <?php } else { ?>
      <a href="index.php?content=profile">My Profile</a>
      <a href="index.php?content=wishlist" title="Wishlist">♥ Wishlist</a>
      <a href="index.php?content=orderhistory">Orders</a>
    <?php } ?>
  </div>
  <div class="right">
    <?php if (!empty($_SESSION['user_id']) || !empty($_SESSION['login'])) { ?>
      <a href="index.php?content=logout" class="logout">Sign Out</a>
    <?php } else { ?>
      <a href="index.php?content=user_login">Sign In</a>
    <?php } ?>
  </div>
</div>
