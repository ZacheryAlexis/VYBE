<?php
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
   <h2>Item ID: <?php echo $item->itemID; ?></h2>
   <h2>Item Name: <?php echo $item->itemName; ?></h2>
   <h2>Item Price: <?php echo $item->listPrice; ?></h2>
   <br>
<?php
 } else {
   echo "<h2>Sorry, item not found.</h2>\n";
 }
}
?>
