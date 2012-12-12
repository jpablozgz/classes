<?php

class indexController
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
		$this->content = $this->view->renderView('index/index', array());
	}
	
	public function __destruct()
	{
		$params = array('content'=>$this->content);
		echo $this->view->renderLayout("layout_front", $params);
	}
} 
?>