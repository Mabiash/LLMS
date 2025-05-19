<?php
require_once 'includes/functions.php';

// Check if book ID is provided
$book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $book_id > 0) {
    if (deleteBook($book_id)) {
        header('Location: index.php?message=deleted');
        exit;
    }
}

// If we get here, something went wrong
header('Location: index.php?error=delete_failed');
exit;
?>