<?php

class Part
{
	protected $id;
    protected $id_expense;
    protected $id_user;
    protected $cost;

	function __construct( $id, $id_expense, $id_user, $cost )
	{
		$this->id = $id;
		$this->id_expense = $id_expense;
		$this->id_user = $id_user;
		$this->cost = $cost;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>