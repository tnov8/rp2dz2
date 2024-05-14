<?php 

class UsersController extends BaseController
{
    public function index() { }
    public function balance() { 
        $this->checkLogin();
    }
    public function checkLogin() {
        if( !isset( $_COOKIE['loginUsername'] ) )
        {
            header( 'Location: ' . __SITE_URL . '/balance.php?rt=users/login' );
            $this->login();
        }
    }

    public function overview() 
	{
        $this->checkLogin();

		$bs = new BalanceService();

		$this->registry->template->title = 'Overview';
		$this->registry->template->balanceList = $bs->getAllBalances();

        $this->registry->template->show( 'users_overview' );
	}

    public function history()
    {
        $this->checkLogin();
        
        $bs = new BalanceService();
        $user_id = $_GET['id_user'];
        $name = $bs->getUserNameById( $user_id );
		$this->registry->template->title = "Overview ($name)";
        $balanceList = $bs->getHistory( $user_id );
		$this->registry->template->balanceList = $balanceList;
        $total = 0;
        foreach( $balanceList as $id => $balance )
            $total += intval( $balance->balance );
        $this->registry->template->total = $total;
        $this->registry->template->show( 'users_history' );
    }

    public function login() 
	{
        $this->registry->template->title = 'Login';
		$this->registry->template->show( 'users_login' );
	}

	public function loginCheck() 
	{
		// Kontroler koji sluzi za login korisnika

		$bs = new BalanceService();

		$this->registry->template->title = 'Login';

        // Ako nam forma nije u $_POST poslala autora u ispravnom obliku, preusmjeri ponovno na formu.
		if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
		{
			header( 'Location: ' . __SITE_URL . '/balance.php?rt=users/login');
			exit();
		}
        
        // Sanitizacija usernamea i passworda
        $username = filter_var( $_POST['username'], FILTER_SANITIZE_STRING );
        $password = filter_var( $_POST['password'], FILTER_SANITIZE_STRING );

        // Provjeri je li Login valjan (uspjesan)
        if ($bs->validateLogin($username, $password)) {
            setcookie('loginUsername', $username, time() + 3600);
            setcookie('loginId', $bs->getIdByUsername( $username ), time() + 3600);
            header( 'Location: ' . __SITE_URL . '/balance.php?rt=users/overview' );
        } else {
            $this->registry->template->title = 'Login';
            $this->registry->template->message = "Invalid username or password or account not confirmed. Please try again.";
		    $this->registry->template->show( 'users_login' );
        }
	}
    
    public function newUser() 
    {
        $bs = new BalanceService();
        $this->registry->template->message = $bs->addUser();
        $this->registry->template->title = 'Login';
        $this->registry->template->show( 'users_login' );
    }

    public function register()
    {
        $bs = new BalanceService();
        $regseq = $_GET['seq'];
        $this->registry->template->message = $bs->confirmRegistration( $regseq );
        $this->registry->template->title = 'Login';
        $this->registry->template->show( 'users_login' );
    }

    public function logout() 
    {
        setcookie('loginUsername', '', 1);
        setcookie('loginId', '', 1);
        $this->registry->template->title = 'Login';
		$this->registry->template->show( 'users_login' );
    }
}; 

?>
