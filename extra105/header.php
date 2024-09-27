<?php
session_start();
?>
<nav>
    <ul>
        <?php if (isset($_SESSION['username'])): ?>
            <li>ログイン中: <?php echo $_SESSION['username']; ?></li>
            <?php if ($_SESSION['is_admin'] == 1): ?>
                <li><a href="admin.php">管理者ページ</a></li>
            <?php endif; ?>
            <li><a href="logout.php">ログアウト</a></li>
        <?php else: ?>
            <li><a href="login.php">ログイン</a></li>
        <?php endif; ?>
    </ul>
</nav>
