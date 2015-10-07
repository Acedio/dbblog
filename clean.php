<?php // cleaning function for user provided text
	function clean_input($value){
		if(get_magic_quotes_gpc()){
			$value = stripslashes($value);
		}

		$value = mysql_real_escape_string($value);
		return $value;
	}
?>
