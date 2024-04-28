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
        <p>Rating: <?= str_repeat('★', (int)$review['rating']) ?></p>
        <p>Date: <?= htmlspecialchars($review['rating_date']) ?></p>
        <p>Comment: <?= nl2br(htmlspecialchars($review['comments'])) ?></p>
    <?php endforeach; ?>


    <h2>Reviews</h2>
    <?php foreach ($reviews as $review): ?>
        <p>Rating: <?= str_repeat('★', (int)$review['rating']) ?></p>
        <p>Date: <?= htmlspecialchars($review['rating_date']) ?></p>
        <p>Comment: <?= nl2br(htmlspecialchars($review['comments'])) ?></p>
    <?php endforeach; ?>

    <!-- Add Review Button -->
    <form action="review.php" method="GET">
        <input type="hidden" name="ISBN" value="<?= htmlspecialchars($isbn) ?>">
        <input type="submit" value="Add Review">
    </form>

    <a href="allbooks.php">Back to Catalog</a>
</body>
</html>
