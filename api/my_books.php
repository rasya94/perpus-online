<?php
require_once 'functions.php';
requireLogin();

$borrowedBooks = getUserBorrowedBooks($_SESSION['user_id']);
$today = date('Y-m-d');

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
    <title>My Books</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <aside class="left-panel">
            <h1>Perpus</h1>
            <div class="divider" style="border-top: 0px solid #d8d8d8;"></div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php">Search</a></li>
                    <li><a href="my_books.php" class="active">My Books</a></li>
                    <li><a href="profile.php">Profile</a></li>
                </ul>
            </nav>

            <div class="user-info">
                <p><strong>Logged in as:</strong><br><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>My Borrowed Books</h1>
                <p>You have borrowed <?php echo count($borrowedBooks); ?> / <?php echo BORROW_LIMIT; ?> books</p>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($borrowedBooks)): ?>
                <p>You don't have any borrowed books. <a href="index.php">Search for books to borrow.</a></p>
            <?php else: ?>
                <div class="borrowed-books-list">
                    <?php foreach ($borrowedBooks as $book): ?>
                        <?php
                        $isExpired = $book['expire_date'] < $today;
                        $daysLeft = ceil((strtotime($book['expire_date']) - strtotime($today)) / 86400);
                        ?>
                        <div class="borrowed-book">
                            <img src="<?php echo htmlspecialchars($book['book_cover']); ?>" alt="<?php echo htmlspecialchars($book['book_title']); ?>">

                            <div class="borrowed-book-info">
                                <h3><?php echo htmlspecialchars($book['book_title']); ?></h3>
                                <p><strong>Author:</strong> <?php echo htmlspecialchars($book['book_author']); ?></p>
                                <div class="dates">
                                    <p><strong>Borrowed on:</strong>
                                        <?php echo date('F j, Y', strtotime($book['borrow_date'])); ?></p>
                                    <p><strong>Return date:</strong> <?php echo date('F j, Y', strtotime($book['expire_date'])); ?>
                                    </p>
                                    <?php if ($isExpired): ?>
                                        <p class="expired">⚠ This book is overdue!</p>
                                    <?php else: ?>
                                        <p><?php echo $daysLeft; ?> days remaining</p>
                                    <?php endif; ?>
                                </div>
                                <form method="POST" action="return_book.php" style="margin-top: 15px;">
                                    <input type="hidden" name="borrow_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="btn btn-secondary"
                                        onclick="return confirm('Are you sure you want to return this book?');">Return Book</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>