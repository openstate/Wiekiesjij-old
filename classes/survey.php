<?php

	include_once "dbquery_class.php";

	class Survey
	{
		var $id = null;
		var $name;
		var $length = 0;
		var $userID = null;
		var $message = "";
		var $verbose = false;

		function Survey($surveyID, $userID)
		{
			$this->id = $surveyID;
			$this->name = "survey".$surveyID;
			$this->userID = $userID;
		}

		function showSurveyForm($action)
		{
			if ($this->userID == null) {
				echo "<p id=\"warning\">Fatale fout bij formulier constructie!</p>";
				return;
			}

			echo "<h2>Enquête</h2>";
			echo "<p>Graag zouden wij u nog enkele vragen stellen in verband met uw stem, welke wij gebruiken voor onze statistieken.</p>";
			echo "<p>De vragen gemarkeerd met <span style=\"color: #ED1C24\">*</span> zijn verplicht.";
			echo " Bij voorbaat dank voor uw medewerking!</p>";

			echo "<form action=\"".$action."\" method=\"post\" id=\"".$this->name."\">";
			echo "<input type=\"hidden\" name=\"userid\" value=\"".$this->userID."\"/>";
			echo "<input type=\"hidden\" name=\"action\" value=\"savesurvey\"/>";

			//#################################################################
			//#################################################################
			//########### LAatste 2 vragen valen even buiten de boot  #########
			//############ ivm chbox verwerking javscript !!!!!!!!!!###########
			//#################################################################


			$q = new DBQuery ( "SELECT * FROM ns_profielvragen WHERE id < 8 OR id = 10 ORDER BY verplicht DESC" );

			$this->length = $q->NumRows();

			for($i=1; $i<= $this->length; $i++) {
				$record = $q->Fetch();
				Survey::showQuestion($i,$record);
			}

			echo "<div style=\"margin: 10px; width: 110px\" class=\"vote_button\" onclick=\"postForm('".$this->name."',showSurveyResult)\">";
			echo "<img style=\"float: left\" alt=\"keuzerondje\" src=\"layout/checkbox_checked.png\"/>";
			echo "<p style=\"font-weight: bold; padding: 0 26px; margin: 0; line-height: 16px;\">verzend</p></div>";

			echo "</form>";

		}

		function showQuestion($num,$record)
		{
			echo "<p style=\"padding-bottom: 3px; border-bottom: 1px solid #999999\"><b>".$num.". </b>".$record['vraaglang'];
			if ($record['verplicht'] == 1) {
				echo " <span style=\"color: #ED1C24\">*</span>";
			}
			echo "</p><p>";

			switch ($record['type']) {
				case 1:	// Sex
					echo "<input class=\"radio\" type=\"radio\" name=\"vraag".$record['id']."\" id=\"man_vraag".$record['id']."\" value=\"1\"/> <label for=\"man_vraag".$record['id']."\">man</label><br/>";
					echo "<input class=\"radio\" type=\"radio\" name=\"vraag".$record['id']."\" id=\"vrouw_vraag".$record['id']."\" value=\"2\"/> <label for=\"vrouw_vraag".$record['id']."\">vrouw</label>";
					break;
				case 2: // Birth date
					echo "<input type=\"text\" style=\"width: 20px\" name=\"d_vraag".$record['id']."\"/> - ";
					echo "<input type=\"text\" style=\"width: 20px\" name=\"m_vraag".$record['id']."\"/> - ";
					echo "<input type=\"text\" style=\"width: 40px\" name=\"y_vraag".$record['id']."\"/> (dd-mm-jjjj)";
					break;
				case 3:
					echo "<input style=\"width: 30px\" type=\"text\" name=\"vraag".$record['id']."\"/>";
				case 4:
					break;
				case 10:
					Survey::showOptions($record['id'],true);
					break;
				case 11:
					Survey::showOptions($record['id'],false);
					break;
				default:

			}
			echo "</p>";
		}

		function showOptions($id, $radio = true)
		{
			$q = new DBQuery ( "SELECT * FROM ns_keuzes WHERE profielvraagid=" . $id );
			for($i=1; $i<= $q->NumRows(); $i++) {
				$record = $q->Fetch();
				if ($radio) {
					echo "<input class=\"radio\" type=\"radio\" value=\"".$record['id']."\" name=\"vraag".$id."\" id=\"antwoord".$id."_".$i."\"/>";
				}
				else {
					echo "<input class=\"checkbox\" type=\"checkbox\" value=\"".$record['id']."\" name=\"vraag".$id."[]\" id=\"antwoord".$id."_".$i."\"/>";
				}
				echo "<label for=\"antwoord".$id."_".$i."\">".$record['tekst']."</label><br/>";
			}
		}

		function processSurvey($postdata)
		{
			if($this->verbose) {
				echo "<pre>";
				print_r($postdata);
				echo "</pre>";
			}

			$sql = array();

			$mq = new DBQuery ( "SELECT * FROM ns_profielvragen ORDER BY verplicht DESC" );
			$this->length = $mq->NumRows();

			for($i=1; $i<= $this->length; $i++) {
				$record = $mq->Fetch();

				switch ($record['type']) {
					case 1:	// Sex
						// Check data
						if ($record['verplicht'] == 1 && !isset($postdata['vraag'.$record['id']])) {
							Survey::dump("U bent vergeten uw geslacht te vermelden!");
							return false;
						}
						$sql[] = "UPDATE ns_stemmers SET geslacht = ".$postdata['vraag'.$record['id']]." WHERE id = ".$this->userID." LIMIT 1";
						//Survey::dump($sql);
						break;
					case 2:	 // Birth date
						// Check data
						if ($record['verplicht'] == 1) {
							if($postdata['d_vraag'.$record['id']] == "" || $postdata['m_vraag'.$record['id']] == "" || $postdata['y_vraag'.$record['id']] == "") {
								Survey::dump("U bent vergeten uw geboortedatum te vermelden!");
								return false;
							}
						}
						// Check date
						if (!checkdate($postdata['m_vraag'.$record['id']],$postdata['d_vraag'.$record['id']],$postdata['y_vraag'.$record['id']])) {
							Survey::dump("Ongeldige geboortedatum!");
							return false;
						}
						$sql[] = "UPDATE ns_stemmers SET geboortedatum = '".$postdata['y_vraag'.$record['id']]."-".$postdata['m_vraag'.$record['id']]."-".$postdata['d_vraag'.$record['id']]."' WHERE id = ".$this->userID." LIMIT 1";
						//Survey::dump($sql);
						break;
					case 3:	 // Zip code
						// Check data
						if ($record['verplicht'] == 1 && $postdata['vraag'.$record['id']] == "") {
							Survey::dump("U bent vergeten uw postcode in te vullen!");
							return false;
						}
						// Check zip code
						if (strlen($postdata['vraag'.$record['id']]) != 4 || !is_numeric($postdata['vraag'.$record['id']]) || $postdata['vraag'.$record['id']] < 1000) {
							Survey::dump("Ongeldige postcode!");
							return false;
						}
						$sql[] = "UPDATE ns_stemmers SET postcode = '".$postdata['vraag'.$record['id']]."' WHERE id = ".$this->userID." LIMIT 1";
						//Survey::dump($sql);
						break;
					case 4:	 // Email
						break;
					case 11: // Checkbox (many out of multiple)
						if ($record['verplicht'] == 1 && !isset($postdata['vraag'.$record['id']])) {
							Survey::dump("U bent vergeten de vraag '".$record['vraaglang']."' te beantwoorden!");
							return false;
						}

						if(isset($postdata['vraag'.$record['id']])) {
							foreach($postdata['vraag'.$record['id']] as $value) {
								$sql[] = "INSERT INTO ns_profielantwoorden (stemmerid, profielvraagid, keuzeid) VALUES (".$this->userID.",".$record['id'].",".$value.")";
								//Survey::dump($sql);
							}
						}
						break;
					case 10:	// Radio falls under default
					default:
						if ($record['verplicht'] == 1 && !isset($postdata['vraag'.$record['id']])) {
							Survey::dump("U bent vergeten de vraag '".$record['vraaglang']."' te beantwoorden!");
							return false;
						}

						if(isset($postdata['vraag'.$record['id']])) {
							$sql[] = "INSERT INTO ns_profielantwoorden (stemmerid, profielvraagid, keuzeid) VALUES (".$this->userID.",".$record['id'].",".$postdata['vraag'.$record['id']].")";
							//Survey::dump($sql);
						}
				}
			}

			foreach($sql as $query) {
				$save = new DBQuery($query);
			}

			return true;
		}

		function dump($msg)
		{
			if($this->verbose){
				echo "\n<!--".$msg."//-->";
			}
			else {
				$this->message = $msg;
			}
		}

	}
?>
