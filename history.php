<?php
require_once 'includes/functions.php';

// Check if it's an AJAX request
$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

if (!$is_ajax) {
    include 'includes/header.php';
}

// Initialize variables
$student_id = isset($_GET['student_id']) ? trim($_GET['student_id']) : '';
$transactions = [];

// Get transaction history if student_id is provided
if (!empty($student_id)) {
    $transactions = getTransactionHistory($student_id);
}

// Handle AJAX request
if ($is_ajax) {
    if (empty($transactions)) {
        echo '<div class="alert alert-warning">No transaction history found for this Student ID.</div>';
    } else {
        // Output transaction table for AJAX
        include 'includes/transaction_table.php';
    }
    exit;
}
?>

<div class="page-header">
    <h1 class="page-title">Transaction History</h1>
</div>

<div class="form-container">
    <form method="GET" action="history.php">
        <div class="form-group">
            <label for="student_id">Enter Your Student ID</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
        </div>
        
        <button type="submit" class="btn btn-block">
            <i class="fas fa-search"></i> View History
        </button>
    </form>
</div>

<div class="mt-3" id="student-history">
    <?php if (!empty($student_id)): ?>
        <?php if (empty($transactions)): ?>
            <div class="alert alert-warning">No transaction history found for this Student ID.</div>
        <?php else: ?>
            <?php include 'includes/transaction_table.php'; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>