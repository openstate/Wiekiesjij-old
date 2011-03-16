<?php

	/*** Voter Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1149162982031_583535_89 ***/

	/** + Class Description +
		*
		* This class describes a Voter, which is a user visiting the webpage with the intent to vote.
		**/

	require_once("phonenumber_class.php");
	require_once("dbquery_class.php");
	require_once("sms_class.php");
	require_once("poll_class.php");

	class Voter
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: integer
			* The voter database key value
			**/
		var $voterID;

		/** - type: string
			* The code which, together with the phone number, uniquely identifies
			* a voter. This code consists of a mixture of 8 numbers and digits.
			**/
		var $votingCode;

		/*+*+*+*+*+*+*+*+*  associations *+*+*+*+*+*+*+*+*/

		/** - type: PhoneNumber
			* The voter's (mobile) phone number.
			**/
		var $votingPhoneNumber;

		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			* - param phonenumber: PhoneNumber
			*
			* The constructor. The given phonenumber is used to determine
			* if the voter is known or not. If the voter is known, the voterID, votingCode
			* and votingPhoneNumber are set.
			* PRE: phoneNumber is valid.
			**/
		function Voter($phonenumber)
		{//_9_5_1_5e60209_1149163402734_348928_213
			Voter::dump("# Creating Voter");
			$this->phonenumber = $phonenumber;
		}//_9_5_1_5e60209_1149163402734_348928_213

		/** - return type: boolean
			*
			* This method sends the voter's voting code.
			**/
		function sendVotingCode()
		{//_9_5_1_5e60209_1149418011859_453831_1378
			$sms = new SMS();
			$codesms = "Uw stemcode, bestaande uit 8 cijfers en letters, is ".$this->votingCode.". Powered by hetnieuwestemmen.nl: One Mobile, One Vote";
			Voter::dump("+ Message: $codesms");
			return $sms->sendMessage($this->phonenumber->Format(),$codesms);
		}//_9_5_1_5e60209_1149418011859_453831_1378

		/** - return type: void
			* - param pollid: integer
			* 	The poll to log an sms for.
			*
			* This method logs the fact that an SMS has been sent to the voter.
			**/
		function logSms($pollid)
		{//_9_5_1_5e60209_1149515782062_468128_3013
			if(isset($_SERVER['REMOTE_ADDR']) && strlen($_SERVER['REMOTE_ADDR']) > 0 && $this->voterID != null && is_numeric($pollid)) {
				$q = new DBQuery ( "INSERT INTO ns_smslog (stemmerid,pollid,ipaddress) VALUES ('".$this->voterID."', '".$pollid."','".$_SERVER['REMOTE_ADDR']."')" );
			}
		}//_9_5_1_5e60209_1149515782062_468128_3013

		/** - return type: boolean
			*
			* This method generates an 8 character voting code which consists of lower
			* letters and numbers. The code is stored in the votingCode attribute. The method
			* returns 'true' on succes and 'false' otherwise.
			* PRE:  Phone number is valid and the votingCode attribute is not set.
			* POST: The votingCode attribute is set.
			**/
		function generateVotingCode()
		{//_9_5_1_5e60209_1149432418562_606395_1850
			if($this->phonenumber->IsValidNumber() && $this->votingCode == null) {
				$msg = $this->phonenumber . rand();
				$md5 = md5 ( $msg );
				$this->votingCode = strtolower ( base64_encode ( substr ( $md5, 0, 6 ) ) );
				Voter::dump("+ Code '".$this->votingCode."' generated");
			}
		}//_9_5_1_5e60209_1149432418562_606395_1850

		/** - return type: boolean
			* - param pollid: integer
			* 	The poll for which the vote is for.
			* - param choiceid: integer
			* 	The ID of the choice made.
			* - param votingCode: string
			* 	The voting code used to validate the vote.
			*
			* This method registers a vote for a certain poll. Returns 'true' on succes
			* and 'false' otherwise.
			* PRE:  Pollid and Choice must be valid
			**/
		function vote($pollid, $choiceid, $votingCode)
		{//_9_5_1_5e60209_1149434916046_578921_1855
			if($this->isKnown()) {
				if($pollid != null && $choiceid != null && is_numeric($pollid) && is_numeric($choiceid)) {
					$poll = new Poll($pollid);
					if ($poll->validChoice($choiceid)) { // If the choice is valid
						if ($this->votingCode == $votingCode) { // If the code is valid
							if($this->hasVoted($pollid)) {	// Vote change
								$q = new DBQuery ( "UPDATE ns_stemmen SET antwoordid=".$choiceid." WHERE pollid=".$pollid." AND stemmerid=".$this->voterID." LIMIT 1" );
								return $this->voterID;
							}
							else {	// New Vote
								$q = new DBQuery ( "INSERT INTO ns_stemmen ( pollid, stemmerid, antwoordid ) VALUES ( ".$pollid.", ".$this->voterID.", ".$choiceid.")" );
								return $this->voterID;
							}
						}
						Voter::dump("- Voting code missmatch! (".$this->votingCode." != ".$votingCode.")");
						return false;
					}
					Voter::dump("- Impossible choice!!");
					return false;
				}
				Voter::dump("- Invalid or missing choice or poll!");
				return false;
			}
			else{
				Voter::dump("- Voter not registered!");
				return false;
			}
		}//_9_5_1_5e60209_1149434916046_578921_1855

		/** - return type: boolean
			*
			* Stores the voter in the database.
			**/
		function storeVoter()
		{//_9_5_1_5e60209_1149455607687_772010_1863
			if (($this->voterID == null) && (Voter::isKnown() === false)) {		// If new voter
				if ($this->phonenumber != null && $this->phonenumber->IsValidNumber() && $this->votingCode != null) {
					$q = new DBQuery ( "INSERT INTO ns_stemmers (mobiel,stemcode) VALUES ('".$this->phonenumber->Format()."', '".$this->votingCode."')" );
					$this->voterID = $q->GetInsertID();
					Voter::dump("+ Voter stored");
				}
			}
			else {
					// Survey stuff goes here
					//$q = new DBQuery ( "UPDATE ns_stemmers SET postcode='".DBQuery::EscapeString($_POST['postcode'])."' WHERE mobiel='".$this->phonenumber->Format()."'" );
			}
		}//_9_5_1_5e60209_1149455607687_772010_1863

		/** - return type: boolean
			* - param pollid: integer
			* 	The poll to be checked for.
			*
			* Indicates whether the voter has voted for the given poll.
			**/
		function hasVoted($pollid)
		{//_9_5_1_5e60209_1149455708000_749190_1868
			if($this->voterID != null && is_numeric($pollid)) {
				$q = new DBQuery ( "SELECT * FROM ns_stemmen WHERE pollid=".$pollid." AND stemmerid=".$this->voterID."" );
				if($q->NumRows() > 0) {
					Voter::dump("+ Voter has voted for this poll");
					return true;
				}
				else {
					Voter::dump("+ Voter has not voted for this poll");
				}
			}
			Voter::dump("- Voter ID not set or pollID invalid.");
			return false;
		}//_9_5_1_5e60209_1149455708000_749190_1868

		/** - return type: boolean
			* - param pollid: integer
			* 	The poll to check against.
			*
			* This method determines whether the voter has received an SMS with
			* a voting code for the given poll.
			**/
		function receivedSMS($pollid)
		{//_9_5_1_5e60209_1149509551375_452227_3001
			if($this->voterID != null && is_numeric($pollid)) {
				$q = new DBQuery ( "SELECT * FROM ns_smslog WHERE pollid=".$pollid." AND stemmerid=".$this->voterID."" );
				if($q->numRows() > 0) {
					Voter::dump("+ Voter has received sms for this poll");
					return true;
				}
				else {
					Voter::dump("+ Voter has not received sms for this poll");
				}
			}
			else {
				Voter::dump("- Voter ID not set or pollID invalid.");
			}
			return false;
		}//_9_5_1_5e60209_1149509551375_452227_3001

		/** - return type: boolean		/** - type: UNKNOWN!
			* 	The poll for which to check.
			*
			* This method tries to determine if the new user is eligable for receiving
			* a voting code. It does so by checking the amount of messages sent
			* from the voter's IP address. This is to prevent abuse.
			* (Currently the restriction is 5 sms messages per poll per IP)
			**/
		function mayReceiveSMS($pollid)
		{//_9_5_1_5e60209_1149510265000_762123_3008
			$smsLimit = 5;
			if(isset($_SERVER['REMOTE_ADDR']) && strlen($_SERVER['REMOTE_ADDR']) > 0 && is_numeric($pollid)) {
				$address = $_SERVER['REMOTE_ADDR'];
				Voter::dump("+ Remote address: " . $address);
				$q = new DBQuery ( "SELECT * FROM ns_smslog WHERE pollid=".$pollid." AND ipaddress='".$address."'" );
				if($q->numRows() >= $smsLimit) {
					Voter::dump("- SMS limit reached! (".$q->numRows()." > ".$smsLimit.")");
					return false;
				}
				else {
					Voter::dump("+ SMS limit not reached (".$q->numRows()." <= ".$smsLimit.")");
					return true;
				}
			}
			else {
				Voter::dump("- Unkown remote address or illegal poll id! (address: ".$_SERVER['REMOTE_ADDR'].")");
				return false;
			}
		}//_9_5_1_5e60209_1149510265000_762123_3008

		/** - return type: boolean
			*
			* This method tries to determine if the voter is stored in the system by
			* checking the phone number.
			* PRE:  phonenumber has been set
			* POST: votingcode and voterID have been set if the voter is known
			**/
		function isKnown()
		{//_9_5_1_5e60209_1149456202984_164194_1871
			if($this->phonenumber != null) {
				$q = new DBQuery ( "SELECT * FROM ns_stemmers WHERE mobiel='".$this->phonenumber->Format()."'" );

				if($q->NumRows() == 1) {
					Voter::dump("+ Voter found");
					$voter = $q->Fetch();
					$this->votingCode = strtolower($voter['stemcode']);  // strtolower for safety
					$this->voterID = $voter['id'];
					Voter::dump("+ ID: ".$this->voterID);
					Voter::dump("+ Code: ".$this->votingCode);
					return true;
				}
				Voter::dump("+ New Voter");
			}
			else {
				Voter::dump("- Phone number not set!");
			}
			return false;
		}//_9_5_1_5e60209_1149456202984_164194_1871

		/** - return type: boolean
			* - param votingCode: string
			* 	The optional voting code to check against.
			*
			* This method checks whether the phone number/voting code combination is valid.
			* PRE:  phonenumber and votingcode have been set (or votingcode has been provided)
			* POST: voterID has been set if the voter is valid
			**/
		function isValid($votingCode = null)
		{//_9_5_1_5e60209_1149456202984_401190_1873
			if($this->phonenumber != null) {
				if($votingCode != null) {
					$votingCode = strtolower(trim($votingCode));
					if(strlen($votingCode) == 8) {
						$q = new DBQuery ( "SELECT * FROM ns_stemmers WHERE mobiel='".$this->phonenumber->Format()."' AND stemcode='".$votingCode."'" );
					}
					else {
						Voter::dump("-Illegal voting code");
						return false;
					}
				}
				else if($this->votingCode != null) {
					$q = new DBQuery ( "SELECT * FROM ns_stemmers WHERE mobiel='".$this->phonenumber->Format()."' AND stemcode='".$this->votingCode."'" );
				}
				else {
					Voter::dump("- No votingcode found!");
					return false;
				}
			}
			else {
				Voter::dump("- No phonenumber found!");
				return false;
			}

			if($q->NumRows() == 1) {
				Voter::dump("Voter is valid");
				$voter = $q->Fetch();
				$this->voterID = $voter['id'];
				return true;
			}
			else {
				Voter::dump("- Voter is not invalid");
				return false;
			}
		}//_9_5_1_5e60209_1149456202984_401190_1873

		/** - return type: void
			* - param message: string
			*
			**/
		function dump($message)
		{//_9_5_1_5e60209_1149458739343_617959_1882
			//$_GET['verbose'] = true;
			if(isset($_GET['verbose'])) {
				echo "\n<!--".$message."//-->";
			}
		}//_9_5_1_5e60209_1149458739343_617959_1882

	}

?>
