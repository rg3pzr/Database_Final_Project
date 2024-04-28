<?php
include('connect-db.php');

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
                    <!-- Add to Reading List functionality can be implemented as needed -->
                </div>
            <?php endforeach; ?>
        </div>
        <div class="filter-section">
            <!-- Filter form can go here -->
        </div>
    </div>
</body>
</html>

