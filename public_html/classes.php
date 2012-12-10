<?php

class animal
{
	public function comer()
	{
		echo "Comer";
	}
	
	protected function escupir()
	{
		echo "Warroooooo";
	}
}

class humano extends animal
{
	protected function mentir()
	{
		echo "Es que es una amiga";
	}
	
	public function depredador()
	{
		
	}
}

class user extends humano
{
	public $name='';
	protected $amante=array();
	const pi="3.14159265";
	
	function __construct($name)
	{
		$this->name =$name;
	}
	
	protected function tenerAmante()
	{
		$this->amante=array('anillo'=>self::pi);
	}
	
	public function irsedeMarcha()
	{
		$this->tenerAmante();
	}
	
	public static function hacerseElLongui()
	{
		parent::mentir();  // Tenemos que invocar el metodo mentir con el operador de
						   // resolucion porque estamos dentro de una funcion estatica!
		parent::escupir();
	}
	
	function __destruct()
	{
		$this->name="RIP".$this->name;
		$baja="57894798547";
	}
}

$usuario1 = new user('agustin');
$usuario2 = new user('sebastian');

echo $usuario1->name;
// $usuario1->tenerAmante();   // No se puede porque el metodo es protegido
$usuario1->irsedeMarcha();
$usuario1->hacerseElLongui();
echo "<pre>";
print_r($usuario1);
echo "</pre>";


user::hacerseElLongui();

?>