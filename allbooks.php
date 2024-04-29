<?php
include('connect-db.php');
session_start();

$message = ''; // To store messages that will be displayed to the user

$bookImages = [
    "the-prophet" => "https://upload.wikimedia.org/wikipedia/commons/8/8a/The_Prophet_%28Gibran%29.jpg", 
    "murder-on-the-orient-express" => "https://upload.wikimedia.org/wikipedia/en/c/c0/Murder_on_the_Orient_Express_First_Edition_Cover_1934.jpg", 
    "how-the-grinch-stole-christmas!" => "https://upload.wikimedia.org/wikipedia/en/8/87/How_the_Grinch_Stole_Christmas_cover.png",
    "the-hunger-games" => "https://upload.wikimedia.org/wikipedia/en/3/39/The_Hunger_Games_cover.jpg", 
    "angela's-ashes" => "https://upload.wikimedia.org/wikipedia/en/0/0c/AngelasAshes.jpg",
    "insurgent" => "https://upload.wikimedia.org/wikipedia/en/9/9c/Insurgent_%28book%29.jpeg",
    "the-giving-tree" => "https://upload.wikimedia.org/wikipedia/en/7/79/The_Giving_Tree.jpg",
    "a-light-in-the-attic" => "https://upload.wikimedia.org/wikipedia/en/1/1b/A_Light_in_the_Attic_cover.jpg",
    "neverwhere" => "https://upload.wikimedia.org/wikipedia/en/1/13/Neverwhere.jpg"
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'], $_POST['ISBN'])) {
    $userId = $_SESSION['user_id'];
    $isbn = $_POST['ISBN'];
    $action = isset($_POST['like']) ? 'like' : 'unlike';

    if ($action == 'like') {
        // Attempt to like the book
        $insertStmt = $db->prepare("INSERT INTO Has_Read (user_id, ISBN) VALUES (?, ?)");
        $result = $insertStmt->execute([$userId, $isbn]);
    } elseif ($action == 'unlike') {
        // Attempt to unlike the book
        $deleteStmt = $db->prepare("DELETE FROM Has_Read WHERE user_id = ? AND ISBN = ?");
        $result = $deleteStmt->execute([$userId, $isbn]);
        
        if ($result) {
            $message = "You unliked this book.";
        } else {
            $message = "Error unliking the book.";
        }
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
    <link rel="stylesheet" type="text/css" href="allbooks.css">
</head>
<body>

<header>
  <nav class="top-nav">
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="allbooks.php">All Books</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="logout.php">Sign Out</a></li>
    </ul>
  </nav>
</header>

    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="catalog-container">
        <div class="book-list">
            <h1>Catalog Page</h1>

            <!-- Form for sorting options -->
            <form method="post" action="">
                <h2>Sort by:
                    <label><input type="radio" name="sort_option" value="asc" <?php if(isset($_POST['sort_option']) && $_POST['sort_option'] == 'asc') echo 'checked'; ?>> Ascending</label>
                    <label><input type="radio" name="sort_option" value="desc" <?php if(isset($_POST['sort_option']) && $_POST['sort_option'] == 'desc') echo 'checked'; ?>> Descending</label>
                    <label><input type="radio" name="sort_option" value="none" <?php if(!isset($_POST['sort_option']) || $_POST['sort_option'] == 'none') echo 'checked'; ?>> None</label>
                    <input type="submit" value="Apply">
                </h2>
            </form>
            <?php 
                function sortBooks($a, $b) {
                    if ($_POST['sort_option'] == 'asc') {
                        return strcmp($a['title'], $b['title']);
                    } elseif ($_POST['sort_option'] == 'desc') {
                        return strcmp($b['title'], $a['title']);
                    } else {
                        return 0; // No sorting
                    }
                }

                // Check if sort_option is set and valid
                if(isset($_POST['sort_option']) && in_array($_POST['sort_option'], ['asc', 'desc'])) {
                    // Sort the books based on the selected option
                    usort($books, 'sortBooks');
                }
            ?>

            <?php foreach ($books as $book): ?>
                <?php $normalizedTitle = strtolower(str_replace(' ', '-', $book['title'])); ?>
                <?php error_reporting(E_ERROR | E_PARSE); $imageUrl = ($bookImages[$normalizedTitle]); ?>
                <div class="book-item">
                    <div class="book-info">
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
                        <?php if (isset($_SESSION['user_id'])): // Check if the user is logged in
                            // Check if the book has been liked
                            $checkStmt = $db->prepare("SELECT * FROM Has_Read WHERE user_id = ? AND ISBN = ?");
                            $checkStmt->execute([$_SESSION['user_id'], $book['ISBN']]);
                            $alreadyLiked = $checkStmt->fetch();
                            $buttonText = $alreadyLiked ? 'Unlike' : 'Like';
                        ?>
                            <form action="allbooks.php" method="post">
                                <input type="hidden" name="ISBN" value="<?= htmlspecialchars($book['ISBN']) ?>">
                                <input type="submit" name="<?= strtolower($buttonText) ?>" value="<?= $buttonText ?>">
                            </form>
                        <?php endif; ?>
                    </div>
                    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image">
                 </div>
            <?php endforeach; ?>
        </div>
        <div class="filter-section">
            <!-- Filter form can go here -->
        </div>
    </div>
<script> 
</script> 
</body>
</html>
