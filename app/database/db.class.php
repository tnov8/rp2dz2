<?php

class DB
{
	private static $db = null;

	private function __construct() { }
	private function __clone() { }

	public static function getConnection() 
	{
		if( DB::$db === null )
	    {
	    	try
	    	{
                $fileName = __DIR__ . '/../../../../../databasePassword';
                if ( ( $f = fopen($fileName, 'r') ) === false )
                	exit( "Ne mogu otvoriti file: $php_err	ormsg" );
                $userinfo = fscanf( $f, "%s %s" );
                list( $USERNAME, $PASSWORD ) = $userinfo;
                fclose($f);

	    		// Unesi ispravni HOSTNAME, DATABASE, USERNAME i PASSWORD
				// $USERNAME = "student";
				// $PASSWORD = "pass.mysql";
				$HOSTNAME = "rp2.studenti.math.hr";
                $DATABASE = "novak";
		    	DB::$db = new PDO( "mysql:host=rp2.studenti.math.hr;dbname=novak;charset=utf8", $USERNAME, $PASSWORD );
		    	DB::$db-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    }
		    catch( PDOException $e ) { exit( "$USERNAME $PASSWORD PDO Error: " . $e->getMessage() ); }
	    }
		return DB::$db;
	}
}

?>
