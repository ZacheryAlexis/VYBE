<?php
header('Content-Type: application/json');
require_once('database.php');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$db = getDB();
$searchTerm = '%' . $query . '%';

$sql = "
    SELECT i.itemID, i.itemName, i.listPrice, c.categoryName 
    FROM items i 
    LEFT JOIN categories c ON i.categoryID = c.categoryID 
    WHERE i.itemName LIKE ? 
       OR i.description LIKE ?
    ORDER BY 
        CASE 
            WHEN i.itemName LIKE ? THEN 1
            ELSE 2
        END,
        i.itemName ASC
    LIMIT 8
";

$stmt = $db->prepare($sql);
$stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = [
        'itemID' => $row['itemID'],
        'itemName' => $row['itemName'],
        'listPrice' => number_format($row['listPrice'], 2),
        'categoryName' => $row['categoryName'] ?? 'Uncategorized'
    ];
}

$db->close();
echo json_encode($suggestions);
?>
