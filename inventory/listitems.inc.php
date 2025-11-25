<script language="javascript">
   function listbox_dblclick() {
       document.items.displayitem.click()
   }
   function button_click(target) {
       var userConfirmed = true;
       if (target == 1) {
           userConfirmed = confirm("Are you sure you want to remove this category?");
       }
       if (userConfirmed) {
           if (target == 0) items.action = "index.php?content=displayitem";
           if (target == 1) items.action = "index.php?content=removeitem";
           if (target == 2) items.action = "index.php?content=updateitem";
       } else {
           alert("Action canceled.");
       }
   }
</script>

<h2>Select Item</h2>
<form name="items" method="post">
   <select  ondblclick="listbox_dblclick()" name="itemID" size="20">
<?php
// include("item.php");
$items = Item::getItems();
foreach ($items as $item) {
   $itemID = $item->itemID;
   $itemName = $item->itemName;
   $itemPrice = $item->listPrice;
   $option = $itemID . " - " . $itemName .  " - " . $itemPrice;
   echo "<option value=\"$itemID\">$option</option>\n";
}
?>
   </select>
   <br>
   <input type="submit" onClick="button_click(0)" name="displayitem" value="View Item">
   <input type="submit" onClick="button_click(1)" name="deleteitem" value="Delete Item">
   <input type="submit" onClick="button_click(2)" name="updateitem" value="Update Item">

</form>

