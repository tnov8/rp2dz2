<header>
    <nav>
        <ul>
            <li><a href="<?php echo __SITE_URL; ?>/balance.php?rt=users/overview">Overview</a></li>
            <li><a href="<?php echo __SITE_URL; ?>/balance.php?rt=expenses">Expenses</a></li>
            <li><a href="<?php echo __SITE_URL; ?>/balance.php?rt=expenses/newexpense">New expense</a></li>
        </ul>
        <span>Hello, <?php echo $_COOKIE['loginUsername']; ?>!</span>
        <a class="logout" href="<?php echo __SITE_URL; ?>/balance.php?rt=users/logout">Logout</a>
    </nav>
</header>