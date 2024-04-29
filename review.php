<?php
include('connect-db.php');
session_start();

error_reporting(E_ERROR | E_PARSE);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'], $_POST['ISBN'])) {
    $userId = $_SESSION['user_id'];
    $isbn = $_POST['ISBN'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];
    $ratingDate = date('Y-m-d');  // Current date in YYYY-MM-DD format

    $checkStmt = $db->prepare("SELECT COUNT(*) FROM Ratings WHERE user_id = ? AND ISBN = ?");
    $checkStmt->execute([$userId, $isbn]);
    $reviewExists = $checkStmt->fetchColumn() > 0;

    if ($reviewExists) {
        $errorMessage = "You have already reviewed this book. Duplicate reviews are not allowed.";
    }
    else 
    {
        $stmt = $db->prepare("INSERT INTO Ratings (user_id, ISBN, rating, comments, rating_date) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$userId, $isbn, $rating, $comments, $ratingDate]);

    }
    
    if ($result) {
        echo "Review submitted successfully!";
        // Optionally redirect or perform other action
    } else {
        $errorMessage = "You have already reviewed this book. Duplicate reviews are not allowed.";
    }
} elseif (!isset($_GET['ISBN']) && !isset($_POST['ISBN'])) {
    die("No ISBN specified.");
} else {
    $isbn = $_GET['ISBN'] ?? $_POST['ISBN'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
    <h1 class="review-title">Submit Review</h1>
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>
    <form action="review.php" method="post">
        <input type="hidden" name="ISBN" value="<?= htmlspecialchars($isbn) ?>">
        <div>
            <label for="rating">Rating:</label>
            <select name="rating" id="rating" required>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
        </div>
        <div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Comments:</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" name="comments" rows="3" style="border-radius: 15px;"></textarea>
            </div>
        </div>
        <br>
        <button class="btn btn-primary" type="submit">Submit Review</button>
    </form>
    <br>
    <a href="book_detail.php?ISBN=<?= htmlspecialchars($isbn) ?>" class="btn btn-outline-primary">Back to Book Details</a>
</body>
</html>
