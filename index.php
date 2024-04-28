<?php
include('connect-db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// User's dashboard or homepage content goes here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to the Book Recommendation System</title>
</head>
<body>
    <h1>Welcome to Our Book Recommendation System</h1>
    <p><a href="search_books.php">Search Books</a></p>
    <p><a href="add_book.php">Add New Book</a></p>
    <p><a href="update_profile.php">Update Profile</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>