<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<?php require_once __SITE_PATH . '/view/_navigation.php'; ?>

<table>
	<tr><th>Name</th><th>Balance</th></tr>
	<?php 
		foreach( $balanceList as $user_id => $balance )
		{
			echo '<tr>' .
                 '<td><a href="' . __SITE_URL . '/balance.php?rt=users/history&id_user=' . $user_id . '">' . $balance->username . '</a></td>' .
			     '<td>' . $balance->balance . ' €</td>' .
			     '</tr>';
		}
	?>
</table>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
