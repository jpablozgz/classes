<?php

class Application_bootstrap
{
	protected $configFile;
	protected $config;
	protected $request;
	
	public function __construct($filename)
	{
		$this->configFile = $filename;
		
		$this->_initSession();
		$this->_initConfig();
		$this->_initDb();
		$this->request = $this->_initRequest();
		$this->_initDefaultRole();
		$this->_initAcl();
	}
	
	protected function _initSession()
	{
		session_start();
		if(!isset($_SESSION[$this->config['sessionNamespace']]))
			$_SESSION[$this->config['sessionNamespace']]=array();
		Models_debugModel::_debug($_SESSION);
	}
	
	protected function _initConfig()
	{
		$this->config = Models_applicationModel::readConfig('../application/configs/'.$this->configFile.'.ini',
							 						  APPLICATION_ENV);
	}

	protected function _initDb()
	{
		$_SESSION['register']['db']=Models_mysqlModel::singleton($this->config);
//		$cnx=connect($config);
	}
	
	protected function _initRequest()
	{
		$this->request = Models_applicationModel::setRequest();
	}
	
	protected function _initDefaultRole()
	{
		$_SESSION['user_role']=$this->config['defaultRole'];
	}

	protected function _initAcl()
	{
		$this->request = Models_applicationModel::acl($this->request);
	}
	
	public function run()
	{
		/* Aqui se crearia la sesion, se crearian las cookies,
		 * y se verificaria que el usuario esta autenticado */
		
		include("../application/controllers/".$this->request['controller'].".php");
	}
}
?>