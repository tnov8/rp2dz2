<?php 

class BalanceController extends BaseController
{
    public function index() { }
	public function balance() 
	{
		// Preusmjeri na login ili overview
        if( isset( $_COOKIE['loginUsername'] ) )
            header( 'Location: ' . __SITE_URL . '/balance.php?rt=users/overview' );
        else
            header( 'Location: ' . __SITE_URL . '/balance.php?rt=users/login' );
	}
}; 

?>