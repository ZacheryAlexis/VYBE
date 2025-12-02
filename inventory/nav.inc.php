<table width="100%" cellpadding="3">
   <?php
   if (empty($_SESSION['is_admin'])) {
   ?>
       <tr>
              <td>
              
                  </td>
       </tr>
   <?php
   } else {
       echo "<td><h3>Welcome, " . htmlspecialchars($_SESSION['user_name'] ?? 'Admin') . "</h3></td>\n";
   ?>
       <tr>
            <td><img src="images/home.png" alt="Home Icon" width="12" height="12">&nbsp;
                <a href="index.php"><strong>Home</strong></a>
       </tr>
       <tr>
            <td><img src="images/categories.png" alt="Categories Icon" width="12" height="12">&nbsp;
                <strong>Categories</strong>
       </tr>
       <tr>
           <td>&nbsp;&nbsp;&nbsp;<a href="index.php?content=listcategories">
                   <strong>List Categories</strong></a></td>
       </tr>
       <tr>
           <td>&nbsp;&nbsp;&nbsp;<a href="index.php?content=newcategory">
                   <strong>Add New Category</strong></a></td>
       </tr>
       <tr>
            <td><img src="images/items.png" alt="Items Icon" width="12" height="12">&nbsp;
            <strong>Items</strong>
       </tr>
       <tr>
           <td>&nbsp;&nbsp;&nbsp;<a href="index.php?content=listitems">
                   <strong>List Items</strong></a></td>
       </tr>
       <tr>
           <td>&nbsp;&nbsp;&nbsp;<a href="index.php?content=newitem">
                   <strong>Add New Item</strong></a></td>
       </tr>
       <tr>
           <td>
               <hr />
           </td>
       </tr>
       <tr>
       <td><a href="index.php?content=logout">
                   <img src="images/logout.png" alt="Logout Icon" width="12" height="12"></a>&nbsp;
               <a href="index.php?content=logout">
                   <strong>Logout</strong></a>
           </td>
       </tr>
       <tr>
           <td>&nbsp;</td>
       </tr>
       <tr>
           <td>
               <form action="index.php" method="post">
                   <label>Search for Item:</label><br>
                   <input type="text" name="itemID" size="14" />
                   <input type="submit" value="find" />
                   <input type="hidden" name="content" value="updateitem" />
                   </form>
           </td>
       </tr>
       <tr>
           <td>
               <form action="index.php" method="post">
                   <label>Search for Category:</label><br>
                   <input type="text" name="categoryID" size="14" />
                   <input type="submit" value="find" />
                   <input type="hidden" name="content" value="displaycategory" />
               </form>
           </td>
       </tr>
   <?php
   }
   ?>
</table>
