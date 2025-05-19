<?php
require_once 'includes/functions.php';
include 'includes/header.php';

// Initialize variables
$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $isbn = isset($_POST['isbn']) ? trim($_POST['isbn']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $publication_year = isset($_POST['publication_year']) ? (int)$_POST['publication_year'] : '';
    $cover_image = 'default-book.jpg'; // Default cover image
    
    // Validate input
    if (empty($title)) {
        $error = 'Book title is required.';
    } elseif (empty($author)) {
        $error = 'Author name is required.';
    } elseif (empty($isbn)) {
        $error = 'ISBN is required.';
    } elseif (empty($category)) {
        $error = 'Category is required.';
    } elseif (empty($publication_year) || $publication_year < 1000 || $publication_year > date('Y')) {
        $error = 'Valid publication year is required.';
    } else {
        // Process file upload if present
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'assets/images/';
            $temp_name = $_FILES['cover_image']['tmp_name'];
            $file_name = basename($_FILES['cover_image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Generate a unique filename
            $new_file_name = 'book_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;
            
            // Check file type
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_ext, $allowed_exts)) {
                // Move uploaded file
                if (move_uploaded_file($temp_name, $upload_path)) {
                    $cover_image = $new_file_name;
                }
            }
        }
        
        // Add book to database
        $book_data = [
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'category' => $category,
            'publication_year' => $publication_year,
            'cover_image' => $cover_image
        ];
        
        if (addBook($book_data)) {
            $success = true;
        } else {
            $error = 'Failed to add book. Please try again.';
        }
    }
}

// Get categories for dropdown
$categories = [
    'Fiction', 'Non-Fiction', 'Science Fiction', 'Fantasy', 'Mystery', 
    'Thriller', 'Romance', 'Biography', 'History', 'Science', 
    'Technology', 'Self-Help', 'Reference', 'Children', 'Young Adult',
    'Classic', 'Poetry', 'Drama', 'Horror', 'Adventure'
];
sort($categories);
?>

<div class="page-header">
    <h1 class="page-title">Add New Book</h1>
</div>

<?php if ($success): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> Book has been successfully added! <a href="index.php">View book catalog</a>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="add_book.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Book Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" id="author" name="author" required>
        </div>
        
        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" required>
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <option value="">-- Select a category --</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="publication_year">Publication Year</label>
            <input type="number" id="publication_year" name="publication_year" min="1000" max="<?php echo date('Y'); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="cover_image">Cover Image (Optional)</label>
            <input type="file" id="cover_image" name="cover_image" accept="image/*">
            <img id="cover-preview" src="" alt="Cover Preview" style="display: none; max-width: 100%; margin-top: 10px;">
        </div>
        
        <button type="submit" class="btn btn-success btn-block">
            <i class="fas fa-plus-circle"></i> Add Book
        </button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>