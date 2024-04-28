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
    $sql = "SELECT * FROM Books b WHERE title LIKE '%$search_term%'";

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

<?php if (!empty($search_results)) : ?>
    <?php foreach ($search_results as $book) : ?>
        <h3><a href="book_detail.php?ISBN=<?= urlencode($book['ISBN']) ?>"><?= htmlspecialchars($book['title']) ?></a></h3>
        <h4>Genre: <?php echo $book['genre']; ?></h4>
        <h4>Published Date: <?php echo $book['publication_date']; ?></h4>
        <h4>Average Rating: <?php echo $book['avg_rating']; ?> </h4>
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
