<?php

//BUGFIX: Corregir chapucillas

class userController
{
	public $content;
	public $view;
	public $config;
	
	public function __construct($config)
	{
		$this->view = new Models_applicationModel();
		$this->config = $config;
		$arrayUser = initArrayUser();
	}
	
	public function indexAction()
	{
		$this->selectAction();
	}
	
	public function selectAction()
	{
		$model = new Models_usersDBModel($this->config);
		$arrayUsers = $model->readUsers($cnx);
		$params=array('arrayUsers'=>$arrayUsers);
		$this->content = $this->view->renderView("users/select", $params, $this->config);
	}
	
	public function insertAction()
	{
		if($_POST)
		{
			$imageName = (!$_FILES['photo']['error'] ? uploadImage($_FILES, $config) : '');
			$id=insertUser($_POST, $cnx, $imageName);
			header("Location: index.php?controller=users&action=select");
			exit();
		}
		else
		{
			$params=array('arrayUser'=>$arrayUser,
						  'arrayDataPets'=>readPets($cnx),
						  'arrayUserPets'=>array(),
						  'arrayDataCities'=>readCities($cnx),
						  'arrayUserCities'=>array(),
						  'arrayDataCoders'=>readCoders($cnx),
						  'arrayUserCoders'=>array(),
						  'arrayDataLanguages'=>readLanguages($cnx),
						  'arrayUserLanguages'=>array(),
						 );
			$content = renderView("users/formulario", $params, $config);
		}
	}
	
	public function updateAction()
	{
		if($_POST)
		{
			$imageName = updateImage($_FILES, $_GET['id'], $config);
			updateUser($arrayData, $_GET['id'], $cnx, $imageName);
			header("Location: index.php?controller=users&action=select");
			exit();
		}
		else
		{
			$arrayUser=readUser($_GET['id'], $cnx);
			$params=array('arrayUser'=>$arrayUser,
						  'arrayDataPets'=>readPets($cnx),
						  'arrayUserPets'=>readUserPets($arrayUser['iduser'], $cnx),
						  'arrayDataCities'=>readCities($cnx),
						  'arrayUserCities'=>array($arrayUser['cities_idcity']),
						  'arrayDataCoders'=>readCoders($cnx),
						  'arrayUserCoders'=>array($arrayUser['coders']),
						  'arrayDataLanguages'=>readLanguages($cnx),
						  'arrayUserLanguages'=>readUserLanguages($arrayUser['iduser'], $cnx),
						 );
		}
		$this->insertAction();
	}
	
	public function deleteAction()
	{
		if($_POST)
		{
			if($_POST['submit']=='yes')
				deleteUser($_GET['id'], $cnx);
			header("Location: index.php?controller=users&action=select");
			exit();
		}
		else
		{
			$content = renderView("users/delete", array(), $config);
		}
	}
	
	public function __destruct()
	{
		$params = array('userName'=>(isset($_SESSION['name'])?$_SESSION['name']:'Guest'),
						'content'=>$this->content);
		echo $this->view->renderLayout("layout_admin1", $params, $this->config);
	}
}
// Initializing variables

switch($arrayRequest['action'])
{
	case 'update':
		// CAUTION: There is no break; here!!!!!!!!!!
	case 'insert':
		break;
	case 'delete':
		break;
	case 'index':
	case 'select':
	default:
		break;
}
?>