<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<form method="post" action="<?php echo __SITE_URL . '/balance.php?rt=users/loginCheck'?>">
	Username: <input type="text" name="username" /> <br />
	Password: <input type="password" name="password" /> <br />
	<input type="submit" value="Log in">
</form>
<br /> 
<?php echo $message; ?>
<br />
Don't have an account? Make one!
<form method="post" action="<?php echo __SITE_URL . '/balance.php?rt=users/newUser'?>">
	Username: <input type="text" name="username" /> <br />
    Email:    <input type="text" name="email" /> <br />
	Password: <input type="password" name="password" /> <br />
	<input type="submit" value="Register">
</form>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
