<?php
include('connect-db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$search_results = array();

// Process user input
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];

    // Construct SQL query
    $sql = "SELECT * FROM Books WHERE title LIKE '%$search_term%'";

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
      <li><a href="signin.php">Sign in</a></li>
      <li><a href="signup.php">Sign up</a></li>
    </ul>
  </nav>
</header>

<h2>Search Results</h2>

<?php if (!empty($search_results)) : ?>
    <?php foreach ($search_results as $book) : ?>
        <p>Book Name: <?php echo $book['title']; ?></p>
        <!-- Add more fields to display other book information as needed -->
    <?php endforeach; ?>
<?php else : ?>
    <p>No results found</p>
<?php endif; ?>

</body>
</html>
