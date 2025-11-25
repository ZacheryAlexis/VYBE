<?php
// include("category.php");
if (isset($_SESSION['login'])) {
   $categoryID = filter_input(INPUT_POST, 'categoryID', FILTER_VALIDATE_INT);
   if ((trim($categoryID) == '') or (!is_int($categoryID))) {
       echo "<h2>Sorry, you must enter a valid category ID number</h2>\n";
   } else if (Category::findCategory($categoryID)) {
       echo "<h2>Sorry, A category with the ID #$categoryID already exists</h2>\n";
   } else {
       $categoryCode = htmlspecialchars($_POST['categoryCode']);
       $categoryName = htmlspecialchars($_POST['categoryName']);
       $category = new Category($categoryID, $categoryCode, $categoryName);
       $result = $category->saveCategory();
       if ($result)
           echo "<h2>New Category #$categoryID successfully added</h2>\n";
       else
           echo "<h2>Sorry, there was a problem adding that category</h2>\n";
   }
} else {
   echo "<h2>Please log in first</h2>\n";
}
?>
