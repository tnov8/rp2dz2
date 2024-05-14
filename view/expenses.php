<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<?php require_once __SITE_PATH . '/view/_navigation.php'; ?>

<table>
	<tr><th>Name</th><th>Balance</th></tr>
	<?php 
		foreach( $expenseList as $expense )
		{
			echo '<tr>' .
			     '<td>' . $expense->description . '</td>' .
			     '<td>' . $expense->username . '</td>' .
			     '<td>' . $expense->cost . '</td>' .
			     '</tr>';
		}
	?>
</table>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
