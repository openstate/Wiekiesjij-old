<?php

	/*** PhoneNumber Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1149164904453_874431_270 ***/

	/** + Class Description +
		*
		* This class defines a phone number with which people can vote.
		* (At the present time, only dutch (mobile) phone numbers are supported)
		**/


	class PhoneNumber
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: integer
			* The country code of the phone number. Defaults to 31 (NL)
			**/
		var $countryCode = 31;

		/** - type: string
			* The subscription part of the phone number.
			* i.e. evertyhing after the country code: 31612345678 -&gt; 612345678
			**/
		var $subscriberNumber = null;

		/** - type: boolean
			* The result of the last parse attempt, indicating whether the given number
			* was of the correct form.
			**/
		var $parseResult = false;

		/** - type: string
			* The (error) message generated in the last parse attempt, indicating
			* what the problem is, if any.
			**/
		var $parseMessage = "Number not parsed";

		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			* - param number: string
			* 	A phone number.
			*
			* The Constructor. It parses the given phone number on creation.
			**/
		function PhoneNumber($number)
		{//_9_5_1_5e60209_1149165861593_873835_360
			PhoneNumber::dump("# Creating phone number '$number'");
			$this->parse($number);
		}//_9_5_1_5e60209_1149165861593_873835_360

		/** - return type: boolean
			* - param number: string
			* 	A phone number.
			*
			* This method validates and disects the given phone number. It
			* returns true on succes, and false on failure.
			* POST: countryCode, subscriberNumber, parseResult and parseMessage are set
			* on succes.
			**/
		function parse($number)
		{//_9_5_1_5e60209_1149166185453_767208_365
			$this->parseResult = false;

			$len = strlen($number);
			if($len < 10) {
				$this->parseMessage = "Het nummer is te kort!";
				PhoneNumber::dump("- Number too short!");
				return false;
			}

			/** Stripping Country Code **/

			$offset = 0;

			if($number[$offset] == '+') {	// e.g. +31 prefix
				$this->countryCode = $number[++$offset] . $number[++$offset];
				PhoneNumber::dump("+ Country code ".$this->countryCode." found");
				$offset++;;
			}
			else if($number[$offset] == '0' && $number[$offset+1] == '0') {  // e.g. 0031
				$this->countryCode = $number[$offset+2] . $number[$offset+3];
				PhoneNumber::dump("+ Country code ".$this->countryCode." found");
				$offset += 4;
			}
			else if($number[$offset] == '0') {	// no country prefix, stripping '0'
				PhoneNumber::dump("+ Asuming country code ".$this->countryCode);
				$offset++;
			}
			else { // something else -> error
				$this->parseMessage = "$number Is een ongeldig telefoonnummer!";
				PhoneNumber::dump("- Invalid phone number!");
				return false;
			}

			/** Retrieve Phone Number **/

			$this->subscriberNumber = "";

			while($offset < $len) {
				if (is_numeric($number[$offset])) {  // All digits are oke
					$this->subscriberNumber .= $number[$offset];
				}
				else if($number[$offset] != ' ' && $number[$offset] != '\t' && $number[$offset] != '-' && $number[$offset] != '/' ) { // When weird character encountered -> error
					$this->parseResult = false;
					$this->parseMessage = "Ongeldige karakters in telefoonnummer!";
					PhoneNumber::dump("- Invalid characters found!");
					return false;
				}
				$offset++;
			}

			// Only Dutch phonenumbers are allowed
			if (strlen($this->subscriberNumber) != 9 ) {
				$this->parseResult = false;
				$this->parseMessage = "Alleen nederlandse telefoonnummers zijn toegestaan! Controleer uw invoer op typfouten a.u.b.";
				PhoneNumber::dump("- Invalid number (illegal length)!");
				return false;
			}
			else {
				$this->parseResult = true;
				PhoneNumber::dump("+ Subscriber Number: ".$this->subscriberNumber);
				return true;
			}

		}//_9_5_1_5e60209_1149166185453_767208_365

		/** - return type: string
			*
			* Returns the phone number, including the country code if they are valid,
			* 'false' otherwise.
			* PRE: countryCode and subscriberNumber are set and valid.
			**/
		function format()
		{//_9_5_1_5e60209_1149166767140_310539_371
			if($this->parseResult) {
				return $this->countryCode . $this->subscriberNumber;
			}
			else {
				return false;
			}
		}//_9_5_1_5e60209_1149166767140_310539_371

		/** - return type: boolean
			*
			* Returns the result of the last parse action. 'True' if the number was
			* succesfully parsed, 'false' otherwise.
			**/
		function isValidNumber()
		{//_9_5_1_5e60209_1149166999843_924482_375
			return $this->parseResult;
		}//_9_5_1_5e60209_1149166999843_924482_375

		/** - return type: boolean
			*
			* Determines whether the phone number belongs to a mobile phone.
			**/
		function isMobile()
		{//_9_5_1_5e60209_1149166999843_723119_377
			return $this->subscriberNumber[0] == 6;
		}//_9_5_1_5e60209_1149166999843_723119_377

		/** - return type: boolean
			* - param code: integer
			* 	A 2 digit country code.
			*
			* Determines whether the phone number matches with the given
			* country code. 'True' on succes, 'false' otherwise.
			**/
		function hasCountryCode($code)
		{//_9_5_1_5e60209_1149166999843_633981_379
			return $this->countryCode == $code;
		}//_9_5_1_5e60209_1149166999843_633981_379

		/** - return type: void
			* - param message: string
			*
			* Status message dumper.
			**/
		function dump($message)
		{//_9_5_1_5e60209_1149188915984_845866_391
			if(isset($_GET['verbose'])) {
				echo "\n<!--".$message."//-->";
			}
		}//_9_5_1_5e60209_1149188915984_845866_391

	}

?>
