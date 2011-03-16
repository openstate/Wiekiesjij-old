<?php

	/*** Questionaire Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1159649371656_730329_0 ***/

	/** + Class Description +
		*
		* Question types:
		* text:          short text, like a name
		* blob:          long text
		* radio:         select one out many
		* date:          a date
		* select:        select one out many
		* checkbox:  select many out many
		* subheader:  Can be used to put sub-headers into the form
		* warning:     Can be used to add warnings like disclaimers etc.
		**/

	require_once("dbquery_class.php");

	class Questionaire
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: integer
			* The integer id of the questionaire.
			**/
		var $id;

		/** - type: string
			* Name of the questionaire.
			**/
		var $name;

		/** - type: string
			* Description of the questionaire.
			**/
		var $description;

		/** - type: UNKNOWN!
			* An array of the different question groups belonging to the questionaire.
			**/
		var $groups = array();

		/** - type: string[]
			* An array describing the hidden inputs to be put into the questionaire form.
			**/
		var $hiddeninput;

		/** - type: integer
			* The integer id of the entity which owns the questionaire.
			**/
		var $owner = false;

		/** - type: integer
			* The number of buttons within the generated form. Used to create unique XHTML id's
			**/
		var $buttonCount = 0;

		/** - type: boolean
			* Indicates whether the questionaire form should be enabled.
			**/
		var $enabled = true;

		/*+*+*+*+*+*+*+*+*  associations *+*+*+*+*+*+*+*+*/
		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			* - param name: string
			* 	The name of the questionaire to build.
			*
			* Contructor.
			**/
		function Questionaire($name)
		{//_9_5_1_5e60209_1159649814812_398025_106
			Questionaire::dump("+ Creating Questionaire object for '$name'...");
			$q = new DBQuery("SELECT * FROM ns_questionaire WHERE name='$name'");
			if($q->numRows() != 1) {
				Questionaire::dump("- Error in loading questionaire!");
				Questionaire::dump("- Expected 1 result on '$name', but found ".$q->numRows);
				die;
			}
			else {
				$r = $q->fetch();
				$this->id = $r['id'];
				$this->name = $r['name'];
				$this->description = $r['description'];
				Questionaire::dump("+ Questionaire '$name' with id ".$this->id." found: ".$this->description);
				Questionaire::loadGroups();
			}
		}//_9_5_1_5e60209_1159649814812_398025_106

		/** - return type: void
			* - param owner: integer
			* 	The integer id of the entity to which the questionaire belongs.
			* - param enabled: boolean
			* 	Indicates whether the form in enabled.
			*
			* This method writes an XHTML form of the questionaire.
			**/
		function build($owner = false, $enabled = true)
		{//_9_5_1_5e60209_1159650779609_326604_177
			Questionaire::dump("+ Building form for owner $owner");

			//define("FORCE",1);

			list($usec, $sec) = explode(" ", microtime());
   	 	$start = ((float)$usec + (float)$sec);

			$this->owner = $owner;
			$this->enabled = $enabled;

			echo "\n<form action=\"\" method=\"post\" id=\"questionaireform\">";
			echo "\n<div>";
			// Adding hidden inputs
			foreach($this->hiddeninput as $key => $val) {
				echo "\n<input type=\"hidden\" name=\"$key\" id=\"$key\" value=\"$val\"/>";
			}
			if($this->owner !== false) {
				echo "\n<input type=\"hidden\" name=\"questionaireowner\" id=\"questionaireowner\" value=\"".$this->owner."\"/>";
			}
			echo "\n</div>";

			// Adding question groups

			foreach($this->groups as $key => $val) {
				$this->buildGroup($key, $val);
			}

			echo "\n</form>";

			list($usec, $sec) = explode(" ", microtime());
   	  $end = ((float)$usec + (float)$sec);

			echo "<p class=\"subhint\">Enquête gegenereerd in ".sprintf("%0.2f",($end - $start))."s.</p>";
		}//_9_5_1_5e60209_1159650779609_326604_177

		/** - return type: void
			* - param groupid: integer
			* 	The id of the group to be built.
			* - param groupname: string
			* 	The name of the group.
			*
			* This method writes the question group for the group id provided.
			**/
		function buildGroup($groupid, $groupname)
		{//_9_5_1_5e60209_1159771862250_580667_102
			echo "<div class=\"attention\">".htmlspecialchars($groupname)."</div>";
			$q = new DBQuery("SELECT ns_question_link.*, ns_question.* FROM ns_question_link LEFT JOIN ns_question ON (ns_question.id = ns_question_link.question_id) WHERE question_group_id=".$groupid." ORDER BY sortorder");

			if($q->numRows() > 0 ){
				echo "<table>";
				while($r = $q->fetch()) {
					Questionaire::dump($r['question']);
					switch($r['type']) {
						case 'text':
							$this->buildTextField($r);
							break;
						case 'blob':
							$this->buildTextArea($r);
							break;
						case 'radio':
							$this->buildRadioButtons($r);
							break;
						case 'date':
							$this->buildDate($r);
							break;
						case 'select':
							$this->buildSelectBox($r);
							break;
						case 'checkbox':
							$this->buildCheckBoxes($r);
							break;
						case 'subheader':
							echo "<tr><th colspan=\"2\">".$r['question']."</th></tr>";
							break;
						case 'warning':
							echo "<tr><th style=\"text-align: center; color: red; font-size: 14px\" colspan=\"2\">".$r['question']."</th></tr>";
							break;
						default:
							Questionaire::dump("- Unkown question type!");
							die;
					}
				}
				echo "</table>";
				$this->buildSubmitButton();
			}
		}//_9_5_1_5e60209_1159771862250_580667_102

		/** - return type: void
			* - param question: array
			*
			* Write an XHTML form text field.
			**/
		function buildTextField($question)
		{//_9_5_1_5e60209_1159774535328_880334_110
			if($this->owner !== false) {
				Questionaire::dump("+ Searching value for owner");
				$q = new DBQuery("SELECT ".$question['destination_field']." FROM ".$question['destination_table']." WHERE ".$question['destination_idfield']."='".$this->owner."'");
				$r = $q->fetchIndex();
				$val = $r[0];
			}
			else {
				$val = "";
			}

			if($val == '') {
				$rowstyle = "background-color: #FCE2E3";
			}

			if(!$this->enabled) {
				$enabled = "disabled=\"disabled\"";
			}

			echo "<tr style=\"$rowstyle\"><td class=\"label\">".htmlspecialchars($question['question'])."</td><td><input $enabled style=\"".$question['style']."\" class=\"string\" type=\"text\" name=\"".$question['name']."\" id=\"".$question['name']."\" value=\"$val\" /><small>".htmlspecialchars($question['extra'])."</small></td></tr>\n";
		}//_9_5_1_5e60209_1159774535328_880334_110

		/** - return type: void
			* - param question: array
			* 	An array describing the question.
			*
			* This method write an XHTML textarea.
			**/
		function buildTextArea($question)
		{//_9_5_1_5e60209_1159898118453_274159_247
			if($this->owner !== false) {
				Questionaire::dump("+ Searching value for owner");
				$q = new DBQuery("SELECT ".$question['destination_field']." FROM ".$question['destination_table']." WHERE ".$question['destination_idfield']."='".$this->owner."'");
				$r = $q->fetchIndex();
				$val = $r[0];
			}
			else {
				$val = "";
			}

			if($val == '') {
				$rowstyle = "background-color: #FCE2E3";
			}

			if(!$this->enabled) {
				$enabled = "disabled=\"disabled\"";
			}

			echo "<tr style=\"$rowstyle\"><td class=\"label\">".htmlspecialchars($question['question'])."</td><td>";
			echo "<textarea $enabled style=\"".$question['style']."\" rows=\"10\" cols=\"45\" name=\"".$question['name']."\" id=\"".$question['name']."\">".$val."</textarea>";
			if(strlen($question['extra']) > 0) {
				echo "<small>".htmlspecialchars($question['extra'])."</small>";
			}
			echo "</td></tr>\n";
		}//_9_5_1_5e60209_1159898118453_274159_247

		/** - return type: void
			* - param question: array
			*
			* Builds a radio button selection.
			**/
		function buildRadioButtons($question)
		{//_9_5_1_5e60209_1159782666890_669650_206
			if($this->owner !== false) {
				Questionaire::dump("+ Searching value for owner");
				$q = new DBQuery("SELECT ".$question['destination_field']." FROM ".$question['destination_table']." WHERE ".$question['destination_idfield']."='".$this->owner."'");
				$r = $q->fetchIndex();
				$val = $r[0];
			}
			else {
				$val = "";
			}

			if($val == '' || $val == 0) {
				$rowstyle = "background-color: #FCE2E3";
			}

			if(!$this->enabled) {
				$enabled = "disabled=\"disabled\"";
			}

			echo "<tr style=\"$rowstyle\"><td class=\"label\">".htmlspecialchars($question['question'])."</td><td>";

			if($question['answer_source'] == 'default') {
				$q = new DBQuery("SELECT ns_question_answer_link.*, ns_question_answer.* FROM ns_question_answer_link LEFT JOIN ns_question_answer ON(id = question_answer_id) WHERE question_id = " .$question['id']);
				while($r = $q->fetch()) {
					if ($val == $r['value']) {
						$chk = "checked =\"checked \"";
					}
					else {
						$chk = "";
					}
					echo "<input $enabled $chk type=\"radio\" class=\"radio\" id=\"".$question['name'].$r['value']."\" value=\"".$r['value']."\" name=\"".$question['name']."\"/><label for=\"".$question['name'].$r['value']."\">".htmlspecialchars($r['label'])."</label><br/>";				}
			}

			echo "</td></tr>\n";
		}//_9_5_1_5e60209_1159782666890_669650_206

		/** - return type: void
			* - param question: array
			* 	The array containing the question definition.
			*
			* Builds an XHTML select box.
			**/
		function buildSelectBox($question)
		{//_9_5_1_5e60209_1159790748640_551730_227

			if($this->owner !== false) {
				Questionaire::dump("+ Searching value for owner");
				$q = new DBQuery("SELECT ".$question['destination_field']." FROM ".$question['destination_table']." WHERE ".$question['destination_idfield']."='".$this->owner."'");
				$r = $q->fetchIndex();
				$val = $r[0];
				Questionaire::dump("+ Value is $val");
			}
			else {
				$val = "";
			}

			if($val == '' || $val == 0) {
				$rowstyle = "background-color: #FCE2E3";
			}

			if(!$this->enabled) {
				$enabled = "disabled=\"disabled\"";
			}

			echo "<tr style=\"$rowstyle\"><td class=\"label\">".htmlspecialchars($question['question'])."</td><td>";
			echo "<select $enabled style=\"".$question['style']."\" name=\"".$question['name']."\" id=\"".$question['name']."\">";
			echo "<option value=\"0\"></option>";

			if($question['answer_source'] == 'default') {
				$q = new DBQuery("SELECT ns_question_answer_link.*, ns_question_answer.* FROM ns_question_answer_link LEFT JOIN ns_question_answer ON(id = question_answer_id) WHERE question_id = " .$question['id']." ORDER BY sortorder");
				while($r = $q->fetch()) {
					if ($val == $r['value']) {
						$chk = "selected =\"selected \"";
					}
					else {
						$chk = "";
					}
					echo "<option $chk value=\"".$r['value']."\">".htmlspecialchars($r['label'])."</option>";
				}
			}
			else if($question['answer_source'] == 'external') {
				$q = new DBQuery("SELECT ".$question['answer_idfield'].",".$question['answer_value'].",".$question['answer_label']." FROM ".$question['answer_table']." ORDER BY ".$question['answer_label']);
				while($r = $q->fetchIndex()) {
					if ($val == $r[1]) {
						$chk = "selected =\"selected \"";
					}
					else {
						$chk = "";
					}
					echo "<option $chk value=\"".$r[1]."\">".$r[2]."</option>";
				}
			}

			echo "</select></td></tr>\n";
		}//_9_5_1_5e60209_1159790748640_551730_227

		/** - return type: void
			* - param question: array
			* 	The question definition.
			*
			* Builds a series of XHTML checkboxes.
			**/
		function buildCheckBoxes($question)
		{//_9_5_1_5e60209_1159794364093_741328_241
			$val = array();
			if($this->owner !== false) {
				Questionaire::dump("+ Searching values for owner");
				if($question['answer_source'] == 'default') {
					// Searches in the default storage 'ns_question_answered', should be made to include an alternate source.
					$q = new DBQuery("SELECT value FROM ns_question_answered LEFT JOIN ns_question_answer ON(ns_question_answer.id = ns_question_answered.answer_id) WHERE participant_id ='".$this->owner."' AND question_id=".$question['id']);
					$present = $q->numRows() > 0;
					while($r = $q->fetch()) {
						$val[] = $r['value'];
					}
				}
				else if($question['answer_source'] == 'external') {
					$sql = "SELECT ".$question['answer_table'].".".$question['answer_value']." AS value FROM ns_question_answered LEFT JOIN  ".$question['answer_table']." ON(".$question['answer_table'].".".$question['answer_idfield']." = ns_question_answered.answer_id) WHERE participant_id ='".$this->owner."' AND question_id=".$question['id'];
					$q = new DBQuery($sql);
					$present = $q->numRows() > 0;
					while($r = $q->fetch()) {
						$val[] = $r['value'];
					}
				}
			}

			if($present === false) {
				$rowstyle = "background-color: #FCE2E3";
			}

			if(!$this->enabled) {
				$enabled = "disabled=\"disabled\"";
			}

			echo "<tr style=\"$rowstyle\"><td class=\"label\">".htmlspecialchars($question['question'])."</td><td>";

			// Hack: hidden entry for when all visible checkboxes are unchecked. Now, the array at the server always at least has length 1
			echo "<input type=\"hidden\" id=\"".$question['name']."\" value=\"-1\" name=\"".$question['name']."[]\"/>";
			if($question['answer_source'] == 'default') {
				$q = new DBQuery("SELECT ns_question_answer_link.*, ns_question_answer.* FROM ns_question_answer_link LEFT JOIN ns_question_answer ON(id = question_answer_id) WHERE question_id = " .$question['id']." ORDER BY ns_question_answer.label");
				while($r = $q->fetch()) {
					if (array_search($r['value'],$val) !== false) {
						$chk = "checked =\"checked \"";
					}
					else {
						$chk = "";
					}
					echo "<input $enabled $chk type=\"checkbox\" class=\"checkbox\" id=\"".$question['name'].$r['value']."\" value=\"".$r['value']."\" name=\"".$question['name']."[]\"/><label for=\"".$question['name'].$r['value']."\">".htmlspecialchars($r['label'])."</label><br/>";
				}
			}
			else if($question['answer_source'] == 'external') {
				$q = new DBQuery("SELECT ".$question['answer_idfield'].",".$question['answer_value'].",".$question['answer_label']." FROM ".$question['answer_table']." ORDER BY ".$question['answer_label']);
				while($r = $q->fetchIndex()) {
					if (array_search($r[1],$val) !== false) {
						$chk = "checked =\"checked \"";
					}
					else {
						$chk = "";
					}
					echo "<input $enabled $chk type=\"checkbox\" class=\"checkbox\" id=\"".$question['name'].$r[1]."\" value=\"".$r[1]."\" name=\"".$question['name']."[]\"/><label for=\"".$question['name'].$r[1]."\">".htmlspecialchars($r[2])."</label><br/>";
				}
			}
			echo "</td></tr>\n";
		}//_9_5_1_5e60209_1159794364093_741328_241

		/** - return type: void
			* - param question: array
			* 	An array containing info about he question.
			*
			* Builds a date entry form element.
			**/
		function buildDate($question)
		{//_9_5_1_5e60209_1159788936000_291897_221
			if($this->owner !== false) {
				Questionaire::dump("+ Searching value for owner");
				$q = new DBQuery("SELECT ".$question['destination_field']." FROM ".$question['destination_table']." WHERE ".$question['destination_idfield']."='".$this->owner."'");
				$r = $q->fetchIndex();
				$val = $r[0];
				Questionaire::dump("+ Value is $val");
			}
			else {
				$val = "";
			}

			if($val == "0000-00-00" || $val == "0"|| $val == "") {
				$rowstyle = "background-color: #FCE2E3";
			}

			if(!$this->enabled) {
				$enabled = "disabled=\"disabled\"";
			}

			$units = explode('-',$val);
			$format = explode(';',$question['extra']);

			echo "<tr style=\"$rowstyle\"><td class=\"label\">".htmlspecialchars($question['question'])."</td><td>";
			// Days
			if($format[0] > 2) {
				echo "<select $enabled onblur=\"setDate('".$question['name']."')\" class=\"date\" name=\"".$question['name']."day\" id=\"".$question['name']."day\">";
				echo "<option value=\"0\"></option>";
				for($i=1;$i<32;$i++) {
					if ((int)$units[2] == $i) {
						$chk = "selected =\"selected \"";
					}
					else {
						$chk = "";
					}
					echo "<option $chk value=\"".sprintf("%02d",$i)."\">".sprintf("%02d",$i)."</option>";
				}
				echo "</select>";
			}

			// Months
			if($format[0] > 1) {
				echo "<select $enabled onblur=\"setDate('".$question['name']."')\" class=\"date\" name=\"".$question['name']."month\" id=\"".$question['name']."month\">";
				echo "<option value=\"0\"></option>";
				for($i=1;$i<13;$i++) {
					if ((int)$units[1] == $i) {
						$chk = "selected =\"selected \"";
					}
					else {
						$chk = "";
					}
					echo "<option $chk value=\"".sprintf("%02d",$i)."\">".sprintf("%02d",$i)."</option>";
				}
				echo "</select>";
			}

			// Years
			echo "<select $enabled onblur=\"setDate('".$question['name']."')\" class=\"date\" name=\"".$question['name']."year\" id=\"".$question['name']."year\">";
			echo "<option value=\"0\"></option>";
			for($i=$format[1];$i<$format[2];$i++) {
				if ((int)$units[0] == $i) {
					$chk = "selected =\"selected \"";
				}
				else {
					$chk = "";
				}
				echo "<option $chk value=\"".sprintf("%02d",$i)."\">".sprintf("%02d",$i)."</option>";
			}

			echo "</select>";
			echo "<input class=\"string\" type=\"hidden\" name=\"".$question['name']."\" id=\"".$question['name']."\" value=\"".str_replace('-','',$val)."\" />";
			if($format[0] > 2) {
				echo "<small>(dag, maand, jaar)</small>";
			}
			echo "</td></tr>\n";
		}//_9_5_1_5e60209_1159788936000_291897_221

		/** - return type: void
			*
			* Outputs an XHTML button for submitting the form.
			**/
		function buildSubmitButton()
		{//_9_5_1_5e60209_1159792775828_732888_232
			if($this->enabled) {
				$this->buttonCount++;
				echo "<div style=\"width: 80px; margin: 10px 20px\" class=\"button\" id=\"enquetebutton".$this->buttonCount."\" onclick=\"verzendAanmelding(".$this->buttonCount.")\">";
				echo "<img class=\"button\" alt=\"keuzerondje\" id=\"loadingimg".$this->buttonCount."\" src=\"layout/checkbox_checked.png\"/>";
				echo "<p class=\"button\">opslaan</p>";
				echo "</div>";
				echo "<p class=\"subhint\">U kunt tussentijds uw antwoorden opslaan, zonder dat dit uw invoerproces verstoort.</p>";
			}
		}//_9_5_1_5e60209_1159792775828_732888_232

		/** - return type: void
			*
			* This method loads the groups belonging to the questionaire and stores them in the 'groups' attribute.
			**/
		function loadGroups()
		{//_9_5_1_5e60209_1159651465468_633382_184
			Questionaire::dump("+ Loading groups...");
			$q = new DBQuery("SELECT ns_question_group_link.*, ns_question_group.* FROM ns_question_group_link LEFT JOIN ns_question_group ON (ns_question_group.id = ns_question_group_link.question_group_id) WHERE questionaire_id=".$this->id." ORDER BY sortorder");
			while($r = $q->fetch()) {
				Questionaire::dump("+ ".$r['id'].": ".$r['name']);
				$this->groups[$r['id']] = $r['name'];
			}
		}//_9_5_1_5e60209_1159651465468_633382_184

		/** - return type: void
			* - param hiddendata: array
			* 	An array with key/value pairs describing the hidden input's name and value.
			*
			* This method sets the hidden form input.
			**/
		function setHiddenInputs($hiddendata)
		{//_9_5_1_5e60209_1159729719562_128726_3
			Questionaire::dump("+ Setting hidden inputs:");
			foreach($hiddendata as $key => $val) {
				Questionaire::dump("    $key: $val");
			}
			$this->hiddeninput = $hiddendata;
		}//_9_5_1_5e60209_1159729719562_128726_3

		/** - return type: void
			*
			* A utility method, which build a simple XHTML form to build questionaires.
			**/
		function questionaireConstructor()
		{//_9_5_1_5e60209_1159777022500_423717_123

			// Postponed
			define("FORCE",1);

			echo "<iframe src=\"http://gleufserver.balpol.tudelft.nl/_dbding/sql.php?db=stef01_nieuwestemmen&table=ns_question&goto=tbl_properties_structure.php\" ";
			echo "style=\"overflow: auto; margin: 5px 5px 0 0; padding: 0px; width: 1100px; border: 2px solid red; float: left; height: 400px;\">";
			echo "</iframe>";

			echo "<iframe src=\"http://gleufserver.balpol.tudelft.nl/_dbding/sql.php?db=stef01_nieuwestemmen&table=ns_question_link&goto=tbl_properties_structure.php\" ";
			echo "style=\"width: 750px; overflow: auto; margin: 5px 5px 0 0; padding: 0px; border: 2px solid red; float: left; height: 400px;\">";
			echo "</iframe>";

			echo "<iframe src=\"http://gleufserver.balpol.tudelft.nl/_dbding/sql.php?db=stef01_nieuwestemmen&table=ns_question_group&goto=tbl_properties_structure.php\" ";
			echo "style=\"width: 450px; overflow: auto; margin: 5px 5px 0 0; padding: 0px; border: 2px solid green; float: left; height: 400px;\">";
			echo "</iframe>";

			echo "<iframe src=\"http://gleufserver.balpol.tudelft.nl/_dbding/sql.php?db=stef01_nieuwestemmen&table=ns_question_group_link&goto=tbl_properties_structure.php\" ";
			echo "style=\"width: 450px; overflow: auto; margin: 5px 5px 0 0; padding: 0px; border: 2px solid green; float: left; height: 400px;\">";
			echo "</iframe>";

			echo "<iframe src=\"http://gleufserver.balpol.tudelft.nl/_dbding/sql.php?db=stef01_nieuwestemmen&table=ns_question_answer&goto=tbl_properties_structure.php\" ";
			echo "style=\"width: 450px; overflow: auto; margin: 5px 5px 0 0; padding: 0px; border: 2px solid blue; float: left; height: 400px;\">";
			echo "</iframe>";

			echo "<iframe src=\"http://gleufserver.balpol.tudelft.nl/_dbding/sql.php?db=stef01_nieuwestemmen&table=ns_question_answer_link&goto=tbl_properties_structure.php\" ";
			echo "style=\"width: 450px; overflow: auto; margin: 5px 5px 0 0; padding: 0px; border: 2px solid blue; float: left; height: 400px;\">";
			echo "</iframe>";


		}//_9_5_1_5e60209_1159777022500_423717_123

		/** - return type: mixed
			*
			* This method looks through the receive GET and POST headers and stores any questionaire information it might find. If the method succeeds, True is returned, otherwise an error message.
			**/
		function saveAnswers()
		{//_9_5_1_5e60209_1159912875828_787295_252

			if(isset($_POST['questionaireowner'])) {
					// This UPDATE should be integrated in a different way. And ns_groslijst should not be hardcoded, but a questionaire property.
					$updateq = new DBQuery("UPDATE ns_groslijst SET lastupdate = NULL, ipadres='".$_SERVER['REMOTE_ADDR']."' WHERE id='".$_POST['questionaireowner']."'");

					$q = new DBQuery("SELECT id FROM ns_groslijst WHERE id='".$_POST['questionaireowner']."'");
					if($q->numRows() == 1) {

						foreach($this->groups as $key => $val) {
								$q = new DBQuery("SELECT id, name, type, destination_table,destination_field, destination_idfield, answer_source,answer_table,answer_idfield,answer_value  FROM ns_question_link LEFT JOIN ns_question ON (ns_question.id = ns_question_link.question_id) WHERE question_group_id=".$key." ORDER BY sortorder");

								while($r = $q->fetch()) {
									if(isset($_POST[$r['name']])) {
										if(is_array($_POST[$r['name']])) {
											// Remove all previous answers for the current question.
											$aq = new DBQuery("DELETE FROM ns_question_answered WHERE participant_id = ".$_POST['questionaireowner']." AND question_id = ".$r['id']);
												if(!$aq->getQueryResult()) {
													return "Kon antwoorden voor vraag ".$r['id']." niet verwijderen!";
												}
											// Because of an hidden "checkbox" the length is 1 when no visible checkboxes are selected
											if(count($_POST[$r['name']]) > 1) {
												$insert = "INSERT INTO ns_question_answered (question_id, answer_id, participant_id) VALUES ";
												foreach($_POST[$r['name']] as $postedval) {
													if($postedval != -1) {

														if($r['answer_source'] == "external") {
															$insert .= "(".$r['id'].",".$postedval.",".$_POST['questionaireowner']."),";
														}
														else {
															$sq = new DBQuery("SELECT question_answer_id FROM ns_question_answer_link LEFT JOIN ns_question_answer ON (ns_question_answer.id=ns_question_answer_link.question_answer_id) WHERE question_id = ".$r['id']." AND value = ".$postedval);
															$sr = $sq->fetchIndex();
															$insert .= "(".$r['id'].",".$sr[0].",".$_POST['questionaireowner']."),";
														}
													}
												}
												$query = substr($insert,0,-1);
												$aq = new DBQuery($query);
												if(!$aq->getQueryResult()) {
													return "Kon antwoorden voor vraag ".$r['id']." niet opslaan!";
												}
											}
										}
										else {
											// utf8_decode added, beacause Javascript uses UTF-8 by default!
											$query = "UPDATE ".$r['destination_table']." SET ".$r['destination_field']."='".utf8_decode($_POST[$r['name']])."' WHERE ".$r['destination_idfield']."=".$_POST['questionaireowner'];
											$aq = new DBQuery($query);
											if(!$aq->getQueryResult()) {
												return "Kon antwoord voor vraag ".$r['id']." niet opslaan! $query";
											}
										}
									}
								}
						}

						return true;
					}
					else {
						return "Gebruiker ".$_POST['questionaireowner']." niet gevonden in database!";
					}
			}

			return "Geen gebruiker aangetroffen in formulier data!";

		}//_9_5_1_5e60209_1159912875828_787295_252

		/** - return type: void
			* - param text: string
			* - param color: string
			*
			**/
		function dump($text, $color = black)
		{//_9_5_1_5e60209_1159650122578_186687_132
			if(isset($_GET['verbose']) || defined('FORCE')) {
				if(isset($_GET['visible']) || defined('FORCE')){
					echo "\n<pre style=\"color: $color; padding: 0; margin: 0\">".$text."</pre>";
				}
				else {
					echo "\n<!--".$text."//-->";
				}
			}
		}//_9_5_1_5e60209_1159650122578_186687_132

	}

?>
