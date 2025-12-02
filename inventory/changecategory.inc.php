<?php
require_once("category.php");
if (!empty($_SESSION['is_admin'])) {
$categoryID = $_POST['categoryID'];
$answer = $_POST['answer'];
if ($answer == "Update Category") {
$category = Category::findCategory($categoryID);
$category->categoryID = $_POST['categoryID'];
$category->categoryCode = $_POST['categoryCode'];
$category->categoryName = $_POST['categoryName'];
$result = $category->updateCategory();
if ($result) {
   echo "<h2>Category $categoryID updated</h2>\n";
} else {
   echo "<h2>Problem updating category $categoryID</h2>\n";
}
} else {
   echo "<h2>Update Canceled for category $categoryID</h2>\n";
}
} else {
echo "<h2>Admin access required</h2>\n";
}

?>
