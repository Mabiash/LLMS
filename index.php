<?php
require_once 'includes/functions.php';
include 'includes/header.php';

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get books based on filters
$books = getBooks($status, $search);

// Handle messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<div class="page-header">
    <h1 class="page-title">Book Catalog</h1>
</div>

<?php if ($message === 'deleted'): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> Book has been successfully deleted.
</div>
<?php endif; ?>

<?php if ($error === 'delete_failed'): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> Could not delete the book. It might be currently borrowed.
</div>
<?php endif; ?>

<!-- Search Form -->
<form method="GET" action="index.php" class="search-form">
    <input type="text" name="search" id="book-search" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit" class="btn">Search</button>
</form>

<!-- Filters -->
<div class="filters">
    <div class="filter-item <?php echo $status === 'all' ? 'active' : ''; ?>" data-status="all">
        All Books
    </div>
    <div class="filter-item <?php echo $status === 'available' ? 'active' : ''; ?>" data-status="available">
        Available
    </div>
    <div class="filter-item <?php echo $status === 'borrowed' ? 'active' : ''; ?>" data-status="borrowed">
        Borrowed
    </div>
</div>

<?php if (empty($books)): ?>
<div class="alert alert-warning">
    No books found. <?php echo !empty($search) ? 'Try a different search term.' : ''; ?>
</div>
<?php else: ?>
<div class="book-grid">
    <?php foreach ($books as $book): ?>
    <div class="book-card">
        <div class="book-cover">
            <img src="assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" 
                 alt="<?php echo htmlspecialchars($book['title']); ?>" 
                 onerror="this.src='https://images.pexels.com/photos/1290141/pexels-photo-1290141.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'">
            <div class="book-status status-<?php echo $book['status']; ?>">
                <?php echo ucfirst($book['status']); ?>
            </div>
        </div>
        <div class="book-info">
            <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
            <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
            <div class="book-meta">
                <span class="book-category">
                    <i class="fas fa-bookmark"></i> 
                    <?php echo htmlspecialchars($book['category']); ?>
                </span>
                <span class="book-year">
                    <i class="fas fa-calendar"></i>
                    <?php echo $book['publication_year']; ?>
                </span>
            </div>
            <div class="mt-2 con">
                <?php if ($book['status'] === 'available'): ?>
                <div class="button-group">
                    <a href="borrow.php?book_id=<?php echo $book['id']; ?>" class="btn btn-success">
                        <i class="fas fa-hand-holding"></i> Borrow
                    </a>
                    <form method="POST" action="delete_book.php" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this book?');">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <button class="btn btn-secondary btn-block" disabled>
                    <i class="fas fa-clock"></i> Unavailable
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>