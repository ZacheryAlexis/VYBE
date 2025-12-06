<?php
require_once('security.php');

if (!isset($_REQUEST['itemID']) or (!is_numeric($_REQUEST['itemID']))) {
?>
 <h2>You did not select a valid itemID to view.</h2>
 <a href="index.php?content=listitems">List Items</a>
 <?php
} else {
 $itemID = $_REQUEST['itemID'];
 $item = Item::findItem($itemID);
 if ($item) {
 ?>
   <div class="panel" style="max-width: 600px; margin: 0 auto;">
       <h2 style="color: var(--vybe-orange); margin-top: 0;"><?php echo htmlspecialchars($item->itemName); ?></h2>
       <p style="color: var(--vybe-muted); margin: 10px 0;">Item ID: <?php echo $item->itemID; ?></p>
       <p style="font-size: 1.8rem; font-weight: 700; color: var(--vybe-text); margin: 20px 0;">
           $<?php echo number_format($item->listPrice, 2); ?>
       </p>
       
       <form method="post" action="index.php?content=addtocart" style="margin: 25px 0;">
           <?php csrf_field(); ?>
           <input type="hidden" name="itemID" value="<?php echo $item->itemID; ?>">
           <label style="color: var(--vybe-text); margin-bottom: 10px; display: block;">Quantity:</label>
           <input type="number" name="quantity" value="1" min="1" max="10" 
                  style="width: 80px; padding: 8px; margin-right: 15px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px;">
           <button type="submit" style="background: var(--vybe-orange); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
               Add to Cart
           </button>
       </form>
       
       <p style="margin-top: 25px;">
           <a class="accent-link" href="index.php?content=listitems">← Back to All Scents</a>
       </p>
   </div>
<?php
 } else {
   echo "<h2>Sorry, item not found.</h2>\n";
 }
}
?>
