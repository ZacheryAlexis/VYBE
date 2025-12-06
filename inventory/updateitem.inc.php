<style>
   form[name="item"] {
       display: grid;
       grid-template-columns: 125px 1fr;
       gap: 10px 5px;
       align-items: left;
       max-width: 300px;
       margin: 0px;
   }
   form[name="item"] label {
       text-align: left;
       padding-right: 5px;
   }
   form[name="item"] input[type="text"] {
       width: 100%;
   }
   form[name="item"] input[type="submit"] {
       grid-column: 2;
       justify-self: start;
   }
</style>

<?php
if (empty($_SESSION['is_admin'])) {
?>
    <div class="panel" style="max-width: 600px; margin: 0 auto;">
        <h2 style="color: var(--vybe-orange); margin-top: 0;">Access Denied</h2>
        <p style="color: var(--vybe-muted);">Admin access required to update items.</p>
        <p><a class="accent-link" href="index.php?content=listitems">← Back to Items</a></p>
    </div>
<?php
} else if (!isset($_POST['itemID']) or (!is_numeric($_POST['itemID']))) {
?>
   <div class="panel" style="max-width: 600px; margin: 0 auto;">
       <h2 style="color: var(--vybe-orange); margin-top: 0;">Invalid Item</h2>
       <p style="color: var(--vybe-muted);">You did not select a valid item to update.</p>
       <p><a class="accent-link" href="index.php?content=listitems">← Back to Items</a></p>
   </div>
<?php
} else {
   $itemID = $_POST['itemID'];
   $item = Item::findItem($itemID);
   if ($item) {
?>
       <div class="panel" style="max-width: 600px; margin: 0 auto;">
           <h2 style="color: var(--vybe-orange); margin-top: 0;">Update Item #<?php echo $item->itemID; ?></h2>
           <p style="color: var(--vybe-muted); margin-bottom: 25px;">Modify the item details below.</p>
           
           <form name="items" action="index.php" method="post">
               <div style="display: grid; grid-template-columns: 140px 1fr; gap: 15px; align-items: center; margin-bottom: 25px;">
                   <label style="color: var(--vybe-text); font-weight: 500;">Item ID:</label>
                   <span style="color: var(--vybe-muted);"><?php echo $item->itemID; ?></span>
                   
                   <label style="color: var(--vybe-text); font-weight: 500;">Name:</label>
                   <input type="text" name="itemName" value="<?php echo htmlspecialchars($item->itemName); ?>" required 
                          style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
                   
                   <label style="color: var(--vybe-text); font-weight: 500;">Category ID:</label>
                   <input type="number" name="categoryID" value="<?php echo $item->categoryID; ?>" required 
                          style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
                   
                   <label style="color: var(--vybe-text); font-weight: 500;">List Price:</label>
                   <input type="number" name="listPrice" step="0.01" value="<?php echo $item->listPrice; ?>" required 
                          style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
               </div>
               
               <div style="display: flex; gap: 10px;">
                   <input type="submit" name="answer" value="Update Item" 
                          style="background: var(--vybe-orange); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
                   <input type="submit" name="answer" value="Cancel" 
                          style="background: var(--vybe-navy); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
               </div>
               <input type="hidden" name="itemID" value="<?php echo $itemID; ?>">
               <input type="hidden" name="content" value="changeitem">
           </form>
       </div>
<?php
} else {
?>
        <div class="panel" style="max-width: 600px; margin: 0 auto;">
            <h2 style="color: var(--vybe-orange); margin-top: 0;">Item Not Found</h2>
            <p style="color: var(--vybe-muted);">Sorry, item <?php echo $itemID; ?> not found.</p>
            <p><a class="accent-link" href="index.php?content=listitems">← Back to Items</a></p>
        </div>
<?php
    }
 }
 ?>
<script language="javascript">
   document.item.itemID.focus();
   document.item.itemID.select();
</script>
