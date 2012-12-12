<?php

class usersController
{
	public $content;
	public $view;
	public $model;
	public $config;
	
	public function __construct($config)
	{
		$this->config = $config;
		$this->view = new Models_applicationModel($config);
		$this->model = new Models_usersDBModel($config);
	}
	
	public function indexAction()
	{
		$this->selectAction();
	}
	
	public function selectAction()
	{
		$arrayUsers = $this->model->readUsers();
		$params=array('arrayUsers'=>$arrayUsers);
		$this->content = $this->view->renderView("users/select", $params);
	}
	
	public function insertAction()
	{
		if($_POST)
		{
			$imageName = (!$_FILES['photo']['error'] ? Models_usersDBModel::uploadImage($_FILES, $this->config) : '');
			$id = $this->model->insertUser($_POST, $imageName);
			header("Location: index.php?controller=users&action=select");
			exit();
		}
		else
		{
			$params=array('arrayUser'=>Models_usersDBModel::initArrayUser(),
						  'arrayDataPets'=>$this->model->readPets(),
						  'arrayUserPets'=>array(),
						  'arrayDataCities'=>$this->model->readCities(),
						  'arrayUserCities'=>array(),
						  'arrayDataCoders'=>$this->model->readCoders(),
						  'arrayUserCoders'=>array(),
						  'arrayDataLanguages'=>$this->model->readLanguages(),
						  'arrayUserLanguages'=>array()
						 );
			$this->content = $this->view->renderView("users/formulario", $params);
		}
	}
	
	public function updateAction()
	{
		if($_POST)
		{
			$imageName = $this->model->updateImage($_FILES, $_GET['id']);
			$this->model->updateUser($_POST, $_GET['id'], $imageName);
			header("Location: index.php?controller=users&action=select");
			exit();
		}
		else
		{
			$arrayUser = $this->model->readUser($_GET['id']);
			$params=array('arrayUser'=>$arrayUser,
						  'arrayDataPets'=>$this->model->readPets(),
						  'arrayUserPets'=>$this->model->readUserPets($arrayUser['iduser']),
						  'arrayDataCities'=>$this->model->readCities(),
						  'arrayUserCities'=>array($arrayUser['cities_idcity']),
						  'arrayDataCoders'=>$this->model->readCoders(),
						  'arrayUserCoders'=>array($arrayUser['coders']),
						  'arrayDataLanguages'=>$this->model->readLanguages(),
						  'arrayUserLanguages'=>$this->model->readUserLanguages($arrayUser['iduser'])
						 );
		}
		$this->content = $this->view->renderView("users/formulario", $params);
	}
	
	public function deleteAction()
	{
		if($_POST)
		{
			if($_POST['submit']=='yes')
				$this->model->deleteUser($_GET['id']);
			header("Location: index.php?controller=users&action=select");
			exit();
		}
		else
		{
			$this->content = $this->view->renderView("users/delete", array());
		}
	}
	
	public function __destruct()
	{
		$params = array('userName'=>(isset($_SESSION[$this->config['sessionNamespace']]['name'])?
											$_SESSION[$this->config['sessionNamespace']]['name']:'Guest'),
						'content'=>$this->content);
		echo $this->view->renderLayout("layout_admin1", $params);
	}
}