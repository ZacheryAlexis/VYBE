<?php
require_once("category.php");
if (isset($_SESSION['login'])) {
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
echo "<h2>Please login first</h2>\n";
}

?>
