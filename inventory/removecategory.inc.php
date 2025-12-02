<?php
error_log("\$_POST " . print_r($_POST, true));
require_once("category.php");
if (!empty($_SESSION['is_admin'])) {
   if (!isset($_POST['categoryID']) or (!is_numeric($_POST['categoryID']))) {
      ?>
             <h2>You did not select a valid categoryID to delete.</h2>
             <a href="index.php?content=listcategories">List Categories</a>
      <?php
   } else {
      
   $categoryID = $_POST['categoryID'];
   $category = Category::findCategory($categoryID);
   $result = $category->removeCategory();
   if ($result)
      echo "<h2>Category $categoryID removed</h2>\n";
   else
      echo "<h2>Sorry, problem removing category $categoryID</h2>\n";
   }
} else {
   echo "<H2>Admin access required</h2>\n";
}

?>
   