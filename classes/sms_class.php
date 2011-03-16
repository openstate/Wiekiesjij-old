<?php

	/*** Sms Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1149498722734_228741_2557 ***/

	/** + Class Description +
		*
		* This class is responsible for sms messages.
		**/

	require_once("config_class.php");

	class Sms
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: boolean
			* This attribute indicates whether the SMS should realy be sent ('false') or that,
			* for debugging purposes, the SMS should not actually be sent ('true').
			**/
		var $blockSend = false;

		/*+*+*+*+*+*+*+*+*  associations *+*+*+*+*+*+*+*+*/
		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			*
			* The constructor.
			**/
		function Sms()
		{//_9_5_1_5e60209_1149498796125_997705_2649
			// Set to true here, because the initial value gets overridden on code generation.
			//$this->blockSend = true;
		}//_9_5_1_5e60209_1149498796125_997705_2649

		/** - return type: boolean
			* - param phonenumber: string
			* 	The phone number to send the message to.
			* - param message: string
			* 	The message to send.
			*
			* The method sends the given message to the given number. If the message
			* is longer than 160 charcters, it will be truncated. On succes 'true' is returned and
			* 'false' otherwise.
			* PRE: The given phonenumber is valid.
			**/
		function sendMessage($phonenumber, $message)
		{//_9_5_1_5e60209_1149498975562_185929_2669
			$url  = "http://bulksms.vsms.net:5567/eapi/submission/send_sms/2/2.0".
			$url .= "?username=".Config::Value("smsuser");
			$url .= "&password=".Config::Value("smspass");
			$url .= "&msisdn=".$phonenumber;
			$url .= "&message=".urlencode($message);

			if(!$this->blockSend) {
				$f = fopen("$url", "r");
				if (!$f) {
					return false;
				}
				fclose($f);
			}

			return true;
		}//_9_5_1_5e60209_1149498975562_185929_2669

	}

?>
