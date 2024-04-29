<?php
include('connect-db.php');

$bookImages = [
    "the-prophet" => "https://upload.wikimedia.org/wikipedia/commons/8/8a/The_Prophet_%28Gibran%29.jpg", 
    "murder-on-the-orient-express" => "https://upload.wikimedia.org/wikipedia/en/c/c0/Murder_on_the_Orient_Express_First_Edition_Cover_1934.jpg", 
    "how-the-grinch-stole-christmas!" => "https://upload.wikimedia.org/wikipedia/en/8/87/How_the_Grinch_Stole_Christmas_cover.png",
    "the-hunger-games" => "https://upload.wikimedia.org/wikipedia/en/3/39/The_Hunger_Games_cover.jpg", 
    "angela's-ashes" => "https://upload.wikimedia.org/wikipedia/en/0/0c/AngelasAshes.jpg"
    // Add more entries as needed
];
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
    <link rel="stylesheet" type="text/css" href="book_detail.css">
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
    <?php $normalizedTitle = strtolower(str_replace(' ', '-', $book['title'])); ?>
    <?php $imageUrl = ($bookImages[$normalizedTitle]) ?>
    <h1><?= htmlspecialchars($book['title']) ?></h1>
    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image">
    <!-- other book details here -->
    <h2 >Reviews</h2>
        <?php foreach ($reviews as $review): ?>
            <p>Rating: <?= str_repeat('★', (int)$review['rating']) ?></p>
            <div class="book-info">
                <p>Date: <?= htmlspecialchars($review['rating_date']) ?></p>
                <p>Comment: <?= nl2br(htmlspecialchars($review['comments'])) ?></p>
                <p>Reviewer: <?= htmlspecialchars($review['username']) ?></p> <!-- Displaying the username of the reviewer -->
            </div>
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