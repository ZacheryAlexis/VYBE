<?php
if (!isset($_REQUEST['categoryID']) or (!is_numeric($_REQUEST['categoryID']))) {
?>
 <div class="panel" style="max-width: 600px; margin: 0 auto;">
     <h2 style="color: var(--vybe-orange); margin-top: 0;">Invalid Category</h2>
     <p style="color: var(--vybe-muted);">You did not select a valid category to view.</p>
     <p><a class="accent-link" href="index.php?content=listcategories">← Back to Categories</a></p>
 </div>
<?php
} else {
 $categoryID = $_REQUEST['categoryID'];
 $category = Category::findCategory($categoryID);
 if ($category) {
?>
   <div class="panel" style="max-width: 700px; margin: 0 auto;">
       <h2 style="color: var(--vybe-orange); margin-top: 0;"><?php echo htmlspecialchars($category->categoryName); ?></h2>
       <p style="color: var(--vybe-muted); margin: 10px 0;">Category ID: <?php echo $category->categoryID; ?> | Code: <?php echo htmlspecialchars($category->categoryCode); ?></p>
       
<?php
   $items = Item::getItemsByCategory($categoryID);
   if ($items) {
?>
       <h3 style="margin-top: 25px; margin-bottom: 15px; color: var(--vybe-text);">Items in this Category:</h3>
       <table style="width: 100%; border-collapse: collapse;">
           <thead>
               <tr style="background: var(--vybe-bg); border-bottom: 2px solid var(--vybe-navy);">
                   <th style="padding: 12px; text-align: left; color: var(--vybe-text);">Item ID</th>
                   <th style="padding: 12px; text-align: left; color: var(--vybe-text);">Name</th>
                   <th style="padding: 12px; text-align: right; color: var(--vybe-text);">Price</th>
               </tr>
           </thead>
           <tbody>
<?php
       foreach ($items as $item) {
?>
               <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                   <td style="padding: 10px; color: var(--vybe-muted);"><?php echo $item->itemID; ?></td>
                   <td style="padding: 10px;">
                       <a href="index.php?content=displayitem&itemID=<?php echo $item->itemID; ?>" class="accent-link">
                           <?php echo htmlspecialchars($item->itemName); ?>
                       </a>
                   </td>
                   <td style="padding: 10px; text-align: right; color: var(--vybe-text); font-weight: 600;">$<?php echo number_format($item->listPrice, 2); ?></td>
               </tr>
<?php
       }
?>
           </tbody>
       </table>
<?php
   } else {
       echo "<p style='color: var(--vybe-muted); margin-top: 20px;'>There are no items in this category.</p>\n";
   }
?>
       <p style="margin-top: 25px;"><a class="accent-link" href="index.php?content=listcategories">← Back to Categories</a></p>
   </div>
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
