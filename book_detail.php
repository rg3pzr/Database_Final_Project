<?php
include('connect-db.php');

// Check if the 'ISBN' GET parameter is set
if (isset($_GET['ISBN'])) {
    $isbn = $_GET['ISBN'];

    // Fetch the book details
    $stmt = $db->prepare("SELECT * FROM Books WHERE ISBN = ?");
    $stmt->execute([$isbn]);
    $book = $stmt->fetch();

    // Fetch the book reviews along with the username of the reviewer
    $reviewStmt = $db->prepare("SELECT r.*, u.username FROM Ratings r JOIN Users u ON r.user_id = u.user_id WHERE r.ISBN = ?");
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
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<nav class="top-nav">
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="allbooks.php">All Books</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="logout.php">Sign Out</a></li>
    </ul>
</nav>
    <h1><?= htmlspecialchars($book['title']) ?></h1>
    <!-- other book details here -->
    <h2>Reviews</h2>
    <?php foreach ($reviews as $review): ?>
        <p>Rating: <?= str_repeat('★', (int)$review['rating']) ?></p>
        <p>Date: <?= htmlspecialchars($review['rating_date']) ?></p>
        <p>Comment: <?= nl2br(htmlspecialchars($review['comments'])) ?></p>
        <p>Reviewer: <?= htmlspecialchars($review['username']) ?></p> <!-- Displaying the username of the reviewer -->
        <br>
    <?php endforeach; ?>

    <!-- Add Review Button -->
    <form action="review.php" method="GET">
        <input type="hidden" name="ISBN" value="<?= htmlspecialchars($isbn) ?>">
        <input type="submit" value="Add Review">
    </form>
    <br>
    <button onclick="window.location.href='allbooks.php'">Back to Catalog</button>
    <br>
</body>
</html>
