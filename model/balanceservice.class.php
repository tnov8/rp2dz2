<?php

class BalanceService
{
	function validateLogin( $username, $password )
	{
        if ( !preg_match('/^[a-zA-Z0-9_]+$/', $username) )
            return false;
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT password_hash FROM dz2_users WHERE username=:username AND has_registered = 1' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false )
			return false;
		else
            return password_verify($password, $row['password_hash']);
	}

    function getAllBalances( )
	{
        $balanceList = [];
        try
		{
			$db = DB::getConnection();
			$users = $db->prepare( 'SELECT id, username, total_paid, total_debt FROM dz2_users WHERE has_registered = 1' );
			$users->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		while( $row = $users->fetch() )
		{
			$balanceList[$row['id']] = new Balance($row['username'], $row['total_paid'] - $row['total_debt']);
		}
        
        return $balanceList;
	}

    function getHistory( $user )
	{
        // Expenses
        try
		{
			$db = DB::getConnection();
			$expenses = $db->prepare( 'SELECT description, cost FROM dz2_expenses WHERE id_user=:user' );
			$expenses->execute( array( 'user' => $user ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        $arr = array();
		while( $row = $expenses->fetch() )
		{
            $arr[] = new Balance( $row['description'], "+" . $row['cost'] );
		}

        // Parts
        try
		{
			$db = DB::getConnection();
            $query = 'SELECT
                        p.cost AS part_cost,
                        e.description AS expense_description
                    FROM
                        dz2_parts p
                    JOIN
                        dz2_expenses e ON p.id_expense = e.id
                    WHERE
                        p.id_user = :user;';
			$parts = $db->prepare( $query );
			$parts->execute( array( 'user' => $user ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        
		while( $row = $parts->fetch() )
		{
            $arr[] = new Balance( $row['expense_description'], "-" . $row['part_cost'] );
		}
        
        return $arr;
	}

    function getAllExpenses()
	{
        try
		{
			$db = DB::getConnection();
            $query = 'SELECT 
                        e.description AS description, 
                        u.username AS username,
                        e.cost AS cost,
                        e.date as date
                    FROM 
                        dz2_expenses e
                    JOIN 
                        dz2_users u ON e.id_user = u.id;';
			$expenses = $db->prepare( $query );
			$expenses->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        $arr = array();
		while( $row = $expenses->fetch() )
		{
            $arr[strtotime( $row['date'] )] = new Expensedto( $row['description'], $row['username'], $row['cost'] );
		}
        krsort( $arr );
        return $arr;
	}

    function getUserNameById( $user_id )
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT username FROM dz2_users WHERE id=:id AND has_registered = 1' );
			$st->execute( array( 'id' => $user_id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        $row = $st->fetch();
		if( $row === false )
			return false;
        else
            return $row['username'];
    }

    function getIdByUsername( $username )
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id FROM dz2_users WHERE username=:username AND has_registered = 1' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        $row = $st->fetch();
		if( $row === false )
			return false;
        else
            return $row['id'];
    }

    function getAllUsers()
    {
        try
		{
			$db = DB::getConnection();
			$users = $db->prepare( 'SELECT id, username FROM dz2_users WHERE has_registered = 1' );
			$users->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        $arr = array();
		while( $row = $users->fetch() )
		{
			$arr[$row['id']] = $row['username'];
		}
        return $arr;
    }

    function addExpense()
    {
        try
		{
            // Sanitizacija i validacija
            $description = filter_var( $_POST['description'], FILTER_SANITIZE_STRING );
            if ( strlen( $description ) > 50 )
                return;
            $cost = filter_var( $_POST['cost'], FILTER_SANITIZE_STRING );
            if ( filter_var( $cost, FILTER_VALIDATE_INT ) === false || $cost < 0 )
                return;

            // Na koliko osoba se dijeli trosak?
            $brOsoba = 0;
            foreach( $_POST as $key => $value )
            {
                if ( substr( $key, 0, 5 ) === 'user_' )
                    $brOsoba++;
            }
            if ( $brOsoba === 0 )
                return;
            $trosakPoOsobi =  $_POST['cost'] / $brOsoba;

			$db = DB::getConnection();

            // Insert into EXPENSES ------------------------
            $query =
                'INSERT INTO dz2_expenses (id_user, cost, description, date) ' .
                'VALUES (:id, :cost, :description, :date)';
            $add = $db->prepare( $query );
			$add->execute( array( 
                'id' => $_COOKIE['loginId'],
                'cost' => $cost,
                'description' => $description,
                'date' => date( 'Y-m-d H:i:s', time() )
            ) );
            $newId = $db->lastInsertId();

            // Change total_paid in USERS -----------------
            $query = 
                'UPDATE dz2_users ' .
                'SET total_paid = total_paid + :cost ' .
                'WHERE id = :id';
            $change = $db->prepare( $query );
            $change->execute( array( 
                'cost' => $cost,
                'id' => $_COOKIE['loginId']
            ) );
                 
            foreach( $_POST as $key => $value )
            {
                if ( substr( $key, 0, 5 ) === 'user_' )
                {
                    // Insert into PARTS ------------------    
                    $query = 
                        'INSERT INTO dz2_parts (id_expense, id_user, cost) ' .
                        'VALUES (:id_expense, :id_user, :cost)';
                    $add = $db->prepare( $query );
                    $add->execute( array( 
                        'id_expense' => $newId,
                        'id_user' => intval( substr( $key, 5 ) ),
                        'cost' => $trosakPoOsobi
                    ) );

                    // Change total_debt in USERS ---------
                    $query = 
                        'UPDATE dz2_users ' .
                        'SET total_debt = total_debt + :cost ' .
                        'WHERE id = :id';
                    $change = $db->prepare( $query );
                    $change->execute( array( 
                        'cost' => $trosakPoOsobi,
                        'id' => intval( substr( $key, 5 ) )
                    ) );
                }
            }

		}
        catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
    }

    function addUser()
    {
        if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) || !isset( $_POST['email'] ) )
            return "Every field is mandatory!";

        // Sanitizacija i validacija unosa
        $username = filter_var( $_POST['username'], FILTER_SANITIZE_STRING );
        $password = filter_var( $_POST['password'], FILTER_SANITIZE_STRING );
        $email = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL );

        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) === false )
            return "Email not valid!";
        if ( !preg_match('/^[a-zA-Z0-9_]+$/', $username) )
            return "Username not valid!";
        if (strlen($password) < 1 || strlen($password) > 20)
            return "Password must be between 1 and 20 characters long!";

        // Check if a username exists in the database
        try
		{
			$db = DB::getConnection();
			$user = $db->prepare( 'SELECT * FROM dz2_users WHERE username=:username' );
			$user->execute( array( "username" => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        if( $user->fetch() !== false )
			return "User with given username already exists!";
        
        // Generate random code of length 8
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = substr(str_shuffle($characters), 0, 8);

        // Send email
        $to      = $email;
        $subject = 'Link za registraciju na Balance';
        $message = 'Username: ' . $username .
                   "\nRegistration link: https://rp2.studenti.math.hr/~tnovak/dz2/balance.php/users/register?seq="
                   . $code;
        $headers = 'From: tomislav.novak1@student.math.hr'       . "\r\n" .
                    'Reply-To: tomislav.novak1@student.math.hr' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        if ( mail($to, $subject, $message, $headers) === false )
            return "Email not sent";

        // Add to database
        try
		{
			$db = DB::getConnection();
            $query =
                'INSERT INTO dz2_users (username, password_hash, total_paid, total_debt,' .
                'email, registration_sequence, has_registered) ' .
                'VALUES (:user, :pass, 0, 0, :email, :regseq, 0)';
			$user = $db->prepare( $query );
			$user->execute( array( "user" => $username,
                                    "pass" => password_hash($password, PASSWORD_DEFAULT),
                                    "email" => $email,
                                    "regseq" => $code ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        return "Account successfully created! Check your e-mail for the confirmation link.";
    }

    function confirmRegistration( $regseq )
    {
        // Sanitizacija i validacija
        $regseq = filter_var( $regseq, FILTER_SANITIZE_STRING );
        if ( !preg_match( '/^[a-zA-Z]{8}$/', $regseq ) )
            return "Sequence not valid!";
        
        try
		{
			$db = DB::getConnection();
            $query = 'UPDATE dz2_users SET has_registered = 1 WHERE registration_sequence = :regseq';
			$user = $db->prepare( $query );
			$user->execute( array( "regseq" => $regseq ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        return "Account confirmed! You may now login.";
    }
};

?>