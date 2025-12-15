<?php
require_once('security.php');
require_once('item.php');

if (!isset($_REQUEST['itemID']) or (!is_numeric($_REQUEST['itemID']))) {
?>
 <h2>You did not select a valid itemID to view.</h2>
 <a href="index.php?content=listitems">List Items</a>
 <?php
} else {
 $itemID = intval($_REQUEST['itemID']);
 $item = Item::findItem($itemID);
 if ($item) {
     $stockQty = $item->stockQuantity ?? 0;
     // Prepare variant data if available
     $variants = array();
     try {
         $variants = Item::getVariants($item->itemID) ?: array();
     } catch (Exception $e) {
         $variants = array();
     }
     // Determine image path (prefer png, then svg)
     $baseName = str_replace(' ', '_', $item->itemName);
     $pngPathFs = __DIR__ . '/images/' . $baseName . '.png';
     $svgPathFs = __DIR__ . '/images/' . $baseName . '.svg';
     $pngWeb = 'images/' . $baseName . '.png';
     $svgWeb = 'images/' . $baseName . '.svg';
     if (file_exists($pngPathFs)) {
         $imgPath = $pngWeb;
     } elseif (file_exists($svgPathFs)) {
         $imgPath = $svgWeb;
     } else {
         $imgPath = 'images/items.png';
     }
 ?>
    <div class="panel" style="max-width: 760px; margin: 0 auto;">
       <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
           <h2 style="color: var(--vybe-orange); margin: 0;"><?php echo htmlspecialchars($item->itemName); ?></h2>
           <?php
           if ($stockQty <= 0) {
               echo '<span style="background: rgba(245,101,101,0.2); color: #f56565; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; border: 1px solid rgba(245,101,101,0.4);">Out of Stock</span>';
           } elseif ($stockQty < 10) {
               echo '<span style="background: rgba(237,137,54,0.2); color: #ed8936; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; border: 1px solid rgba(237,137,54,0.4);">Only ' . $stockQty . ' left</span>';
           } else {
               echo '<span style="background: rgba(72,187,120,0.2); color: #48bb78; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; border: 1px solid rgba(72,187,120,0.4);">In Stock</span>';
           }
           ?>
       </div>
       <p style="color: var(--vybe-muted); margin: 10px 0;">Item ID: <?php echo $item->itemID; ?></p>
       <p style="font-size: 1.8rem; font-weight: 700; color: var(--vybe-text); margin: 12px 0;">
           $<span id="priceDisplay"><?php echo number_format($item->listPrice, 2); ?></span>
       </p>

       <div style="display:flex; justify-content:center; margin-bottom: 12px;">
           <img id="productImage" src="<?php echo htmlspecialchars($imgPath); ?>" alt="<?php echo htmlspecialchars($item->itemName); ?>" style="width:220px; max-width:48%; max-height:260px; object-fit:contain; border-radius: 10px; border: 8px solid white; background: #111; padding: 8px; box-shadow: 0 6px 18px rgba(0,0,0,0.55);">
       </div>

    <div style="display: flex; gap: 8px; align-items: flex-end; margin: 16px 0;">
           <form method="post" action="index.php?content=addtocart" style="flex: 1; margin: 0;">
               <?php csrf_field(); ?>
               <input type="hidden" name="itemID" value="<?php echo $item->itemID; ?>">
               <input type="hidden" name="variantID" id="variantIDField" value="">
               <label style="color: var(--vybe-text); margin-bottom: 10px; display: block;">Quantity:</label>
               <div style="display: flex; gap: 10px; align-items: center;">
                     <input type="number" name="quantity" value="1" min="1" max="<?php echo max(1, $stockQty); ?>" 
                         style="width: 70px; padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px;" 
                          <?php echo $stockQty <= 0 ? 'disabled' : ''; ?>>
                   <button type="submit" style="background: var(--vybe-orange); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; flex: 1;" 
                           <?php echo $stockQty <= 0 ? 'disabled' : ''; ?>>
                       <?php echo $stockQty <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                   </button>
               </div>
           </form>

               <?php if (!empty($variants)): ?>
               <div style="min-width:110px;">
                   <label style="display:block; margin-bottom:6px; color: var(--vybe-muted);">Size:</label>
                   <select id="variantSelect" data-original-price="<?php echo htmlspecialchars(number_format($item->listPrice,2)); ?>" data-base-name="<?php echo htmlspecialchars($baseName); ?>" style="padding:8px; border-radius:8px; background: var(--vybe-bg); color: var(--vybe-text); border:1px solid rgba(199,185,255,0.3); width:110px;">
                       <option value="">50ml</option>
                       <?php foreach ($variants as $v): ?>
                           <option value="<?php echo $v['variantID']; ?>" data-price="<?php echo $v['price']; ?>" data-stock="<?php echo $v['stockQuantity']; ?>" data-suffix="<?php echo htmlspecialchars($v['imageSuffix']); ?>"><?php echo htmlspecialchars($v['sizeLabel']) . ' — $' . number_format($v['price'],2); ?></option>
                       <?php endforeach; ?>
                   </select>
               </div>
           <?php endif; ?>

           <?php if (isset($_SESSION['user_id'])): ?>
           <form method="post" action="index.php?content=addwishlist" style="margin: 0;">
               <?php csrf_field(); ?>
               <input type="hidden" name="itemID" value="<?php echo $item->itemID; ?>">
               <input type="hidden" name="variantID" id="wishlistVariantIDField" value="">
               <button type="submit" style="background: rgba(199,185,255,0.1); color: var(--vybe-text); padding: 12px 20px; border: 1px solid rgba(199,185,255,0.3); border-radius: 8px; font-size: 1.1rem; cursor: pointer; margin-top: 30px;" title="Add to Wishlist">
                   ♥ Wishlist
               </button>
           </form>
           <?php endif; ?>
       </div>

       <?php if (!empty($_SESSION['wishlist_message'])): ?>
           <p style="background: rgba(72,187,120,0.2); color: #48bb78; padding: 12px; border-radius: 6px; border: 1px solid rgba(72,187,120,0.4);">
               <?php echo htmlspecialchars($_SESSION['wishlist_message']); unset($_SESSION['wishlist_message']); ?>
           </p>
       <?php endif; ?>

       <p style="margin-top: 25px;">
           <a class="accent-link" href="index.php?content=listitems">← Back to All Scents</a>
       </p>
   </div>
   <?php if (!empty($variants)): ?>
       <script src="js/variants.js"></script>
   <?php endif; ?>
<?php
 } else {
   echo "<h2>Sorry, item not found.</h2>\n";
 }
}
?>
