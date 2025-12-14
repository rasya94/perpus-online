<?php
require_once 'functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my_books.php');
    exit();
}

$borrowId = $_POST['borrow_id'] ?? 0;

if (returnBook($borrowId, $_SESSION['user_id'])) {
    $_SESSION['message'] = 'Book returned successfully!';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Failed to return book.';
    $_SESSION['message_type'] = 'error';
}

header('Location: my_books.php');
exit();
?>