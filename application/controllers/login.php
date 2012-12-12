<?php
require_once(APPLICATION_PATH."/models/loginModel.php");

class loginController
{
	public $content;
	public $view;
	public $config;

	public function __construct($config)
	{
		$this->config = $config;
		$this->view = new Models_applicationModel($config);
	}
	
	public function indexAction()
	{
		$this->loginAction();
	}

	public function loginAction()
	{
		if($_POST)
		{
			$model = new Models_loginModel($this->config);
			$model->loginUser($_POST);
			header("Location: /users");
			exit();
		}
		else
			$this->content = $this->view->renderView('login/login', array());
	}

	public function logoutAction()
	{
		session_destroy();
		header ("Location: /index");
		exit();
	}

	public function __destruct()
	{
		$params = array('content'=>$this->content);
		echo $this->view->renderLayout("layout_login", $params);
	}
}
?>