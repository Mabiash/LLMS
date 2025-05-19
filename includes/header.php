<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <i class="fas fa-book-open"></i>
                    <span>Campus Library</span>
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="borrow.php"><i class="fas fa-hand-holding"></i> Borrow</a></li>
                    <li><a href="return.php"><i class="fas fa-undo"></i> Return</a></li>
                    <li><a href="history.php"><i class="fas fa-history"></i> History</a></li>
                    <li><a href="add_book.php"><i class="fas fa-plus-circle"></i> Add Book</a></li>
                </ul>
            </nav>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
    <main>
        <div class="container">