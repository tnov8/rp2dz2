<?php

class Expensedto
{
    protected $description;
    protected $username;
    protected $cost;
    protected $date;

	function __construct( $description, $username, $cost )
	{
		$this->description = $description;
		$this->username = $username;
        $this->cost = $cost;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>