<?php
include('connect-db.php');
$stmt = $db->query("SELECT ISBN, title, genre, publication_date, avg_rating FROM Books");
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
                    <p>Average Ratings: <?= str_repeat('★', (int)$book['avg_rating']) ?></p>
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
