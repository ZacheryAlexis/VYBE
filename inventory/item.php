<?php
require_once('database.php');
class Item
{
   public $itemID;
   public $itemName;
   public $categoryID;
   public $listPrice;
   public $stockQuantity;
   function __construct(
        $itemID,
        $itemName,
        $categoryID,
        $listPrice,
        $stockQuantity = 0
       ) {
       $this->itemID = $itemID;
       $this->itemName = $itemName;
       $this->categoryID = $categoryID;
       $this->listPrice = $listPrice;
       $this->stockQuantity = $stockQuantity;
   }
   function __toString()
   {
       $output = "<h2>Item : $this->itemID</h2>" .
           "<h2>Name: $this->itemName</h2>\n";
       "<h2>Category ID: $this->categoryID at $this->listPrice</h2>\n";
       return $output;
   }
   function saveItem()
   {
       $db = getDB();
       $query = "INSERT INTO items VALUES (?, ?, ?, ?)";
       $stmt = $db->prepare($query);
       $stmt->bind_param(
           "isid",
           $this->itemID,     // integer data type
           $this->itemName,   // string data type
           $this->categoryID, // integer data type
           $this->listPrice   // float data type
       );
       $result = $stmt->execute();
       $db->close();
       return $result;
   }
   static function getItems()
   {
       $db = getDB();
       $query = "SELECT * FROM items";
       $result = $db->query($query);
       if (mysqli_num_rows($result) > 0) {
           $items = array();
           while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
               $item = new Item(
                   $row['itemID'],
                   $row['itemName'],
                   $row['categoryID'],
                   $row['listPrice'],
                   $row['stockQuantity'] ?? 0
               );
               array_push($items, $item);
           }
           $db->close();
           return $items;
       } else {
           $db->close();
           return NULL;
       }
   }
   static function findItem($itemID)
   {
       $db = getDB();
       $query = "SELECT * FROM items WHERE itemID = ?";
       $stmt = $db->prepare($query);
       $stmt->bind_param('i', $itemID);
       $stmt->execute();
       $result = $stmt->get_result();
       $row = $result->fetch_array(MYSQLI_ASSOC);
       $stmt->close();
       if ($row) {
           $item = new Item(
               $row['itemID'],
               $row['itemName'],
               $row['categoryID'],
               $row['listPrice'],
               $row['stockQuantity'] ?? 0
           );
           $db->close();
           return $item;
       } else {
           $db->close();
           return NULL;
       }
   }
   function updateItem()
   {
       $db = getDB();
       $query = "UPDATE items SET itemName= ?, " .
           "categoryID= ?, listPrice= ? WHERE itemID = $this->itemID";
       $stmt = $db->prepare($query);
       $stmt->bind_param(
           "sid",
           $this->itemName,
           $this->categoryID,
           $this->listPrice
       );
       $result = $stmt->execute();
       $db->close();
       return $result;
   }
   function removeItem()
   {
       $db = getDB();
       $query = "DELETE FROM items WHERE itemID = ?";
       $stmt = $db->prepare($query);
       $stmt->bind_param('i', $this->itemID);
       $result = $stmt->execute();
       $stmt->close();
       $db->close();
       return $result;
   }
   static function getItemsByCategory($categoryID)
   {
       $db = getDB();
       $query = "SELECT * from items where categoryID = ?";
       $stmt = $db->prepare($query);
       $stmt->bind_param('i', $categoryID);
       $stmt->execute();
       $result = $stmt->get_result();
       if (mysqli_num_rows($result) > 0) {
           $items = array();
           while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
               $item = new Item(
                   $row['itemID'],
                   $row['itemName'],
                   $row['categoryID'],
                   $row['listPrice'],
                   $row['stockQuantity'] ?? 0
               );
               array_push($items, $item);
           }
           $stmt->close();
           $db->close();
           return $items;
       } else {
           $stmt->close();
           $db->close();
           return NULL;
       }
   }
   /**
    * Return array of variants for a given itemID. Each variant is associative array.
    */
   static function getVariants($itemID)
   {
       $db = getDB();
       $query = "SELECT variantID, sizeLabel, price, stockQuantity, imageSuffix FROM item_variants WHERE itemID = ?";
       $stmt = $db->prepare($query);
       if (!$stmt) {
           $db->close();
           return array();
       }
       $stmt->bind_param('i', $itemID);
       $stmt->execute();
       $result = $stmt->get_result();
       $variants = array();
       while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
           $variants[] = $row;
       }
       $stmt->close();
       $db->close();
       return $variants;
   }

   static function getVariantByID($variantID)
   {
       $db = getDB();
       $query = "SELECT variantID, itemID, sizeLabel, price, stockQuantity, imageSuffix FROM item_variants WHERE variantID = ?";
       $stmt = $db->prepare($query);
       if (!$stmt) {
           $db->close();
           return NULL;
       }
       $stmt->bind_param('i', $variantID);
       $stmt->execute();
       $result = $stmt->get_result();
       $row = $result->fetch_array(MYSQLI_ASSOC);
       $stmt->close();
       $db->close();
       return $row ?: NULL;
   }
   static function getTotalItems()
{
   $db = getDB();
   $query = "SELECT count(itemID) FROM items";
   $result = $db->query($query);
   $row = $result->fetch_array();
   if ($row) {
       return $row[0];
   } else {
       return NULL;
   }
}
static function getTotalListPrice()
{
   $db = getDB();
   $query = "SELECT sum(listPrice) FROM items";
   $result = $db->query($query);
   $row = $result->fetch_array();
   if ($row) {
       return $row[0];
   } else {
       return NULL;
   }
}



}
?>
