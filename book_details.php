<?php
require_once 'includes/functions.php';

// Check if it's an AJAX request
$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

if (!$is_ajax) {
    include 'includes/header.php';
}

// Get book ID
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book = getBookById($book_id);

if (!$book) {
    echo '<div class="alert alert-error">Book not found.</div>';
    if (!$is_ajax) {
        include 'includes/footer.php';
    }
    exit;
}
?>

<div class="book-details">
    <div class="book-details-cover">
        <img src="assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" 
             alt="<?php echo htmlspecialchars($book['title']); ?>" 
             onerror="this.src='https://images.pexels.com/photos/1290141/pexels-photo-1290141.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'">
    </div>
    <div class="book-details-info">
        <h1 class="book-details-title"><?php echo htmlspecialchars($book['title']); ?></h1>
        <p class="book-details-author">by <?php echo htmlspecialchars($book['author']); ?></p>
        
        <div class="book-details-meta">
            <div class="meta-item">
                <span class="meta-label">ISBN</span>
                <span class="meta-value"><?php echo htmlspecialchars($book['isbn']); ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Category</span>
                <span class="meta-value"><?php echo htmlspecialchars($book['category']); ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Year</span>
                <span class="meta-value"><?php echo $book['publication_year']; ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Status</span>
                <span class="meta-value">
                    <?php if ($book['status'] === 'available'): ?>
                    <span class="status-available">Available</span>
                    <?php else: ?>
                    <span class="status-borrowed">Borrowed</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        
        <div class="mt-3">
            <?php if ($book['status'] === 'available'): ?>
            <a href="borrow.php?book_id=<?php echo $book['id']; ?>" class="btn btn-success">
                <i class="fas fa-hand-holding"></i> Borrow this Book
            </a>
            <?php else: ?>
            <button class="btn btn-secondary" disabled>
                <i class="fas fa-clock"></i> Currently Unavailable
            </button>
            <?php endif; ?>
            
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Catalog
            </a>
        </div>
    </div>
</div>

<?php
if (!$is_ajax) {
    include 'includes/footer.php';
}
?>