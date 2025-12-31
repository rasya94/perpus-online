<?php
require_once 'functions.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$bookKey = $_GET['id'];
$bookData = getBookDetails($bookKey);

if (!$bookData) {
    $error = "Book not found or unable to fetch details.";
}

$searchUrl = 'https://openlibrary.org/search.json?q=' . urlencode($bookKey);
$searchResponse = @file_get_contents($searchUrl);
$searchData = $searchResponse ? json_decode($searchResponse, true) : null;
$bookFromSearch = $searchData['docs'][0] ?? null;

$title = $bookData['title'] ?? $bookFromSearch['title'] ?? 'Unknown Title';
$description = $bookData['description'] ?? '';
if (is_array($description)) {
    $description = $description['value'] ?? '';
}

$author = 'Unknown Author';
if (isset($bookFromSearch['author_name'])) {
    $author = implode(', ', $bookFromSearch['author_name']);
} elseif (isset($bookData['authors'])) {
    $authorKeys = array_column($bookData['authors'], 'author', 'key');
    $author = implode(', ', array_keys($authorKeys));
}

$coverId = $bookFromSearch['cover_i'] ?? ($bookData['covers'][0] ?? null);
$coverUrl = getCoverUrl($coverId, 'L');

$isbn = isset($bookFromSearch['isbn']) ? $bookFromSearch['isbn'][0] : 'N/A';
$publishYear = $bookFromSearch['first_publish_year'] ?? 'N/A';

$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message'], $_SESSION['message_type']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <aside class="left-panel">
            <h1>Perpus</h1>

            <div class="divider" style="border-top: 0px solid #d8d8d8;"></div>

            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php" class="active">Search</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="my_books.php">My Books</a></li>
                        <li><a href="profile.php">My Profile</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <?php if (isLoggedIn()): ?>
                <div class="user-info">
                    <p><strong>Logged in as:</strong><br><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </div>
            <?php else: ?>
                <div class="user-info">
                    <p><a href="login.php">Login</a> or <a href="register.php">Register</a></p>
                </div>
            <?php endif; ?>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>Book Details</h1>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
                <p><a href="index.php">Back to Search</a></p>
            <?php else: ?>
                <div class="book-detail">
                    <div class="book-detail-cover">
                        <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="<?php echo htmlspecialchars($title); ?>">

                        <div class="meta">
                            <p><strong>ISBN:</strong> <?php echo htmlspecialchars($isbn); ?></p>
                            <p><strong>First Published:</strong> <?php echo htmlspecialchars($publishYear); ?></p>
                            <p><strong>OpenLibrary ID:</strong> <?php echo htmlspecialchars($bookKey); ?></p>
                        </div>
                    </div>
                    
                    <div class="book-detail-info">
                        <h1><?php echo htmlspecialchars($title); ?></h1>
                        <p class="author">by <?php echo htmlspecialchars($author); ?></p>
                        
                        <?php if ($description): ?>
                            <div class="description">
                                <h3>Description</h3>
                                <p><?php echo nl2br(htmlspecialchars(substr($description, 0, 1000))); ?><?php echo strlen($description) > 1000 ? '...' : ''; ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isLoggedIn()): ?>
                            <?php if (hasUserBorrowedBook($_SESSION['user_id'], $bookKey)): ?>
                                <div class="message info">You have already borrowed this book.</div>
                            <?php elseif (getUserBorrowedCount($_SESSION['user_id']) >= BORROW_LIMIT): ?>
                                <div class="message info">You have reached the maximum borrow (<?php echo BORROW_LIMIT; ?> books)</div>
                            <?php else: ?>
                                <form method="POST" action="borrow.php">
                                    <!-- hidden input buat borrow.php jalan -->
                                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($bookKey); ?>">
                                    <input type="hidden" name="book_title" value="<?php echo htmlspecialchars($title); ?>">
                                    <input type="hidden" name="book_author" value="<?php echo htmlspecialchars($author); ?>">
                                    <input type="hidden" name="book_cover" value="<?php echo htmlspecialchars($coverUrl); ?>">
                                    <button type="submit" class="btn" style=" background-color: #2c9b6b;">Borrow This Book</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="message info">
                                Please <a href="login.php">login</a> or <a href="register.php">register</a> to borrow this book.
                            </div>
                        <?php endif; ?>
                        
                        <p style="margin-top: 20px;"><a href="index.php">Back to Search</a></p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>