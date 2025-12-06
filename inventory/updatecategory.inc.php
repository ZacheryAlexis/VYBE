<?php
if (empty($_SESSION['is_admin'])) {
?>
    <div class="panel" style="max-width: 600px; margin: 0 auto;">
        <h2 style="color: var(--vybe-orange); margin-top: 0;">Access Denied</h2>
        <p style="color: var(--vybe-muted);">Admin access required to update categories.</p>
        <p><a class="accent-link" href="index.php?content=listcategories">← Back to Categories</a></p>
    </div>
<?php
} else if (!isset($_POST['categoryID']) or (!is_numeric($_POST['categoryID']))) {
?>
    <div class="panel" style="max-width: 600px; margin: 0 auto;">
        <h2 style="color: var(--vybe-orange); margin-top: 0;">Invalid Category</h2>
        <p style="color: var(--vybe-muted);">You did not select a valid category to update.</p>
        <p><a class="accent-link" href="index.php?content=listcategories">← Back to Categories</a></p>
    </div>
<?php
} else {
    $categoryID = $_POST['categoryID'];
    $category = Category::findCategory($categoryID);
    if ($category) {
?>
        <div class="panel" style="max-width: 600px; margin: 0 auto;">
            <h2 style="color: var(--vybe-orange); margin-top: 0;">Update Category #<?php echo $categoryID; ?></h2>
            <p style="color: var(--vybe-muted); margin-bottom: 25px;">Modify the category details below.</p>
            
            <form name="category" action="index.php" method="post">
                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 15px; align-items: center; margin-bottom: 25px;">
                    <label style="color: var(--vybe-text); font-weight: 500;">Category ID:</label>
                    <span style="color: var(--vybe-muted);"><?php echo $categoryID; ?></span>
                    
                    <label for="categoryCode" style="color: var(--vybe-text); font-weight: 500;">Category Code:</label>
                    <input type="text" name="categoryCode" id="categoryCode" value="<?php echo htmlspecialchars($category->categoryCode); ?>" required 
                           style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
                    
                    <label for="categoryName" style="color: var(--vybe-text); font-weight: 500;">Category Name:</label>
                    <input type="text" name="categoryName" id="categoryName" value="<?php echo htmlspecialchars($category->categoryName); ?>" required 
                           style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <input type="submit" name="answer" value="Update Category" 
                           style="background: var(--vybe-orange); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
                    <input type="submit" name="answer" value="Cancel" 
                           style="background: var(--vybe-navy); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
                </div>
                <input type="hidden" name="categoryID" value="<?php echo $categoryID; ?>">
                <input type="hidden" name="content" value="changecategory">
            </form>
        </div>
        <script>
            document.getElementById('categoryCode').focus();
            document.getElementById('categoryCode').select();
        </script>
<?php
    } else {
?>
        <div class="panel" style="max-width: 600px; margin: 0 auto;">
            <h2 style="color: var(--vybe-orange); margin-top: 0;">Category Not Found</h2>
            <p style="color: var(--vybe-muted);">Sorry, category <?php echo $categoryID; ?> not found.</p>
            <p><a class="accent-link" href="index.php?content=listcategories">← Back to Categories</a></p>
        </div>
<?php
    }
}
?>

