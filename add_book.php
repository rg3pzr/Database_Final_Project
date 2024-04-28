<?php
include('connect-db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];

    $stmt = $pdo->prepare("INSERT INTO books (title, author, genre) VALUES (?, ?, ?)");
    $stmt->execute([$title, $author, $genre]);

    echo "Book added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book</title>
</head>
<body>
    <form method="post">
        Title: <input type="text" name="title"><br>
        Author: <input type="text" name="author"><br>
        Genre: <input type="text" name="genre"><br>
        <button type="submit">Add Book</button>
    </form>
</body>
</html>
