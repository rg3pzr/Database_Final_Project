<?php
include('connect-db.php');

// Fetch all books from the database
$stmt = $db->query("SELECT title, genre, publication_date, avg_rating FROM Books");
$books = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Catalog Page</title>
    <style>
        /* Add your CSS styling here */
        body { font-family: Arial, sans-serif; }
        .catalog-container { display: flex; }
        .book-list { width: 70%; padding: 20px; }
        .filter-section { width: 30%; padding: 20px; }
        .book-item { margin-bottom: 20px; }
        /* More styles can be added as needed */
    </style>
</head>
<body>
    <div class="catalog-container">
        <div class="book-list">
            <h1>Catalog Page</h1>
            <?php foreach ($books as $book): ?>
                <div class="book-item">
                    <h2><?= htmlspecialchars($book['title']) ?></h2>
                    <p>Genre: <?= htmlspecialchars($book['genre']) ?></p>
                    <p>Published Date: <?= htmlspecialchars($book['publication_date']) ?></p>
                    <p>Average Ratings: <?= str_repeat('★', $book['avg_rating']) ?></p>
                    <button onclick="addToReadingList('<?= $book['title'] ?>')">Add to my Reading List</button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="filter-section">
            <!-- Filter form can go here -->
        </div>
    </div>

    <script>
        function addToReadingList(title) {
            // Implement the functionality to add to reading list
            alert("Add " + title + " to reading list not implemented yet.");
        }
    </script>
</body>
</html>
