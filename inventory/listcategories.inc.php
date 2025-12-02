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
<h2>Select Category</h2>
<form name="categories" method="post">
   <select  ondblclick="listbox_dblclick()"  name="categoryID" size="20">
<?php
// include("category.php");
$categories = Category::getCategories();
foreach($categories as $category) {
   $categoryID = $category->categoryID;
   $name = $categoryID . " - " . $category->categoryCode . ", " . $category->categoryName;
   echo "<option value=\"$categoryID\">$name</option>\n";
       }
       ?>
   </select>
   <br>
   <input type="submit" onClick="button_click(0)" name="displaycategory" value="View Category">
   <?php if (!empty($_SESSION['is_admin'])) { ?>
   <input type="submit" onClick="button_click(1)" name="deletecategory" value="Delete Category">
   <input type="submit" onClick="button_click(2)" name="updatecategory" value="Update Category">
   <?php } ?>
</form>
