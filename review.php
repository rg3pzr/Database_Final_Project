<?php
include('connect-db.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'], $_POST['ISBN'])) {
    $userId = $_SESSION['user_id'];
    $isbn = $_POST['ISBN'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];
    $ratingDate = date('Y-m-d');  // Current date in YYYY-MM-DD format

    // Insert the new review into the Ratings table with the rating date
    $stmt = $db->prepare("INSERT INTO Ratings (user_id, ISBN, rating, comments, rating_date) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$userId, $isbn, $rating, $comments, $ratingDate]);
    
    if ($result) {
        echo "Review submitted successfully!";
        // Optionally redirect or perform other action
    } else {
        echo "Error submitting the review.";
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
</head>
<body>
    <h1>Submit Review</h1>
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
            <label for="comments">Comments:</label>
            <textarea name="comments" id="comments" rows="4" required></textarea>
        </div>
        <button type="submit">Submit Review</button>
    </form>
    <a href="book_detail.php?ISBN=<?= htmlspecialchars($isbn) ?>">Back to Book Details</a>
</body>
</html>
