<?php
class Models_applicationModel
{
	
	/** 
	 *  Returns the ancestors of a context plus itself (oldest first)
	 *  @param array $arrayConfigs Array of all configs per context
	 *  @param string $context Context
	 *  @return array: Context ancestors array
	 */
	private static function contextAncestors($arrayConfigs, $context)
	{
		$ancestors = array();
	
		foreach($arrayConfigs as $key => $value)
		{
			if(strpos($key,':')===FALSE)
				$currentContext = trim($key);
			else
				$currentContext = trim(strstr($key,':',TRUE));
			if (strpos($currentContext, $context)!==FALSE) // $context found!
			{
				$parent = strstr($key,':'); // look for parent
				if ($parent!==FALSE)            // if it has parent, look for ancestors
					$ancestors = self::contextAncestors($arrayConfigs,trim(substr($parent,1)));
				$ancestors[]= $key;	// appends itself to the list of ancestors
				return $ancestors;
			}
		}	
		return $ancestors;
	}
	
	/** 
	 *  Read config file to array
	 *  @param string $configFile Config filename
	 *  @param string $context Context
	 *  @return array: Config context array
	 */
	public static function readConfig($configFile, $context)
	{
		$arrayConfigs = array();
		
		$arrayConfigs = parse_ini_file($configFile, true);
		$ancestors = self::contextAncestors($arrayConfigs,$context);
		
		$arrayConfig = array();
		foreach($ancestors as $ancestor)
			$arrayConfig = array_merge($arrayConfig,$arrayConfigs[$ancestor]);
		
		return $arrayConfig;
	}
	
	/**
	 * Renders a view
	 * @param string $view View to be rendered
	 * @param array $params Parameters of the view
	 * @param array $config Config variables
	 * @return string Rendered view
	 */
	public function renderView($view, array $params, $config)
	{
		ob_start();
		include($config['viewsDirectory']."/".$view.".php");
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	public function renderLayout($layout, array $params, $config)
	{
		ob_start();
		include($config['layoutsDirectory']."/".$layout.".php");
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	public static function setRequest()
	{
		$uri=(explode('/', $_SERVER['REQUEST_URI']));
		
		if($uri[1]!='')
			if(file_exists(APPLICATION_PATH."/controllers/".$uri[1].".php"))
			{
				if(isset($uri[1]))
					$_GET['controller']=$uri[1];
				else
					$_GET['controller']='index';
				if(isset($uri[2]))
					$_GET['action']=$uri[2];
				else
					$_GET['action']='index';
				if(isset($uri[3]))
					$_GET['id']=$uri[3];
			}
			else
			{
				$_GET['controller']='error';
				$_GET['action']='404';
			}
		else
		{
			$_GET['controller']='index';
			$_GET['action']='index';
		}
		$arrayRequest=array('controller'=>$_GET['controller'],
							'action'=>$_GET['action']);
		
		return $arrayRequest;
	}
	
	public static function acl($arrayRequest, $config)
	{
		$objDB=$_SESSION['register']['db'];
		$sql="SELECT resource
				FROM resources
				LEFT JOIN roles_has_resources
						ON resources.idresource=roles_has_resources.resources_idresource
				WHERE roles_has_resources.roles_idrole='".$_SESSION[$config['sessionNamespace']]['user_role']."';";
		$resources = $objDB->query($sql);
		$arrayResources = array();
		
		foreach ($resources as $resource)
		{
			$arrayResources[]=$resource['resource'];
		}
		
		if(in_array("/".$arrayRequest['controller']."/".$arrayRequest['action'], $arrayResources))
			$arrayRequest=$arrayRequest;
		elseif (in_array("/".$arrayRequest['controller'], $arrayResources))
				$arrayRequest=$arrayRequest;
		else {
			$arrayRequest['controller']='error';
			$arrayRequest['action']='403';
		}
		return $arrayRequest;
	}
}
?>