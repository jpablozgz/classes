<?php

class errorController
{
	public $content;
	public $view;
	public $config;

	public function __construct($config)
	{
		$this->config = $config;
		$this->view = new Models_applicationModel($config);
	}
	
	public function error404Action()
	{
		header("HTTP/1.0 404 Not Found");
		$this->content = $this->view->renderView('error/error404', array());
	}

	public function error403Action()
	{
		header("HTTP/1.0 403 Not Allowed");
		$this->content = $this->view->renderView('error/error403', array());
	}
	
	public function __destruct()
	{
		$params = array('userName'=>(isset($_SESSION['name'])?$_SESSION['name']:'Guest'),
						'content'=>$this->content);
		echo $this->view->renderLayout("layout_admin1", $params, $this->config);
	}
}
?>