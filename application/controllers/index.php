<?php

class indexController
{
	public $content;
	public $view;
	public $config;
	
	public function __construct($config)
	{
		$this->view = new Models_applicationModel();
		$this->config = $config;
	}
	
	public function indexAction()
	{
		$this->content = $this->view->renderView('index/index', array(), $this->config);
	}
	
	public function __destruct()
	{
		$params = array('content'=>$this->content);
		echo $this->view->renderLayout("layout_front", $params, $this->config);
	}
} 
?>