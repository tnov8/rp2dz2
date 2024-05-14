<?php

class User
{
	protected $id;
    protected $username;
    protected $password_hash;
    protected $total_paid;
    protected $total_debt;
    protected $email;
    protected $registration_sequence;
    protected $has_registered;

	function __construct( $id, $username, $email )
	{
		$this->id = $id;
		$this->username = $username;
		$this->email = $email;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>