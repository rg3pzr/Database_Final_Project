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
<html>
<head>
<title>Bookstore</title>
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
  
  <div class="search-box">
    <form method="GET" action="search_book.php">
      <input type="text" name="search" placeholder="What book would you like to search for?">
      <button type="submit">Search</button>
    </form>
  </div>
</header>

<main>
  <h2 class="heading" >Fan Favorites of the Week</h2>
  <section class="book-shelf">
    <article class="book">
      <img src="img/book1.png" alt="Book 1">
      <p>Book 1</p>
      <span>⭐⭐⭐⭐⭐</span>
    </article>
    
    <article class="book">
      <img src="img/book2.png" alt="Book 2">
      <p>Book 2</p>
      <span>⭐⭐⭐⭐</span>
    </article>
    
    <article class="book">
      <img src="img/book3.png" alt="Book 3">
      <p>Book 3</p>
      <span>⭐⭐⭐⭐⭐</span>
    </article>
  </section>
</main>

</body>
</html>
