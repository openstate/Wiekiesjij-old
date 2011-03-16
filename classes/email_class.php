<?php

	/*** Email Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1150903209218_239145_516 ***/

	/** + Class Description +
		*
		**/


	class Email
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: string
			* The email address.
			**/
		var $address;

		/** - type: boolean
			* This attribute indicates whether the email address was validated and found to
			* be correct.
			**/
		var $valid = false;

		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			* - param address: string
			* 	The email address.
			*
			* The constructor builds an Email object and validates the provided
			* email address.
			**/
		function Email($address)
		{//_9_5_1_5e60209_1150903445781_525839_590
			$this->address = $address;
			$this->validate();
		}//_9_5_1_5e60209_1150903445781_525839_590

		/** - return type: void
			*
			* This method validates the email address stored in the 'address attribute
			* and stores the result in the 'valid' attribute.
			* POST: 'valid' is set.
			**/
		function validate()
		{//_9_5_1_5e60209_1150903709640_35795_595
			$this->address = trim(strtolower($this->address)); // eregi is case insensitive, but we want lower case anyway

			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $this->address) === false) {
 				Email::dump("Address ".$this->address." is invalid!");
 				$this->valid = false;
			}
			else {
 				Email::dump("Address ".$this->address." is valid.");
 				$this->valid = true;
			}
		}//_9_5_1_5e60209_1150903709640_35795_595

		/** - return type: boolean
			* - param subject: string
			* 	The subject of the message.
			* - param message: string
			* 	The text of the message.
			* - param from: string
			* 	An optional email address to put in the from field.
			*
			* This method send an email to the specified address. It returns 'true' on succes
			* and 'false' otherwise.
			**/
		function sendMessage($subject, $message, $from = "")
		{//_9_5_1_5e60209_1150904067343_234869_602
			if($this->valid) {
				if($from != "") {
					$fromstr  = "From: ". $from . "\r\n";
				}
				$fromstr .= "Reply-To: ". $from . "\r\n";
				$fromstr .= 'X-Mailer: PHP/' . phpversion();

				if(mail($this->address, $subject, $message, $fromstr)) {
					Email::dump("Message sent.");
					return true;
				}
				else {
					Email::dump("Could not send message to ".$this->address);
					return false;
				}
			}
		}//_9_5_1_5e60209_1150904067343_234869_602

		/** - return type: void
			* - param message: string
			* 	Message to display.
			*
			* Helper function for debugging output.
			**/
		function dump($message)
		{//_9_5_1_5e60209_1150904209531_323599_611
			if(isset($_GET['verbose'])) {
				echo "\n<!--".$message."//-->";
			}
		}//_9_5_1_5e60209_1150904209531_323599_611

	}

?>
