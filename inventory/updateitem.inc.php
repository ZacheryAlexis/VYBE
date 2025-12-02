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
    echo "<h2>Admin access required</h2>\n";
} else if (!isset($_POST['itemID']) or (!is_numeric($_POST['itemID']))) {
?>
   <h2>You did not select a valid itemID value</h2>
   <a href="index.php?content=listitems">List items</a>
   <?php
} else {
   $itemID = $_POST['itemID'];
   $item = Item::findItem($itemID);
   if ($item) {
   ?>
       <h2>Update Item <?php echo $item->itemID; ?></h2><br>
       <form name="items" action="index.php" method="post">
           <table>
               <tr>
                   <td>ItemID</td>
                   <td><?php echo $item->itemID; ?></td>
               </tr>
               <tr>
               <td>Name</td>
                   <td><input type="text" name="itemName" value="<?php echo $item->itemName; ?>"></td>
               </tr>
               <tr>
                   <td>Category ID</td>
               <td><input type="text" name="categoryID" value="<?php echo $item->categoryID; ?>"></td>
               </tr>
               <tr>
                   <td>List Price</td>
                 <td><input type="text" name="listPrice" value="<?php echo $item->listPrice; ?>"></td>
               </tr>
           </table><br><br>
           <input type="submit" name="answer" value="Update Item">
           <input type="submit" name="answer" value="Cancel">
           <input type="hidden" name="itemID" value="<?php echo $itemID; ?>">
           <input type="hidden" name="content" value="changeitem">
       </form>
   <?php
} else {
    ?>
        <h2>Sorry, item <?php echo $itemID; ?> not found</h2>
        <a href="index.php?content=listitems">List items</a>
 <?php
    }
 }
 ?>
<script language="javascript">
   document.item.itemID.focus();
   document.item.itemID.select();
</script>
