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
      <li><a href="signin.php">Sign in</a></li>
      <li><a href="signup.php">Sign up</a></li>
    </ul>
  </nav>
</header>

<main>
    <h2> Search Results </h2>
    <?php
    // Process user input
    $servername = "mysql01.cs.virginia.edu";
    $username = "rg3pzr";
    $password = "dbpass";
    $dbname = "jt8ab_b";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if (isset($_GET['search'])) {
        $search_term = $_GET['search'];
        // Construct SQL query
        $sql = "SELECT * FROM Books WHERE title LIKE '%$search_term%'";

        // Execute SQL query
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Display results
            while($row = $result->fetch_assoc()) {
                echo "<p>Book Name: " . $row["title"]. "</p>";
                // Add more fields to display other book information as needed
            }
        } else {
            echo "<p>No results found</p>";
        }
    }
    ?>
</main>
</body>
</html>
