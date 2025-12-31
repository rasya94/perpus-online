<?php
require_once 'functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// di call di book_details.php
$bookId = $_POST['book_id'] ?? '';
$bookTitle = $_POST['book_title'] ?? '';
$bookAuthor = $_POST['book_author'] ?? '';
$bookCover = $_POST['book_cover'] ?? '';

if (empty($bookId) || empty($bookTitle)) {
    $_SESSION['message'] = 'Invalid book data.';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit();
}

$result = borrowBook($_SESSION['user_id'], $bookId, $bookTitle, $bookAuthor, $bookCover);

$_SESSION['message'] = $result['message'];
$_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

header('Location: book_detail.php?id=' . urlencode($bookId));
exit();
?>