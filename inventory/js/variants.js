document.addEventListener('DOMContentLoaded', function(){
  var sel = document.getElementById('variantSelect');
  if (!sel) return;
  var field = document.getElementById('variantIDField');
  var wishlistField = document.getElementById('wishlistVariantIDField');
  var priceEl = document.getElementById('priceDisplay') || document.querySelector('p[style*="font-size: 1.8rem"]');
  var img = document.getElementById('productImage') || document.querySelector('div > img[alt]');

  sel.addEventListener('change', function(){
    var opt = sel.options[sel.selectedIndex];
    var v = opt.value;
    if (field) field.value = v;
    if (wishlistField) wishlistField.value = v;
    if (v === '') {
      // reset price to item price (stored in data-original-price on select)
      var original = sel.getAttribute('data-original-price');
      if (priceEl && original) {
        // if priceEl is a span or element without innerHTML structure, set textContent
        priceEl.textContent = parseFloat(original).toFixed(2);
        // if parent shows the dollar sign, ensure it's preserved; otherwise replace appropriately
      }
      // reset image to base
      var base = sel.getAttribute('data-base-name');
      if (img && base) img.src = 'images/' + base + '.png';
    } else {
      var p = opt.getAttribute('data-price');
      var suffix = opt.getAttribute('data-suffix') || '_mini';
      if (priceEl && p) priceEl.textContent = parseFloat(p).toFixed(2);
      var base = sel.getAttribute('data-base-name');
      if (img && base) img.src = 'images/' + base + suffix + '.png';
    }
  });
});
