<script language="javascript">
   function listbox_dblclick() {
       document.items.displayitem.click()
   }
   function button_click(target) {
       var userConfirmed = true;
       if (target == 1) {
           userConfirmed = confirm("Are you sure you want to remove this item?");
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

<div class="panel" style="max-width: 700px; margin: 0 auto;">
    <h2 style="color: var(--vybe-orange); margin-top: 0;">Browse All Scents</h2>
    <p style="color: var(--vybe-muted); margin-bottom: 20px;">Select an item to view details or double-click to open.</p>
    
    <form name="items" method="post">
        <select ondblclick="listbox_dblclick()" name="itemID" size="15" 
                style="width: 100%; padding: 12px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 8px; font-size: 1rem; margin-bottom: 15px;">
<?php
$items = Item::getItems();
foreach ($items as $item) {
   $itemID = $item->itemID;
   $itemName = $item->itemName;
   $itemPrice = number_format($item->listPrice, 2);
   $option = "#$itemID — $itemName — \$$itemPrice";
   echo "<option value=\"$itemID\">$option</option>\n";
}
?>
        </select>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="submit" onClick="button_click(0)" name="displayitem" value="View Item" 
                   style="background: var(--vybe-orange); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
            <?php if (!empty($_SESSION['is_admin'])) { ?>
            <input type="submit" onClick="button_click(1)" name="deleteitem" value="Delete Item" 
                   style="background: #d32f2f; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
            <input type="submit" onClick="button_click(2)" name="updateitem" value="Update Item" 
                   style="background: var(--vybe-navy); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
            <?php } ?>
        </div>
    </form>
</div>

