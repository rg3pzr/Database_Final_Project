<?php
include('connect-db.php');
session_start();

$message = ''; // To store messages that will be displayed to the user

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'], $_POST['ISBN'])) {
    $userId = $_SESSION['user_id']; // Assuming the user_id is stored in the session after login
    $isbn = $_POST['ISBN'];

    // Check if the user has already liked this book
    $checkStmt = $db->prepare("SELECT * FROM Has_Read WHERE user_id = ? AND ISBN = ?");
    $checkStmt->execute([$userId, $isbn]);
    $alreadyLiked = $checkStmt->fetch();

    if (!$alreadyLiked) {
        // Insert into liked_books table
        $insertStmt = $db->prepare("INSERT INTO Has_Read (user_id, ISBN) VALUES (?, ?)");
        $result = $insertStmt->execute([$userId, $isbn]);
        
        if ($result) {
            $message = "You liked this book!";
        } else {
            $message = "Error liking the book.";
        }
    } else {
        $message = "You have already liked this book.";
    }
}

$stmt = $db->query("
    SELECT b.ISBN, b.title, b.genre, b.publication_date, AVG(r.rating) as average_rating, COUNT(r.rating) as rating_count
    FROM Books b
    LEFT JOIN Ratings r ON b.ISBN = r.ISBN
    GROUP BY b.ISBN
");
$books = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Catalog Page</title>
    <!-- Add your CSS styling here -->
</head>
<body>

    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="catalog-container">
        <div class="book-list">
            <h1>Catalog Page</h1>
            <?php foreach ($books as $book): ?>
                <div class="book-item">
                    <h2><a href="book_detail.php?ISBN=<?= urlencode($book['ISBN']) ?>"><?= htmlspecialchars($book['title']) ?></a></h2>
                    <p>Genre: <?= htmlspecialchars($book['genre']) ?></p>
                    <p>Published Date: <?= htmlspecialchars($book['publication_date']) ?></p>
                    <p>Average Ratings: 
                        <?php if ($book['rating_count'] > 0): ?>
                            <?= str_repeat('★', round($book['average_rating'])) ?>
                            (<?= $book['rating_count'] ?> Ratings)
                        <?php else: ?>
                            No ratings yet
                        <?php endif; ?>
                    </p>
                    <?php if (isset($_SESSION['user_id'])): // Check if the user is logged in ?>
                        <form action="allbooks.php" method="post">
                            <input type="hidden" name="ISBN" value="<?= htmlspecialchars($book['ISBN']) ?>">
                            <input type="submit" value="I like this book">
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="filter-section">
            <!-- Filter form can go here -->
        </div>
    </div>
</body>
</html>
