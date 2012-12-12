<?php

class Models_loginModel
{
	protected $objDB;
	protected $config;
	
	public function __construct($config)
	{
		$this->objDB=$_SESSION['register']['db'];
		$this->config = $config;
	}

	public function loginUser($arrayData)
	{
		$sql = "SELECT iduser, name, roles_idrole
				FROM users
				WHERE email='".$arrayData['email']."' AND
					  password='".$arrayData['password']."';";
		$user = $this->objDB->query($sql);

		if(count($user)==1)
		{
			$_SESSION[$this->config['sessionNamespace']]['iduser']=$user[0]['iduser'];
			$_SESSION[$this->config['sessionNamespace']]['name']=$user[0]['name'];
			$_SESSION[$this->config['sessionNamespace']]['user_role']=$user[0]['roles_idrole'];
			//TODO: Regenerar el id de sesion
			return TRUE;
		}
		else				 // algo muy chungo ha sucedido con la BdD
			return FALSE;
	}
}
?>