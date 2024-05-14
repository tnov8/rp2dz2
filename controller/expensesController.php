<?php 

class ExpensesController extends BaseController
{
    public function index() {}
    public function checkLogin()
    {
        if( !isset( $_COOKIE['loginUsername'] ) )
        {
            header( 'Location: ' . __SITE_URL . '/balance.php?rt=users/login' );
            $this->registry->template->title = 'Login';
            $this->registry->template->show( 'users_login' );
        }
    }
    
	public function balance() 
	{
        $this->checkLogin();

		$bs = new BalanceService();

		$this->registry->template->title = 'Expenses';
		$this->registry->template->expenseList = $bs->getAllExpenses();

        $this->registry->template->show( 'expenses' );
	}

    public function newexpense() 
	{
        $this->checkLogin();

        $bs = new BalanceService();

		$this->registry->template->title = 'New expense';
        $this->registry->template->userList = $bs->getAllUsers();

        $this->registry->template->show( 'new_expense' );
	}

    public function addexpense()
    {
        $this->checkLogin();

        $bs = new BalanceService();
        $this->registry->template->expenseList = $bs->addExpense();

        $this->balance();
    }
}; 

?>