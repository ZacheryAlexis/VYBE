<?php
ob_start();
include("category.php");
include("item.php");
$totalCategories = Category::getTotalCategories();
$totalItems = Item::getTotalItems();
$listpricetotal = Item::getTotalListPrice();
$doc = new DOMDocument("1.0");
$inventory = $doc->createElement("inventory");
$inventory = $doc->appendChild($inventory);
$categories = $doc->createElement("categories", $totalCategories);
$categories = $inventory->appendChild($categories);
$items = $doc->createElement("items", $totalItems);
$items = $inventory->appendChild($items);
$listprice = $doc->createElement("listpricetotal", $listpricetotal);
$listprice = $inventory->appendChild($listprice);
$output = $doc->saveXML();
header("Content-type: application/xml");
ob_end_flush();
echo $output;
?>
