<?php
// Include database connection
require_once 'config/database.php';

/**
 * Get all books with optional filtering
 * 
 * @param string $status Filter by book status (available, borrowed, or all)
 * @param string $search Search term for title or author
 * @return array Array of books
 */
function getBooks($status = 'all', $search = '') {
    global $pdo;
    
    $sql = "SELECT * FROM books WHERE 1=1";
    
    if ($status !== 'all') {
        $sql .= " AND status = :status";
    }
    
    if (!empty($search)) {
        $sql .= " AND (title LIKE :search OR author LIKE :search)";
    }
    
    $sql .= " ORDER BY title ASC";
    
    $stmt = $pdo->prepare($sql);
    
    if ($status !== 'all') {
        $stmt->bindValue(':status', $status);
    }
    
    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%");
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get book by ID
 * 
 * @param int $id Book ID
 * @return array|bool Book data or false if not found
 */
function getBookById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Delete a book
 * 
 * @param int $id Book ID
 * @return bool True if successful, false otherwise
 */
function deleteBook($id) {
    global $pdo;
    
    try {
        // Check if book is borrowed
        $book = getBookById($id);
        if (!$book || $book['status'] === 'borrowed') {
            return false;
        }
        
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id AND status = 'available'");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Check if student exists, create if not
 * 
 * @param string $studentId Student ID
 * @param string $name Student name (optional, for new students)
 * @param string $email Student email (optional, for new students)
 * @return array|bool Student data or false if error
 */
function getOrCreateStudent($studentId, $name = '', $email = '') {
    global $pdo;
    
    // Check if student exists
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
    $stmt->bindParam(':id', $studentId);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If student doesn't exist and name/email provided, create new student
    if (!$student && !empty($name) && !empty($email)) {
        $stmt = $pdo->prepare("INSERT INTO students (id, name, email) VALUES (:id, :name, :email)");
        $stmt->bindParam(':id', $studentId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        
        if ($stmt->execute()) {
            // Get the newly created student
            return getOrCreateStudent($studentId);
        } else {
            return false;
        }
    }
    
    return $student;
}

/**
 * Borrow a book
 * 
 * @param int $bookId Book ID
 * @param string $studentId Student ID
 * @param int $daysToReturn Number of days until return is due
 * @return bool True if successful, false otherwise
 */
function borrowBook($bookId, $studentId, $daysToReturn = 14) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Check if book is available
        $book = getBookById($bookId);
        if (!$book || $book['status'] !== 'available') {
            return false;
        }
        
        // Check if student exists
        $student = getOrCreateStudent($studentId);
        if (!$student) {
            return false;
        }
        
        // Update book status
        $stmt = $pdo->prepare("UPDATE books SET status = 'borrowed' WHERE id = :id");
        $stmt->bindParam(':id', $bookId);
        $stmt->execute();
        
        // Calculate due date
        $dueDate = date('Y-m-d H:i:s', strtotime("+$daysToReturn days"));
        
        // Create transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (book_id, student_id, due_date) VALUES (:book_id, :student_id, :due_date)");
        $stmt->bindParam(':book_id', $bookId);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':due_date', $dueDate);
        $stmt->execute();
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

/**
 * Return a book
 * 
 * @param int $transactionId Transaction ID
 * @return bool True if successful, false otherwise
 */
function returnBook($transactionId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get transaction details
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = :id AND status = 'borrowed'");
        $stmt->bindParam(':id', $transactionId);
        $stmt->execute();
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$transaction) {
            return false;
        }
        
        // Update book status
        $stmt = $pdo->prepare("UPDATE books SET status = 'available' WHERE id = :id");
        $stmt->bindParam(':id', $transaction['book_id']);
        $stmt->execute();
        
        // Update transaction
        $stmt = $pdo->prepare("UPDATE transactions SET return_date = CURRENT_TIMESTAMP, status = 'returned' WHERE id = :id");
        $stmt->bindParam(':id', $transactionId);
        $stmt->execute();
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

/**
 * Get borrowed books by student ID
 * 
 * @param string $studentId Student ID
 * @return array Array of borrowed books
 */
function getBorrowedBooks($studentId) {
    global $pdo;
    
    $sql = "SELECT t.id as transaction_id, b.id as book_id, b.title, b.author, b.isbn, 
            t.borrow_date, t.due_date, t.status 
            FROM transactions t 
            JOIN books b ON t.book_id = b.id 
            WHERE t.student_id = :student_id AND t.status = 'borrowed'
            ORDER BY t.borrow_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':student_id', $studentId);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get transaction history for a student
 * 
 * @param string $studentId Student ID
 * @return array Array of transactions
 */
function getTransactionHistory($studentId) {
    global $pdo;
    
    $sql = "SELECT t.id as transaction_id, b.id as book_id, b.title, b.author, 
            t.borrow_date, t.due_date, t.return_date, t.status 
            FROM transactions t 
            JOIN books b ON t.book_id = b.id 
            WHERE t.student_id = :student_id
            ORDER BY t.borrow_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':student_id', $studentId);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Add a new book
 * 
 * @param array $bookData Book data
 * @return bool True if successful, false otherwise
 */
function addBook($bookData) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn, category, publication_year, cover_image) 
                               VALUES (:title, :author, :isbn, :category, :publication_year, :cover_image)");
        
        $stmt->bindParam(':title', $bookData['title']);
        $stmt->bindParam(':author', $bookData['author']);
        $stmt->bindParam(':isbn', $bookData['isbn']);
        $stmt->bindParam(':category', $bookData['category']);
        $stmt->bindParam(':publication_year', $bookData['publication_year']);
        $stmt->bindParam(':cover_image', $bookData['cover_image']);
        
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Format date nicely
 * 
 * @param string $date Date string
 * @return string Formatted date
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}
?>