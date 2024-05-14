<?php

// Manualno inicijaliziramo bazu ako veÄ‡ nije.
require_once 'db.class.php';

$db = DB::getConnection();

$has_tables = false;

try
{
    $st = $db->prepare( 
        'SHOW TABLES LIKE :tblname'
    );

    $st->execute( array( 'tblname' => 'dz2_users' ) );
    if( $st->rowCount() > 0 )
        $has_tables = true;

    $st->execute( array( 'tblname' => 'dz2_expenses' ) );
    if( $st->rowCount() > 0 )
        $has_tables = true;

    $st->execute( array( 'tblname' => 'dz2_parts' ) );
    if( $st->rowCount() > 0 )
        $has_tables = true;

}
catch( PDOException $e ) { exit( "PDO error [show tables]: " . $e->getMessage() ); }


if( $has_tables )
{
    exit( 'Tablice dz2_users / dz2_expenses / dz2_parts veÄ‡ postoje. ObriĹˇite ih pa probajte ponovno.' );
}


try
{
    $st = $db->prepare( 
        'CREATE TABLE IF NOT EXISTS dz2_users (' .
        'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
        'username varchar(50) NOT NULL,' .
        'password_hash varchar(255) NOT NULL,'.
        'total_paid int NOT NULL,' .
        'total_debt int NOT NULL,' .
        'email varchar(50) NOT NULL,' .
        'registration_sequence varchar(20) NOT NULL,' .
        'has_registered int' .
        ')'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create dz2_users]: " . $e->getMessage() ); }

echo "Napravio tablicu dz2_users.<br />";

try
{
    $st = $db->prepare( 
        'CREATE TABLE IF NOT EXISTS dz2_expenses (' .
        'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
        'id_user int NOT NULL,' . 
        'cost int NOT NULL,' . 
        'description varchar(50) NOT NULL,' .
        'date DATETIME NOT NULL' .
        ')'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create dz2_expenses]: " . $e->getMessage() ); }

echo "Napravio tablicu dz2_expenses.<br />";

try
{
    $st = $db->prepare( 
        'CREATE TABLE IF NOT EXISTS dz2_parts (' .
        'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
        'id_expense INT NOT NULL,' .
        'id_user INT NOT NULL,' .
        'cost int NOT NULL' . 
        ')'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create parts]: " . $e->getMessage() ); }

echo "Napravio tablicu dz2_parts.<br />";

// RuÄŤak -> mirko 60 za mirko (20), ana (20), maja (20)
// Hotel -> pero 400 za pero (100), ana (100), maja (100), mirko (100)
// Izlet -> maja 200 za ana (100), maja (100)
// Posudba 100 eur -> maja 100 za pero (100)
// ------------------
// mirko 60-20-100 = 60 - 120 = -60
// ana   -20-100-100 = 0 - 220 = -220
// pero  400-100-100 = 400 - 200 = 200
// maja  -20-100+200-100+100 = 300 - 220 = 80


// Ubaci neke korisnike unutra
try
{
    $st = $db->prepare( 'INSERT INTO dz2_users(username, password_hash, total_paid, total_debt, email, registration_sequence, has_registered) VALUES (:username, :password, :paid, :debt, \'a@b.com\', \'abc\', \'1\')' );

    $st->execute( array( 'username' => 'Mirko', 'password' => password_hash( 'mirkovasifra', PASSWORD_DEFAULT ), 'paid' => 60, 'debt' => 120 ) );
    $st->execute( array( 'username' => 'Ana', 'password' => password_hash( 'aninasifra', PASSWORD_DEFAULT ), 'paid' => 0, 'debt' => 220 ) );
    $st->execute( array( 'username' => 'Pero', 'password' => password_hash( 'perinasifra', PASSWORD_DEFAULT ), 'paid' => 400, 'debt' => 200 ) );
    $st->execute( array( 'username' => 'Maja', 'password' => password_hash( 'majinasifra', PASSWORD_DEFAULT ), 'paid' => 300, 'debt' => 220 ) );
}
catch( PDOException $e ) { exit( "PDO error [insert dz2_users]: " . $e->getMessage() ); }

echo "Ubacio u tablicu dz2_users.<br />";


// Ubaci neke raÄŤune unutra (ovo nije baĹˇ pametno ovako raditi, preko hardcodiranih id-eva usera):
try
{
    $st = $db->prepare( 'INSERT INTO dz2_expenses(id_user, cost, description, date) VALUES (:id, :cost, :descr, :date)' );

    $st->execute( array( 'id' => 1, 'cost' => 60, 'descr' => 'RuÄŤak', 'date' => '2024-04-26 12:45:00' ) ); // mirko
    $st->execute( array( 'id' => 3, 'cost' => 400, 'descr' => 'Hotel', 'date' => '2024-04-26 15:00:00' ) ); // pero
    $st->execute( array( 'id' => 4, 'cost' => 200, 'descr' => 'Izlet', 'date' => '2024-04-27 09:00:00' ) ); // maja
    $st->execute( array( 'id' => 4, 'cost' => 100, 'descr' => 'Posudba 100 eur', 'date' => '2024-04-27 11:00:00' ) ); // maja
}
catch( PDOException $e ) { exit( "PDO error [dz2_expenses]: " . $e->getMessage() ); }

echo "Ubacio u tablicu dz2_expenses.<br />";


// Ubaci dijelove raÄŤuna unutra (ovo nije baĹˇ pametno ovako raditi, preko hardcodiranih id-eva usera / raÄŤuna)
try
{
    $st = $db->prepare( 'INSERT INTO dz2_parts(id_expense, id_user, cost) VALUES (:id_ex, :id_user, :cost)' );

    $st->execute( array( 'id_ex' => 1, 'id_user' => 1, 'cost' => 20 ) ); // RuÄŤak, mirko
    $st->execute( array( 'id_ex' => 1, 'id_user' => 2, 'cost' => 20 ) ); // RuÄŤak, ana
    $st->execute( array( 'id_ex' => 1, 'id_user' => 4, 'cost' => 20 ) ); // RuÄŤak, maja
    $st->execute( array( 'id_ex' => 2, 'id_user' => 3, 'cost' => 100 ) ); // Hotel, pero
    $st->execute( array( 'id_ex' => 2, 'id_user' => 2, 'cost' => 100 ) ); // Hotel, ana
    $st->execute( array( 'id_ex' => 2, 'id_user' => 4, 'cost' => 100 ) ); // Hotel, maja
    $st->execute( array( 'id_ex' => 2, 'id_user' => 1, 'cost' => 100 ) ); // Hotel, mirko
    $st->execute( array( 'id_ex' => 3, 'id_user' => 2, 'cost' => 100 ) ); // Izlet, ana
    $st->execute( array( 'id_ex' => 3, 'id_user' => 4, 'cost' => 100 ) ); // Izlet, maja
    $st->execute( array( 'id_ex' => 4, 'id_user' => 3, 'cost' => 100 ) ); // Posudba 100 eur, pero
}
catch( PDOException $e ) { exit( "PDO error [dz2_parts]: " . $e->getMessage() ); }

echo "Ubacio u tablicu dz2_parts.<br />";

?> 
