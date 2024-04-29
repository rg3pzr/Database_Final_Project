<?php
include('connect-db.php');
session_start();


if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header('Location: login.php');
    exit;
}
 

$userId = $_SESSION['user_id'];
$userStmt = $db->prepare("SELECT username FROM Users WHERE user_id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();
 
$booksStmt = $db->prepare("
    SELECT b.ISBN, b.title, b.genre, b.publication_date
    FROM Has_Read hr
    JOIN Books b ON hr.ISBN = b.ISBN
    WHERE hr.user_id = ?
");
$booksStmt->execute([$userId]);
$likedBooks = $booksStmt->fetchAll();

$recommendations = [];
if (isset($_POST['generate'])) {
    // Find genres of liked books
    $genres = array_unique(array_column($likedBooks, 'genre'));

    // Create a string of genre placeholders separated by commas for the SQL IN() clause
    $inQuery = implode(',', array_fill(0, count($genres), '?'));

    // Query for random books from the same genres
    $recStmt = $db->prepare("
        SELECT * FROM Books 
        WHERE genre IN ($inQuery) AND
        ISBN NOT IN (SELECT ISBN FROM Has_Read)
        ORDER BY RAND()
        LIMIT 5
    ");
    $recStmt->execute($genres);
    $recommendations = $recStmt->fetchAll();
}

if (isset($_POST['export_liked'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="liked_books.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ISBN', 'Title', 'Genre', 'Publication Date'));
    foreach ($likedBooks as $book) {
        fputcsv($output, $book);
    }
    fclose($output);
    exit;
}

if (isset($_POST['export_recommended'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="recommended_books.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ISBN', 'Title', 'Genre'));
    foreach ($recommendations as $book) {
        fputcsv($output, $book);
    }
    fclose($output);
    exit;
}

// Import CSV functionality
if (isset($_POST['import']) && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    if ($file['error'] === UPLOAD_ERR_OK && $file['type'] === 'text/csv') {
        $handle = fopen($file['tmp_name'], 'r');
        if ($handle !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $isbn = $data[0];
                $checkStmt = $db->prepare("SELECT * FROM Has_Read WHERE user_id = ? AND ISBN = ?");
                $checkStmt->execute([$userId, $isbn]);
                if ($checkStmt->fetch() === false) {
                    $insertStmt = $db->prepare("INSERT INTO Has_Read (user_id, ISBN) VALUES (?, ?)");
                    $insertStmt->execute([$userId, $isbn]);
                }
            }
            fclose($handle);
            echo "<p>Liked books imported successfully.</p>";
        }
    } else {
        echo "<p>Error uploading file. Please ensure you are uploading a CSV file.</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
</head>
<body>
    <h1>User Profile: <?= htmlspecialchars($user['username']) ?></h1>
    <h2>Liked Books</h2>
    <?php if (count($likedBooks) > 0): ?>
        <ul>
            <?php foreach ($likedBooks as $book): ?>
                <li>
                    <strong><?= htmlspecialchars($book['title']) ?></strong> (<?= htmlspecialchars($book['genre']) ?>) - Published: <?= htmlspecialchars($book['publication_date']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <form method="post">
            <button type="submit" name="export_liked">Export Liked Books</button>  
        </form>
    <?php else: ?>
        <p>You have not liked any books yet.</p>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="generate">Generate Recommendation List</button>
    </form>

    <?php if (count($recommendations) > 0): ?>
        <h2>Recommended Books</h2>
        <ul>
            <?php foreach ($recommendations as $book): ?>
                <li><?= htmlspecialchars($book['title']) ?> - <?= htmlspecialchars($book['genre']) ?></li>
            <?php endforeach; ?>
        </ul>
        <form method="post">
            <button type="submit" name="export_liked">Export Liked Books</button>
        </form>
    <?php endif; ?>

    <form action="profile.php" method="post" enctype="multipart/form-data">
        <label>Upload CSV of Books to Like:</label>
        <input type="file" name="file" required>
        <button type="submit" name="import">Import Liked Books</button>
    </form>
    
</body>
</html>
