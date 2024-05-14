<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<?php require_once __SITE_PATH . '/view/_navigation.php'; ?>

<table>
	<tr><th>Description</th><th>Cost</th></tr>
	<?php 
		foreach( $balanceList as $user_id => $balance )
		{
			echo '<tr>' .
                 '<td>' . $balance->username . '</td>' .
			     '<td>' . $balance->balance . ' €</td>' .
			     '</tr>';
		}
        echo '<tr class="total">' .
            '<td>' . "Total" . '</td>' .
            '<td>' . $total . ' €</td>' .
            '</tr>';
	?>
</table>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
