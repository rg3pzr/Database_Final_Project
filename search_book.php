<?php
include('connect-db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
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
// Initialize variables
$search_results = array();

// Process user input
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];

    // Construct SQL query
    $sql = "SELECT b.ISBN, b.title, b.genre, b.publication_date, AVG(r.rating) as average_rating, COUNT(r.rating) as rating_count
    FROM Books b
    LEFT JOIN Ratings r ON b.ISBN = r.ISBN
    WHERE b.title LIKE '%$search_term%'
    GROUP BY b.ISBN";

    // Execute SQL query
    $result = $db->query($sql);

    if($result != false){
        $search_results = $result->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
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

<h1>Search Results</h1>
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
    usort($search_results, 'sortBooks');
}
?>
<?php if (!empty($search_results)) : ?>
    <?php foreach ($search_results as $book) : ?>
        <h3><a href="book_detail.php?ISBN=<?= urlencode($book['ISBN']) ?>"><?= htmlspecialchars($book['title']) ?></a></h3>
        <h4>Genre: <?php echo $book['genre']; ?></h4>
        <h4>Published Date: <?php echo $book['publication_date']; ?></h4>
        <h4>Average Rating: 
            <?php if ($book['rating_count'] > 0): ?>
            <?= str_repeat('★', round($book['average_rating'])) ?>
            (<?= $book['rating_count'] ?> Ratings)
            <?php else: ?>
                No ratings yet
            <?php endif; ?>
        </h4>
        <?php if (isset($_SESSION['user_id'])): // Check if the user is logged in
            // Check if the book has been liked
            $checkStmt = $db->prepare("SELECT * FROM Has_Read WHERE user_id = ? AND ISBN = ?");
            $checkStmt->execute([$_SESSION['user_id'], $book['ISBN']]);
            $alreadyLiked = $checkStmt->fetch();
            $buttonText = $alreadyLiked ? 'Unlike' : 'Like';
        ?>
            <form action="search_book.php?search=<?php echo urlencode($search_term); ?>" method="post">
                <input type="hidden" name="ISBN" value="<?= htmlspecialchars($book['ISBN']) ?>">
                <input type="submit" name="<?= strtolower($buttonText) ?>" value="<?= $buttonText ?>">
            </form>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else : ?>
    <p>No results found</p>
<?php endif; ?>

</body>
</html>
