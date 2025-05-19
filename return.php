<?php
require_once 'includes/functions.php';
include 'includes/header.php';

// Initialize variables
$success = false;
$error = '';
$student_id = '';
$borrowed_books = [];

// Handle form submission for student search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_student'])) {
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    
    if (empty($student_id)) {
        $error = 'Please enter your Student ID.';
    } else {
        // Get student's borrowed books
        $borrowed_books = getBorrowedBooks($student_id);
        
        if (empty($borrowed_books)) {
            $error = 'No borrowed books found for this Student ID.';
        }
    }
}

// Handle form submission for book return
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_book'])) {
    $transaction_id = isset($_POST['transaction_id']) ? (int)$_POST['transaction_id'] : 0;
    
    if (empty($transaction_id)) {
        $error = 'Invalid transaction.';
    } else {
        // Process book return
        if (returnBook($transaction_id)) {
            $success = true;
            $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
            $borrowed_books = getBorrowedBooks($student_id);
        } else {
            $error = 'Failed to process return. Please try again.';
        }
    }
}
?>

<div class="page-header">
    <h1 class="page-title">Return a Book</h1>
</div>

<?php if ($success): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> Book has been successfully returned! <a href="index.php">Return to book catalog</a>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="return.php">
        <div class="form-group">
            <label for="student_id">Enter Your Student ID</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
        </div>
        
        <button type="submit" name="search_student" class="btn btn-block">
            <i class="fas fa-search"></i> Find My Books
        </button>
    </form>
</div>

<?php if (!empty($borrowed_books)): ?>
<div class="mt-3">
    <h2>Your Borrowed Books</h2>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowed_books as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo formatDate($book['borrow_date']); ?></td>
                    <td><?php echo formatDate($book['due_date']); ?></td>
                    <td>
                        <form method="POST" action="return.php" class="mb-0">
                            <input type="hidden" name="transaction_id" value="<?php echo $book['transaction_id']; ?>">
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                            <button type="submit" name="return_book" class="btn btn-warning">
                                <i class="fas fa-undo"></i> Return
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>