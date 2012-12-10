<?php

class Models_usersDBModel
{
	protected $config;
	protected $cnx;
	
	public function __construct($config)
	{
		$this->config = $config;
		$this->cnx=$_SESSION['register']['db'];
	}
	
	/** Upload photo in uploads
	 * @param array $_FILES Array FILES
	 * @param array $config Config variables
	 * @return string: Final filename of the photograph
	 */
	public static function uploadImage($_FILES, $config)
	{
		$destination = $config['uploadDirectory']."/".$_FILES['photo']['name'];
		$filename = $_FILES['photo']['tmp_name'];
		
		$path_parts = pathinfo($destination);
		$name=$path_parts['basename'];
		
		$i=0;
		while(in_array($name,scandir($config['uploadDirectory'])))
		{
			$i++;	
			$name=$path_parts['filename']."_".$i.".".$path_parts['extension'];
		}
		
		$destination = $config['uploadDirectory']."/".$name;
		move_uploaded_file($filename, $destination);
		return $name;
	}
	
	/** Update photo in uploads
	 * @param array $_FILES Array FILES
	 * @param int User id
	 * @param array $config Config variables
	 * @return string: Filename of the new photo
	 */
	public function updateImage($_FILES, $id)
	{
		$arrayUser=$this->readUserFromFile($id);
		$image=trim($arrayUser[9]);
		if(!$_FILES['photo']['error'])
		{
			unlink($config['uploadDirectory']."/".$image); // deletes old photo
			$image=$this->uploadImage($_FILES, $this->config);		   // uploads new photo
		}
		return $image;
	}

	/**
	 * Initialize user array with keys
	 * @return array: User array initialized
	 */
	public function initArrayUser()
	{
		$keys=array('id','name','email','password','description','pet','city','coder','languages','photo');
		$arrayUser=array();
		foreach($keys as $key)
			$arrayUser[$key]=NULL;
		return $arrayUser;
	}
	
	public function readUserPets($id)
	{
		$arrayPets = array();
		
		/* La consulta que sugiere Agustin es:
		$sql = "SELECT pet
				FROM users
				LEFT JOIN users_has_pets
						  ON users.iduser=users_has_pets.users_iduser
				LEFT JOIN pets
						  ON users_has_pets.pets=pets_idpet.pets.idpet
				WHERE iduser=".$user['iduser'];
		 */
		$sql = "SELECT pet
				FROM pets
				INNER JOIN users_has_pets ON
					  users_has_pets.users_iduser=".$id." AND
					  users_has_pets.pets_idpet=pets.idpet;";
		$results = parent::query($sql);
		foreach ($results as $result)
			$arrayPets[] = $result['pet'];
		return $arrayPets;
	}
	
	public function readUserLanguages($id)
	{
		$arrayLanguages = array();
		
		$sql = "SELECT language
				FROM languages
				INNER JOIN users_has_languages ON
					  users_has_languages.users_iduser=".$id." AND
					  users_has_languages.languages_idlanguage=languages.idlanguage;";
		$results = parent::query($sql);
		foreach ($results as $result)
			$arrayLanguages[] = $result['language'];
		return $arrayLanguages;
	}
	
	function readUsers()
	{
		$sql = "SELECT iduser,name,email,password,description,city,coders,photo
				FROM users
				INNER JOIN cities ON
					  users.cities_idcity=cities.idcity;";
		$arrayUsers = parent::query($sql);
		foreach($arrayUsers as $key => $user)
		{
			$arrayUsers[$key]['pets'] = implode(",",
											readUserPets($arrayUsers[$key]['iduser'],$cnx));
			$arrayUsers[$key]['languages'] = implode(",",
											readUserLanguages($arrayUsers[$key]['iduser'],$cnx));
		}
		return $arrayUsers;
	}
	
	public function readUser($id)
	{
		$sql = "SELECT *
				FROM users
				WHERE iduser='".$id."';";
		$arrayUser = parent::query($sql);
		return $arrayUser[0];
	}
	
	public function insertUser($arrayData, $imageName)
	{
		$sql="INSERT INTO users SET
				name = '".(array_key_exists('name',$arrayData) ? $arrayData['name']: '')."',
				email = '".(array_key_exists('email',$arrayData) ? $arrayData['email']: '')."',
				cities_idcity = '".(array_key_exists('city',$arrayData) ? $arrayData['city']: '')."',
				description = '".(array_key_exists('description',$arrayData) ? $arrayData['description']: '')."',
				password = '".(array_key_exists('password',$arrayData) ? $arrayData['password']: '')."',
				coders = '".(array_key_exists('coder',$arrayData) ? $arrayData['coder']: '')."',
				photo = '".$imageName."';
			 ";
		parent::query($sql);
		$sql="SELECT LAST_INSERT_ID() as id;";
		$array=parent::query($sql);
		$iduser=$array[0]['id'];
		
		foreach($arrayData['pets'] as $idpet)
		{
			$sql="INSERT INTO users_has_pets SET
					users_iduser = '".$iduser."',
					pets_idpet = '".$idpet."';
				 ";
			parent::query($sql,$cnx);
		}
		
		foreach($arrayData['languages'] as $idlanguage)
		{
			$sql="INSERT INTO users_has_languages SET
					users_iduser = '".$iduser."',
					languages_idlanguage = '".$idlanguage."';
				 ";
			parent::query($sql,$cnx);
		}
		
		return $iduser;
	}
	
	public function updateUser($arrayData, $id, $imageName)
	{
		return $numRows;
	}
	
	public function deleteUser($id)
	{
		return $numRows;
	}
	
	public function readPets()
	{
		$sql="SELECT idpet AS id, pet AS value
				FROM pets";
		$arrayPets = parent::query($sql);
		return $arrayPets;
	}
	
	public function readLanguages()
	{
		$sql="SELECT idlanguage AS id, language AS value
				FROM languages";
		$arrayLanguages = parent::query($sql);
		return $arrayLanguages;
	}
	
	public function readCoders()
	{
		//FIXME: Normalizar las tablas
		
		$sql="SELECT coder AS id, coder AS value
				FROM coders";
		$arrayCoders = parent::query($sql);
		return $arrayCoders;
	}
	
	function readCities()
	{
		$sql="SELECT idcity AS id, city AS value
				FROM cities";
		$arrayCities = parent::query($sql);
		return $arrayCities;
	}
}
?>