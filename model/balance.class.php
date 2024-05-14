<?php

class Balance
{
    protected $username;
    protected $balance;

	function __construct( $username, $balance )
	{
		$this->username = $username;
		$this->balance = $balance;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>