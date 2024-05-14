<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<?php require_once __SITE_PATH . '/view/_navigation.php'; ?>

<form method="post" action="<?php echo __SITE_URL . '/balance.php?rt=expenses/addexpense'?>">
	Description: <input type="text" name="description" /> <br />
	Cost in EUR: <input type="text" name="cost" /> <br />
    Users: <br />
    <?php 
		foreach( $userList as $id => $username )
		{
			echo '<input type="checkbox" ' .
                 'id="user_' . $id .
                 '" name="user_' . $id .
                 '" value="1">';
            echo '<label for="user_' . $id .
                 '">' . $username .
                 '</label> <br />';
		}
	?>
	<input type="submit" value="Create Expense">
</form>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
