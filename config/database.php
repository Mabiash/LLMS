<?php
// Database configuration
$host = 'localhost';
$dbname = 'library_management';
$username = 'root';
$password = '';

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
    // Create tables if they don't exist
    
    // Books table
    $pdo->exec("CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        isbn VARCHAR(20) NOT NULL,
        category VARCHAR(100) NOT NULL,
        publication_year INT NOT NULL,
        status ENUM('available', 'borrowed') NOT NULL DEFAULT 'available',
        cover_image VARCHAR(255) DEFAULT 'default-book.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Students table
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id VARCHAR(20) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Transactions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        book_id INT NOT NULL,
        student_id VARCHAR(20) NOT NULL,
        borrow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        due_date TIMESTAMP NULL,
        return_date TIMESTAMP NULL,
        status ENUM('borrowed', 'returned') NOT NULL DEFAULT 'borrowed',
        FOREIGN KEY (book_id) REFERENCES books(id),
        FOREIGN KEY (student_id) REFERENCES students(id)
    )");
    
    // Insert sample data if books table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM books");
    $bookCount = $stmt->fetchColumn();
    
    if ($bookCount == 0) {
        // Sample books
        $pdo->exec("INSERT INTO books (title, author, isbn, category, publication_year, status, cover_image) VALUES
            ('The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 'Fiction', 1925, 'available', 'great-gatsby.jpg'),
            ('To Kill a Mockingbird', 'Harper Lee', '9780060935467', 'Fiction', 1960, 'available', 'to-kill-mockingbird.jpg'),
            ('1984', 'George Orwell', '9780451524935', 'Science Fiction', 1949, 'available', '1984.jpg'),
            ('Pride and Prejudice', 'Jane Austen', '9780141439518', 'Classic', 1813, 'available', 'pride-prejudice.jpg'),
            ('The Hobbit', 'J.R.R. Tolkien', '9780547928227', 'Fantasy', 1937, 'available', 'the-hobbit.jpg'),
            ('Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', '9780747532743', 'Fantasy', 1997, 'available', 'harry-potter.jpg'),
            ('The Catcher in the Rye', 'J.D. Salinger', '9780316769488', 'Fiction', 1951, 'available', 'catcher-rye.jpg'),
            ('The Lord of the Rings', 'J.R.R. Tolkien', '9780618640157', 'Fantasy', 1954, 'available', 'lord-rings.jpg'),
            ('Animal Farm', 'George Orwell', '9780451526342', 'Fiction', 1945, 'available', 'animal-farm.jpg'),
            ('The Alchemist', 'Paulo Coelho', '9780062315007', 'Fiction', 1988, 'available', 'the-alchemist.jpg')
        ");
        
        // Sample students
        $pdo->exec("INSERT INTO students (id, name, email) VALUES
            ('S001', 'John Doe', 'john.doe@example.com'),
            ('S002', 'Jane Smith', 'jane.smith@example.com'),
            ('S003', 'Michael Johnson', 'michael.johnson@example.com')
        ");
    }
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>