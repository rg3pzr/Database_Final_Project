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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
      <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Lolita_1955.JPG/440px-Lolita_1955.JPG" alt="Book 1">
      <p>Lolita</p>
      <span>⭐⭐⭐⭐⭐</span>
      <a href="/book_detail.php?ISBN=A9780001000391" class="btn btn-outline-primary">Check out this book</a>
    </article>
    
    <article class="book">
      <img src="https://upload.wikimedia.org/wikipedia/en/3/39/The_Hunger_Games_cover.jpg" alt="Book 2">
      <p>The Hunger Games</p>
      <span>⭐⭐⭐⭐</span>
      <a href="/book_detail.php?ISBN=A9780439023481" class="btn btn-outline-primary">Check out this book</a>
    </article>
    
    <article class="book">
      <img src="https://upload.wikimedia.org/wikipedia/commons/8/8a/The_Prophet_%28Gibran%29.jpg" alt="Book 3">
      <p>The Prophet</p>
      <span>⭐⭐⭐⭐⭐</span>
      <a href="/book_detail.php?ISBN=A9780001000391" class="btn btn-outline-primary">Check out this book</a>
    </article>
  </section>
</main>

</body>
</html>
