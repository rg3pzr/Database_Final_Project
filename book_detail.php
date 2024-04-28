<?php
include('connect-db.php');

// Check if the 'ISBN' GET parameter is set
if (isset($_GET['ISBN'])) {
    $isbn = $_GET['ISBN'];

    // Fetch the book details
    $stmt = $db->prepare("SELECT * FROM Books WHERE ISBN = ?");
    $stmt->execute([$isbn]);
    $book = $stmt->fetch();

    // Fetch the book reviews
    $reviewStmt = $db->prepare("SELECT * FROM Ratings WHERE ISBN = ?");
    $reviewStmt->execute([$isbn]);
    $reviews = $reviewStmt->fetchAll();
} else {
    die("No ISBN specified.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($book['title']) ?></h1>
    <!-- other book details here -->
    <h2>Reviews</h2>
    <?php foreach ($reviews as $review): ?>
        <!-- display reviews here -->
    <?php endforeach; ?>

    <a href="allbooks.php">Back to Catalog</a>
</body>
</html>
