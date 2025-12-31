<?php
require_once 'functions.php';

$searchResults = [];
$searchQuery = '';

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searchQuery = trim($_GET['q']);
    $searchResults = searchBooks($searchQuery);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <!-- panel kiri, (my_books sama profile cuma keliatan kalau udh login) -->
        <aside class="left-panel">
            <h1>Perpus</h1>

            <div class="divider" style="border-top: 0px solid #d8d8d8;"></div>

            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php" class="active">Search</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="my_books.php">My Books</a></li>
                        <li><a href="profile.php">Profile</a></li>
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
        <!-- --- -->

        <main class="main-content">
            <div class="page-header">
                <h1>Search Books</h1>
            </div>

            <div class="search-form">
                <form method="GET" action="index.php">
                    <input type="text" name="q" class="search-input" placeholder="Search by title, author, or ISBN."
                        value="<?php echo htmlspecialchars($searchQuery); ?>" required>
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>

            <?php if (!empty($searchQuery)): ?>
                <div class="search-results">
                    <h2>Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
                    <p><?php echo htmlspecialchars($searchResults['numFound']); ?> Found.</p>

                    <?php if (empty($searchResults['docs'])): ?>
                        <p>No books found, try a different query.</p>
                    <?php else: ?>
                        <div class="book-grid">
                            <?php foreach (array_slice($searchResults['docs'], 0, 24) as $book): ?>
                                <?php
                                $bookKey = $book['key'] ?? '';
                                $title = $book['title'] ?? 'Unknown Title';
                                $author = $book['author_name'][0] ?? 'Unknown Author';
                                $coverId = $book['cover_i'] ?? null;
                                $coverUrl = getCoverUrl($coverId, 'M');
                                ?>
                                <div class="book-item"
                                    onclick="window.location.href='book_detail.php?id=<?php echo urlencode($bookKey); ?>'">
                                    <img src="<?php echo htmlspecialchars($coverUrl); ?>"
                                        alt="<?php echo htmlspecialchars($title); ?>" class="book-cover">

                                    <div class="book-title">
                                        <?php echo htmlspecialchars(substr($title, 0, 60)); ?>            <?php echo strlen($title) > 60 ? '...' : ''; ?>
                                    </div>
                                    <div class="book-author"><?php echo htmlspecialchars($author); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="welcome-message">
                    <h2>Welcome to Perpus</h2>
                    <p>Search for books using the search bar above. You can search by title, author, or ISBN.</p>
                    <!-- prompt login -->
                    <?php if (!isLoggedIn()): ?>
                        <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to borrow books.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>