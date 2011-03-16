<?php

	/*** Action Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1149503216500_198722_2747 ***/

	/** + Class Description +
		*
		* This method performs (inter)actions with the voter.
		**/

	require_once("phonenumber_class.php");
	require_once("voter_class.php");

	class Action
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: string
			* The name of the last/current action.
			**/
		var $action;

		/** - type: boolean
			* This attribute indicates whether the last action was a succes or not. On succes it's value
			* is set to '1' or higher, on failure it's set to '0'.
			**/
		var $status = 0;

		/** - type: string
			* This attribute contains a message clarifying the current status of the action.
			**/
		var $message;

		/*+*+*+*+*+*+*+*+*  associations *+*+*+*+*+*+*+*+*/
		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			*
			* The constructor tries to determine the action requested by the voter, and execute it.
			**/
		function Action()
		{//_9_5_1_5e60209_1149503459218_128306_2803
			Action::dump("# Creating Action");
			Action::getAction();
			Action::switchToAction();
		}//_9_5_1_5e60209_1149503459218_128306_2803

		/** - return type: string
			*
			* Actions are defined through the super globals $_GET['action'] or $_POST['action].
			* If this method is able to determine the action, it returns the action name as a string.
			* The value 'false' is returned otherwise.
			**/
		function getAction()
		{//_9_5_1_5e60209_1149503709937_591737_2808
			if(isset($_GET['action']) && strlen($_GET['action']) > 0) {
				$this->action = $_GET['action'];
				Action::dump("+ GET Action is: ".$this->action);
			}
			else if(isset($_POST['action']) && strlen($_POST['action']) > 0) {
				$this->action = $_POST['action'];
				Action::dump("+ POST Action is: ".$this->action);
			}
			else {
				$this->action = null;
				Action::dump("- No action found!");
			}
		}//_9_5_1_5e60209_1149503709937_591737_2808

		/** - return type: void
			*
			* This method switches to the given action, or does dies if the action is unkown.
			**/
		function switchToAction()
		{//_9_5_1_5e60209_1149503709937_403889_2810
			switch($this->action) {
				case "votevrk":
					$phonenumber = new PhoneNumber($_GET['phonenumber']);
					$voter = new Voter($phonenumber);
					if($voter->isValid($_GET['votingcode'])) {
						$this->status = 1;
						$this->message = "Stemcode oke";

						if(isset($_GET['candidates'])) {
					 		$candidates = explode(";",$_GET['candidates']);
					 		$numcan = count($candidates);
					 		if($numcan > 0 && $numcan < 6) {
					 			require_once("classes/dbquery_class.php");
					 			$q = new DBQuery ( "DELETE FROM ns_stemmen WHERE stemmerid = ".$voter->voterID." AND pollid = 666" );
					 			foreach($candidates as $value) {
					 				if(is_numeric($value)) {
					 					$sql = "INSERT INTO ns_stemmen (stemmerid, pollid, antwoordid) VALUES (".$voter->voterID.",666,$value)";
					 					//$this->message .= $sql;
					 					$q = new DBQuery($sql);
					 				}
					 			}
					 		}
					 	}

					}
					else {
						$this->message = "Ongeldige stemcode!";
					}
					$this->xmlReply();
					break;
				case "sendcode":		// Send a voting code if possible.
					$this->sendVotingCode();
					break;
				case "checkcode":		// Checks whether the given phonenumber and voting code match.
					$phonenumber = new PhoneNumber($_GET['phonenumber']);
					$voter = new Voter($phonenumber);
					if($voter->isValid($_GET['votingcode'])) {
						$this->status = 1;
						$this->message = "Stemcode oke";
					}
					else {
						$this->message = "Ongeldige stemcode!";
					}
					$this->xmlReply();
					break;
				case "savesurvey":
					require_once("classes/survey.php");
					if (!isset($_POST['userid'])) {
						$this->message = "No user found!";
						$this->xmlReply();
						die;
					}

					$survey = new Survey(0,$_POST['userid']);
					// Suvery ID should be added in future!
					if($survey->processSurvey($_POST) === false) {
						$this->message = $survey->message;
					}
					else {
						$this->status = 1;
						$this->message = "Enquete opgeslagen";
					}
					$this->xmlReply();
					break;
				case "cascade":
					// Should be integrated into cascade class
					require_once("classes/email_class.php");
					$mail = new Email($_POST['emailaddress']);
					$sender = trim($_POST['sender']);

					$message = <<<END
Geachte heer/mevrouw,

$sender wil graag de website www.d66lijsttrekker.nl onder uw aandacht brengen.
Op de website kunnen alle Nederlanders gratis en anoniem via hun mobiele telefoonnummer
stemmen op een kandidaatlijsttrekker van D66.

Hoogachtend,

Stichting Het Nieuwe Stemmen
END;
					if($mail->sendMessage("Uitnodiging van ".$sender,$message,"info@hetnieuwestemmen.nl")) {
						$this->status = 1;
						$this->message = "De uitnodiging is verstuurd. Vul een nieuw e-mailadres in om nog iemand uit te nodigen!";
					}
					else {
						$this->message = "De uitnodiging kon niet verstuurd worden. Controlleer a.u.b. het adres.";
					}
					$this->xmlReply();
					break;
				case "testvkr":	// TEMP TEST
					testVrk();
					break;
				case "aanmelden":
					$this->saveGros();
					break;
				case "storequestionaire":
					require_once "questionaire_class.php";
					$ques = new Questionaire("wiekiesjij");
					$this->message = $ques->saveAnswers();
					if($this->message === true) {
						$this->status = 1;
						$this->message = "Uw gegevens zijn opgeslagen.";
					}
					$this->xmlReply();
					break;
				default:
					$this->message = "Error: unkown action! ".$this->action;
					$this->xmlReply();
					Action::dump("- Unknown action! Fatal error...");
					die;
			}
		}//_9_5_1_5e60209_1149503709937_403889_2810

		/** - return type: void
			*
			* This method outputs xml reflecting the outcome of a desired action.
			**/
		function xmlReply()
		{//_9_5_1_5e60209_1149503924890_692260_2947
			// Send response XML
			@header("Content-Type: text/xml; charset=iso-8859-1");
			echo "<?xml version='1.0' encoding=\"iso-8859-1\"?>\n";
			echo "<result>";
			echo "<status>".$this->status."</status>";
			echo "<message>".$this->message."</message>";
			if($xhtml != null) {
				echo "<xhtml>";
				echo $xhtml;
				echo "</xhtml>";
			}
			echo "</result>";
		}//_9_5_1_5e60209_1149503924890_692260_2947

		/** - return type: boolean
			*
			* This method tries to send a voting code to the user.
			**/
		function sendVotingCode()
		{//_9_5_1_5e60209_1149503924890_713820_2945
			Action::dump("+ Action: sendVotingCode");
			if (!isset($_GET['phonenumber'])) {
				Action::dump("- No phone number received!");
				$this->message = "Geen telefoonnummer ontvangen!";
			}
			else {	// Start number check
				$phonenumber = new PhoneNumber($_GET['phonenumber']);
				if (!$phonenumber->IsValidNumber()) {
					$this->message = $phonenumber->ParseMessage;
				}
				else if (!$phonenumber->isMobile()) {
					$this->message = "Alleen mobiele telefoonnummers zijn toegestaan!";
					Action::dump("- Not a mobile phone number!");
				}
				else if (!$phonenumber->hasCountryCode(31)) {
					$this->message = "U kunt alleen stemmen met een Nederlands mobiel nummer (+31)! U gebruikt landcode +".$phonenumber->CountryCode;
					Action::dump("- Non dutch number!");
				}
				else { // Phone number is oke

					// Check if the poll is valid
					if(is_numeric($_GET['pollid'])) {
						$q = new DBQuery ( "SELECT * FROM ns_poll WHERE actief=1 AND id=".$_GET['pollid'] );
						if($q->numRows() != 1) {
							$this->message = "Fatal error!";
							$this->xmlReply();
							die;
						}
					}
					else {
						$this->message = "Invalid poll!";
						$this->xmlReply();
						die;
					}

					$voter = new Voter($phonenumber);
					if($voter->isKnown()) {	// Known user
						if($voter->receivedSMS($_GET['pollid'])) {	// If sms for current poll already sent
							$this->message = "Vul uw stemcode in om uw stem te wijzigen.";
							$this->status = 1;
							Action::dump("---- ENABLE CODE FIELD");
						}
						else if (isset($_GET['resend'])) {	// If reqeust is for re-send
							Action::dump("+ Resend voting code");
							if($voter->sendVotingCode($_GET['pollid'])) {
								$voter->logSms($_GET['pollid']);
								$this->message = "Uw stemcode is opnieuw verzonden. Vul deze in om uw stem te bevestigen.";
								$this->status = 1;
								Action::dump("---- WAIT FOR ACTUAL VOTE");
							}
							else {
								$this->message = "Uw stemcode kan op dit moment helaas niet verzonden worden. Probeer het later a.u.b. opnieuw.";
								Action::dump("---- SMS ERROR");
							}
						}
						else {
							$this->message = "Vul uw stemcode in om te stemmen. Bent u uw stemcode vergeten? Klik dan op de knop om deze nogmaals ge-sms't te krijgen.";
							$this->status = 2;
							Action::dump("---- SHOW RESEND BUTTON");
							Action::dump("---- ENABLE CODE FIELD");
						}
					}
					else { // New user -> check IP
						if($voter->mayReceiveSMS($_GET['pollid'])) {
							$voter->generateVotingCode();
							if($voter->sendVotingCode($_GET['pollid'])) {
								$voter->storeVoter();
								$voter->logSms($_GET['pollid']);
								$this->status = 1;
								$this->message = "Uw stemcode is verzonden. Vul deze in om uw stem te bevestigen.";
								Action::dump("---- ENABLE CODE FIELD");
							}
							else {
								$this->message = "Uw stemcode kan op dit moment helaas niet verzonden worden. Probeer het later a.u.b. opnieuw.";
								Action::dump("---- SMS ERROR");
							}
						}
						else {
							$this->message = "De sms-limiet voor uw IP adres is bereikt.";
						}
					}
				}
			}
			$this->xmlReply();
		}//_9_5_1_5e60209_1149503924890_713820_2945

		/** - return type: void		/** - type: UNKNOWN!
			*
			* Debugging aid.
			**/
		function dump($message)
		{//_9_5_1_5e60209_1149506699562_817172_2996
			//$_GET['verbose'] = true;
			if(isset($_GET['verbose'])) {
				echo "\n<!--".$message."//-->";
			}
		}//_9_5_1_5e60209_1149506699562_817172_2996

	}

?>
