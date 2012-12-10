<?php

class models_debugModel
{
	/**
	 * Blah blah
	 * @param array $data Data to be shown
	 */
	public static function _debug($data, $label=null)
	{
		echo "<pre>";
		if(isset($label))
			echo $label.": "."\n";
		print_r($data);
		echo "</pre>";
	}
}