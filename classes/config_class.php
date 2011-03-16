<?php

	/*** Config Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1149269939328_488941_1251 ***/

	/** + Class Description +
		*
		* Configuration wrapper class.
		**/


	class Config
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: UNKNOWN!
			* The atrribute which holds the configuration array.
			**/
		var $config;

		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			*
			* The constructor.
			**/
		function Config()
		{//_9_5_1_5e60209_1149369900562_206872_1367
		}//_9_5_1_5e60209_1149369900562_206872_1367

		/** - return type: mixed
			* - param key: string
			* 	The name of the desired value.
			*
			* Returns the request value, or 'false' if the key doesn't match a value.
			**/
		function value($key)
		{//_9_5_1_5e60209_1149373461750_969085_1373
			static $cfg = array (
				"database"	=> "wiekiesjij20061018",
				"dbuser"	  => "elit",
				"dbpass"	  => "elit",
				"smsuser"	  => "",
				"smspass"	  => ""
			);

			if ( !array_key_exists ( $key, $cfg ) )
				die ( "Unknown config variable: $key" );

			return $cfg[$key];
		}//_9_5_1_5e60209_1149373461750_969085_1373

	}

?>
