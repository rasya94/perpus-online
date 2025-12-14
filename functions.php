<?php
require_once 'config.php';

// Database MYSQLI
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    return $conn;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// CONTOH: /search.json?q=harry%20potter&fields=*,availability&limit=1
// TUTOR: https://openlibrary.org/search/howto
// OUTPUT API:
// {
//     "numFound": 2421,
//     "start": 0,
//     "numFoundExact": true,
//     "docs": [
//         {
//             "key": "/works/OL166894W",
//             "title": "Преступление и наказание",
//             "author_name": ["Фёдор Михайлович Достоевский"],
//             "editions": {
//                 "numFound": 290,
//                 "start": 0,
//                 "numFoundExact": true,
//                 "docs": [
//                     {
//                         "key": "/books/OL37239326M",
//                         "title": "Crime and Punishment"
//                     }
//                 ]
//             }
//         },
//     ...
function searchBooks($query) {
    $url = 'https://openlibrary.org/search.json?q=' . urlencode($query);
    $response = @file_get_contents($url);

    if ($response === false) {
        return [];
    }

    return json_decode($response, true);
}

// FORMAT API:
// {
//     "cover_i": 258027,
//     "has_fulltext": true,
//     "edition_count": 120,
//     "title": "The Lord of the Rings",
//     "author_name": [
//         "J. R. R. Tolkien"
//     ],
//     "first_publish_year": 1954,
//     "key": "OL27448W",
//     "ia": [
//         "returnofking00tolk_1",
//         "lordofrings00tolk_1",
//         "lordofrings00tolk_0",
//     ],
//     "author_key": [
//         "OL26320A"
//     ],
//     "public_scan_b": true
// }
function getBookDetails($bookId) {
    $url = 'https://openlibrary.org' . $bookId . '.json';
    $response = @file_get_contents($url);

    if ($response === false) {
        return null;
    }

    return json_decode($response, true);
}

function getCoverUrl($coverId, $size = 'M') {
    if (empty($coverId)) {
        return 'https://via.placeholder.com/200x300?text=No+Cover';
    }
    return "https://covers.openlibrary.org/b/id/{$coverId}-{$size}.jpg";
}

function getUserBorrowedCount($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM borrowed_books WHERE user_id = ? AND returned = 0"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count;
}

function hasUserBorrowedBook($userId, $bookId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM borrowed_books WHERE user_id = ? AND book_id = ? AND returned = 0"
    );
    $stmt->bind_param('is', $userId, $bookId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count > 0;
}

function borrowBook($userId, $bookId, $bookTitle, $bookAuthor, $bookCover) {
    $conn = getDBConnection();

    if (getUserBorrowedCount($userId) >= BORROW_LIMIT) {
        return [
            'success' => false,
            'message' => 'You have reached the maximum borrow limit of ' . BORROW_LIMIT . ' books.'
        ];
    }

    if (hasUserBorrowedBook($userId, $bookId)) {
        return [
            'success' => false,
            'message' => 'You have already borrowed this book.'
        ];
    }

    // format tanggal
    $borrowDate = date('Y-m-d');
    $expireDate = date('Y-m-d', strtotime('+' . BORROW_DAYS . ' days'));

    $stmt = $conn->prepare(
        "INSERT INTO borrowed_books 
        (user_id, book_id, book_title, book_author, book_cover, borrow_date, expire_date)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    // ???????
    $stmt->bind_param(
        'issssss',
        $userId,
        $bookId,
        $bookTitle,
        $bookAuthor,
        $bookCover,
        $borrowDate,
        $expireDate
    );

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Book borrowed successfully!'];
    }

    return ['success' => false, 'message' => 'Failed to borrow book.'];
}

function getUserBorrowedBooks($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare(
        "SELECT * FROM borrowed_books WHERE user_id = ? AND returned = 0 ORDER BY borrow_date DESC"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function returnBook($borrowId, $userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare(
        "UPDATE borrowed_books SET returned = 1 WHERE id = ? AND user_id = ?"
    );
    $stmt->bind_param('ii', $borrowId, $userId);
    return $stmt->execute();
}

function getUserInfo($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare(
        "SELECT id, username, email, created_at FROM users WHERE id = ?"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>
