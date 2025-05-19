<?php
require_once 'includes/functions.php';
include 'includes/header.php';

// Initialize variables
$success = false;
$error = '';
$book = null;
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;

// Get book if ID is provided
if ($book_id) {
    $book = getBookById($book_id);
    if (!$book || $book['status'] !== 'available') {
        $error = 'Book is not available for borrowing.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $student_name = isset($_POST['student_name']) ? trim($_POST['student_name']) : '';
    $student_email = isset($_POST['student_email']) ? trim($_POST['student_email']) : '';
    
    // Validate input
    if (empty($student_id)) {
        $error = 'Student ID is required.';
    } elseif (empty($book_id)) {
        $error = 'Please select a book to borrow.';
    } else {
        // Check if book exists and is available
        $book = getBookById($book_id);
        if (!$book) {
            $error = 'Book not found.';
        } elseif ($book['status'] !== 'available') {
            $error = 'This book is not available for borrowing.';
        } else {
            // Create or get student
            $student = getOrCreateStudent($student_id, $student_name, $student_email);
            
            if (!$student) {
                $error = 'Failed to create or retrieve student record.';
            } else {
                // Process borrowing
                if (borrowBook($book_id, $student_id)) {
                    $success = true;
                } else {
                    $error = 'Failed to process borrowing. Please try again.';
                }
            }
        }
    }
}

// Get all available books for dropdown
$available_books = getBooks('available');
?>

<div class="page-header">
    <h1 class="page-title">Borrow a Book</h1>
</div>

<?php if ($success): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> Book has been successfully borrowed! <a href="index.php">Return to book catalog</a>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="borrow.php">
        <div class="form-group">
            <label for="student_id">Student ID</label>
            <input type="text" id="student_id" name="student_id" required>
        </div>
        
        <div class="form-group">
            <label for="student_name">Your Name (For new students only)</label>
            <input type="text" id="student_name" name="student_name">
        </div>
        
        <div class="form-group">
            <label for="student_email">Your Email (For new students only)</label>
            <input type="email" id="student_email" name="student_email">
        </div>
        
        <div class="form-group">
            <label for="book_id">Select a Book</label>
            <select id="book_id" name="book_id" required>
                <option value="">-- Select a book --</option>
                <?php if ($book && $book['status'] === 'available'): ?>
                <option value="<?php echo $book['id']; ?>" selected>
                    <?php echo htmlspecialchars($book['title']); ?> by <?php echo htmlspecialchars($book['author']); ?>
                </option>
                <?php endif; ?>
                
                <?php foreach ($available_books as $available_book): ?>
                    <?php if (!$book || $book['id'] !== $available_book['id']): ?>
                    <option value="<?php echo $available_book['id']; ?>">
                        <?php echo htmlspecialchars($available_book['title']); ?> by <?php echo htmlspecialchars($available_book['author']); ?>
                    </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-success btn-block">
            <i class="fas fa-hand-holding"></i> Borrow Book
        </button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>