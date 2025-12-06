<script language="javascript">
   function listbox_dblclick() {
       document.categories.displaycategory.click()
   }
   function button_click(target) {
       var userConfirmed = true;
       if (target == 1) {
           userConfirmed = confirm("Are you sure you want to remove this category?");
       }
       if (userConfirmed) {
           if (target == 0) categories.action = "index.php?content=displaycategory";
           if (target == 1) categories.action = "index.php?content=removecategory";
           if (target == 2) categories.action = "index.php?content=updatecategory";
       } else {
           alert("Action canceled.");
       }
   }
</script>

<div class="panel" style="max-width: 700px; margin: 0 auto;">
    <h2 style="color: var(--vybe-orange); margin-top: 0;">Manage Categories</h2>
    <p style="color: var(--vybe-muted); margin-bottom: 20px;">Select a category to view details or double-click to open.</p>
    
    <form name="categories" method="post">
        <select ondblclick="listbox_dblclick()" name="categoryID" size="15" 
                style="width: 100%; padding: 12px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 8px; font-size: 1rem; margin-bottom: 15px;">
<?php
$categories = Category::getCategories();
foreach($categories as $category) {
   $categoryID = $category->categoryID;
   $name = "#$categoryID — " . $category->categoryCode . " — " . $category->categoryName;
   echo "<option value=\"$categoryID\">$name</option>\n";
}
?>
        </select>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="submit" onClick="button_click(0)" name="displaycategory" value="View Category" 
                   style="background: var(--vybe-orange); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
            <?php if (!empty($_SESSION['is_admin'])) { ?>
            <input type="submit" onClick="button_click(1)" name="deletecategory" value="Delete Category" 
                   style="background: #d32f2f; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
            <input type="submit" onClick="button_click(2)" name="updatecategory" value="Update Category" 
                   style="background: var(--vybe-navy); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
            <?php } ?>
        </div>
    </form>
</div>
