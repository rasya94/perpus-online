<?php
require_once 'functions.php';
requireLogin();

$userInfo = getUserInfo($_SESSION['user_id']);
$borrowedCount = getUserBorrowedCount($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
                    <li><a href="my_books.php">My Books</a></li>
                    <li><a href="profile.php" class="active">Profile</a></li>
                </ul>
            </nav>

            <div class="user-info">
                <p><strong>Logged in as:</strong><br><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>My Profile</h1>
            </div>

            <div class="profile-info">
                <h2>Account Information</h2>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($userInfo['username']); ?></p>
                <p><strong>Email:</strong>
                    <?php echo $userInfo['email'] ? htmlspecialchars($userInfo['email']) : '-'; ?></p>
                <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($userInfo['created_at'])); ?></p>
                <p><strong>Currently Borrowed Books:</strong> <?php echo $borrowedCount; ?> /
                    <?php echo BORROW_LIMIT; ?></p>
            </div>

            <div style="margin-top: 30px;">
                <div class="profile-info">
                    <p><strong>Maximum Books Allowed:</strong> <?php echo BORROW_LIMIT; ?> books</p>
                    <p><strong>Borrow Period:</strong> <?php echo BORROW_DAYS; ?> days</p>
                    <p><strong>Available Slots:</strong> <?php echo BORROW_LIMIT - $borrowedCount; ?> /
                        <?php echo BORROW_LIMIT; ?></p>
                </div>
            </div>
        </main>
    </div>
</body>

</html>