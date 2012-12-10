<?php

class Models_mysqlModel
{
	protected $cnx;
	private static $instance;
	
	private function __construct($config)
	{
		$this->cnx = $this->connect($config);
	}
	
	public static function singleton($config)
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className($config);
		}
		return self::$instance;
	}
	
	private function connect($config)
	{
		Models_debugModel::_debug($config);die;
	    try
	    {
	        // Create connection to MYSQL database
	        // Fourth true parameter will allow for multiple connections to be made
	        $db_connection = mysql_connect($config['database.server'],
					  					   $config['database.user'],
					  					   $config['database.password']);
	        mysql_select_db ($config['database.db']);
	        if (!$db_connection)
	            throw new Exception('MySQL Connection Database Error: ' . mysql_error());
	    }
	    catch (Exception $e)
	    {
	        echo $e->getMessage();
	    }
	
	    return $db_connection;
	}
	
	private function disconnect()
	{
		return mysql_close($this->cnx);
	}
	
	public function query($sql)
	{
		$arrayData=array();
		
	    try
	    {
			// Perform Query
			$result = mysql_query($sql,$this->cnx);
			if (!$result)
				throw new Exception('MySQL Query Error: ' . mysql_error());
			else 
			{
				if(is_resource($result))
					while($row=mysql_fetch_array($result,MYSQL_ASSOC))
					{
						$arrayData[]=$row;
					}
				else 
					$arrayData[]=$result;
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
		
		// Free the resources associated with the result set
		// This is done automatically at the end of the script
		if(is_resource($result))
			mysql_free_result($result);
		
		return $arrayData;
	}
	
	public function __destruct()
	{
		return;
	}
}
?>