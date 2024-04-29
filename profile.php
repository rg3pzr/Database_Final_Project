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
$likedBooks = $booksStmt->fetchAll(PDO::FETCH_ASSOC);

$recommendations = [];
if (isset($_POST['generate'])) {
    // Find genres of liked books
    $genres = array_unique(array_column($likedBooks, 'genre'));

    // Create a string of genre placeholders separated by commas for the SQL IN() clause
    $inQuery = implode(',', array_fill(0, count($genres), '?'));

    // Query for random books from the same genres
    $recStmt = $db->prepare("
        SELECT ISBN, title, genre FROM Books 
        WHERE genre IN ($inQuery) AND
        ISBN NOT IN (SELECT ISBN FROM Has_Read WHERE user_id = ?)
        ORDER BY RAND()
        LIMIT 5
    ");
    
    $recStmt->execute(array_merge($genres, [$userId]));
    $recommendations = $recStmt->fetchAll(PDO::FETCH_ASSOC);
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
    // Find genres of liked books
    $genres = array_unique(array_column($likedBooks, 'genre'));

    // Create a string of genre placeholders separated by commas for the SQL IN() clause
    $inQuery = implode(',', array_fill(0, count($genres), '?'));

    // Query for random books from the same genres
    $recStmt = $db->prepare("
        SELECT ISBN, title, genre FROM Books 
        WHERE genre IN ($inQuery) AND
        ISBN NOT IN (SELECT ISBN FROM Has_Read WHERE user_id = ?)
        ORDER BY RAND()
        LIMIT 5
    ");
    
    $recStmt->execute(array_merge($genres, [$userId]));
    $recommendations = $recStmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="profile.css">
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
    <h1>User Profile: <?= htmlspecialchars($user['username']) ?></h1>
    <h3 style="font-family:georgia">Your Liked Books</h3>
    <?php if (count($likedBooks) > 0): ?>
        <ul>
            <?php foreach ($likedBooks as $book): ?>
                <div class="card mb-2 bg-light rounded" style="border-radius:15px">
                    <div class="card-body">
                        <strong><?= htmlspecialchars($book['title']) ?></strong> (<?= htmlspecialchars($book['genre']) ?>) - Published: <?= htmlspecialchars($book['publication_date']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </ul>
        <form method="post">
            <button type="submit" class="btn btn-primary" name="export_liked" style="border-radius: 10px">Export Liked Books</button>   
        </form>
    <?php else: ?>
        <p>You have not liked any books yet.</p>
    <?php endif; ?>

    <br></br>
    <form method="post">
        <button type="submit" name="generate" class="btn btn-primary" style="border-radius: 10px">Generate Recommendation List</button>
    </form>

    <?php if (count($recommendations) > 0): ?>
        <h3 style="font-family:georgia">Recommended Books:</h3>
        <ul>
            <?php foreach ($recommendations as $book): ?>
                <div class="card mb-2 bg-light rounded" style="border-radius:15px">
                    <div class="card-body">
                        <strong><?= htmlspecialchars($book['title']) ?> - <?= htmlspecialchars($book['genre'])?><strong>
                        <a href="book_detail.php?ISBN=<?= htmlspecialchars($book['ISBN']) ?>" class="btn btn-outline-primary">Check out this book</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </ul>
        <form method="post">
            <button type="submit" name="export_recommended" style="border-radius: 10px">Export Recommended Books</button>
        </form>
    <?php endif; ?>

    <form action="profile.php" method="post" enctype="multipart/form-data">
        <label>Upload CSV of Books to Like:</label>
        <input style="border-radius: 10px" type="file" name="file" required>
        <label>Import books you like:</label>
        <button type="submit" name="import" style="border-radius: 10px">Import Liked Books</button>
    </form>

</body>
</html>
