// Wait for DOM content to be loaded
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('nav ul');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Student ID validation
    const studentIdInputs = document.querySelectorAll('input[name="student_id"]');
    studentIdInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            // Remove any non-alphanumeric characters
            this.value = this.value.replace(/[^A-Z0-9]/g, '');
        });
    });
    
    // Form validations
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            
            // Get all required fields
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    e.preventDefault();
                    hasErrors = true;
                    
                    // Add error class to parent form-group
                    const formGroup = field.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.add('has-error');
                        
                        // Add error message if it doesn't exist
                        let errorMsg = formGroup.querySelector('.error-message');
                        if (!errorMsg) {
                            errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            errorMsg.textContent = 'This field is required';
                            errorMsg.style.color = 'var(--error)';
                            errorMsg.style.fontSize = '0.875rem';
                            errorMsg.style.marginTop = '0.25rem';
                            formGroup.appendChild(errorMsg);
                        }
                    }
                } else {
                    // Remove error class and message
                    const formGroup = field.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.remove('has-error');
                        const errorMsg = formGroup.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                }
            });
            
            // ISBN validation for book form
            const isbnField = form.querySelector('input[name="isbn"]');
            if (isbnField && isbnField.value.trim()) {
                const isbn = isbnField.value.replace(/[^0-9X]/g, '');
                if (isbn.length !== 10 && isbn.length !== 13) {
                    e.preventDefault();
                    hasErrors = true;
                    
                    const formGroup = isbnField.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.add('has-error');
                        
                        let errorMsg = formGroup.querySelector('.error-message');
                        if (!errorMsg) {
                            errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            errorMsg.textContent = 'ISBN must be 10 or 13 characters';
                            errorMsg.style.color = 'var(--error)';
                            errorMsg.style.fontSize = '0.875rem';
                            errorMsg.style.marginTop = '0.25rem';
                            formGroup.appendChild(errorMsg);
                        } else {
                            errorMsg.textContent = 'ISBN must be 10 or 13 characters';
                        }
                    }
                }
            }
            
            // Publication year validation
            const yearField = form.querySelector('input[name="publication_year"]');
            if (yearField && yearField.value.trim()) {
                const year = parseInt(yearField.value, 10);
                const currentYear = new Date().getFullYear();
                
                if (isNaN(year) || year < 1000 || year > currentYear) {
                    e.preventDefault();
                    hasErrors = true;
                    
                    const formGroup = yearField.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.add('has-error');
                        
                        let errorMsg = formGroup.querySelector('.error-message');
                        if (!errorMsg) {
                            errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            errorMsg.textContent = `Year must be between 1000 and ${currentYear}`;
                            errorMsg.style.color = 'var(--error)';
                            errorMsg.style.fontSize = '0.875rem';
                            errorMsg.style.marginTop = '0.25rem';
                            formGroup.appendChild(errorMsg);
                        } else {
                            errorMsg.textContent = `Year must be between 1000 and ${currentYear}`;
                        }
                    }
                }
            }
            
            // Show form submission errors at the top
            if (hasErrors) {
                // Remove any existing error alerts
                const existingAlert = form.querySelector('.alert-error');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Create new error alert
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-error';
                errorAlert.textContent = 'Please fix the errors below before submitting.';
                
                // Insert at the top of the form
                form.prepend(errorAlert);
                
                // Scroll to the top of the form
                errorAlert.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            
            // Remove from DOM after fade out
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Book search functionality
    const searchInput = document.getElementById('book-search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const searchForm = this.closest('form');
            searchForm.submit();
        }, 500));
    }
    
    // Book filter tabs
    const filterItems = document.querySelectorAll('.filter-item');
    filterItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            filterItems.forEach(filter => filter.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Get filter value and redirect
            const status = this.getAttribute('data-status');
            window.location.href = `index.php?status=${status}`;
        });
    });
    
    // Preview image on file input change
    const coverImageInput = document.getElementById('cover_image');
    const coverPreview = document.getElementById('cover-preview');
    
    if (coverImageInput && coverPreview) {
        coverImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    coverPreview.src = e.target.result;
                    coverPreview.style.display = 'block';
                };
                
                reader.readAsDataURL(file);
            }
        });
    }
});

// Helper function for debouncing
function debounce(func, delay) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), delay);
    };
}

// Student history loading
function loadStudentHistory(studentId) {
    if (!studentId) return;
    
    // Get the history container
    const historyContainer = document.getElementById('student-history');
    if (!historyContainer) return;
    
    // Show loading state
    historyContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading history...</div>';
    
    // Make an AJAX request to get the history
    fetch(`history.php?student_id=${studentId}&ajax=1`)
        .then(response => response.text())
        .then(data => {
            historyContainer.innerHTML = data;
        })
        .catch(error => {
            historyContainer.innerHTML = '<div class="alert alert-error">Error loading history. Please try again.</div>';
        });
}

// Book details popup
function showBookDetails(bookId) {
    // Implement a modal popup for book details
    // This is a simplified version - in a real app, you'd fetch book details via AJAX
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="text-center">
                <i class="fas fa-spinner fa-spin"></i> Loading book details...
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add modal styles
    modal.style.display = 'block';
    modal.style.position = 'fixed';
    modal.style.zIndex = '1000';
    modal.style.left = '0';
    modal.style.top = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.overflow = 'auto';
    modal.style.backgroundColor = 'rgba(0,0,0,0.4)';
    
    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.backgroundColor = 'white';
    modalContent.style.margin = '10% auto';
    modalContent.style.padding = '20px';
    modalContent.style.border = '1px solid #888';
    modalContent.style.width = '80%';
    modalContent.style.maxWidth = '800px';
    modalContent.style.borderRadius = 'var(--radius-md)';
    modalContent.style.boxShadow = 'var(--shadow-lg)';
    
    const closeButton = modal.querySelector('.close');
    closeButton.style.color = '#aaa';
    closeButton.style.float = 'right';
    closeButton.style.fontSize = '28px';
    closeButton.style.fontWeight = 'bold';
    closeButton.style.cursor = 'pointer';
    
    // Close modal when clicking X
    closeButton.onclick = function() {
        document.body.removeChild(modal);
    };
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target === modal) {
            document.body.removeChild(modal);
        }
    };
    
    // Fetch book details via AJAX
    fetch(`book_details.php?id=${bookId}&ajax=1`)
        .then(response => response.text())
        .then(data => {
            modalContent.innerHTML = `<span class="close">&times;</span>${data}`;
            
            // Reattach close event
            const newCloseButton = modalContent.querySelector('.close');
            newCloseButton.style.color = '#aaa';
            newCloseButton.style.float = 'right';
            newCloseButton.style.fontSize = '28px';
            newCloseButton.style.fontWeight = 'bold';
            newCloseButton.style.cursor = 'pointer';
            
            newCloseButton.onclick = function() {
                document.body.removeChild(modal);
            };
        })
        .catch(error => {
            modalContent.innerHTML = `
                <span class="close">&times;</span>
                <div class="alert alert-error">Error loading book details. Please try again.</div>
            `;
            
            // Reattach close event
            const newCloseButton = modalContent.querySelector('.close');
            newCloseButton.onclick = function() {
                document.body.removeChild(modal);
            };
        });
}