<?php

	/*** Poll Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1150797977625_815072_27 ***/

	/** + Class Description +
		*
		**/

	require_once("dbquery_class.php");

	class Poll
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: integer
			* The ID of the poll.
			**/
		var $pollID;

		/** - type: string
			* The title of the poll.
			**/
		var $title;

		/** - type: string
			* HTML descibing the poll, or displaying any other information.
			**/
		var $description;

		/** - type: boolean
			* This attribute indicates whether the poll is still running ('true') of not.
			**/
		var $active = false;

		/** - type: string
			* HTML string containing statistical data about the poll.
			**/
		var $statistics;

		/** - type: string[]
			* The candidates involved in the poll
			**/
		var $candidates = array();

		/** - type: integer
			* The number of candidates involved in the poll.
			**/
		var $candidateCount = 0;

		/** - type: array
			* An array of arrays, describing how to construct the lay out
			* of candidates depending on their number.
			**/
		var $formation;

		/** - type: string
			* The directory in which images, and other files belonging to the poll, are stored.
			**/
		var $rootdir = null;

		/*+*+*+*+*+*+*+*+*  associations *+*+*+*+*+*+*+*+*/
		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			* - param pollid: integer
			* 	The id of the poll to build  a Poll object for.
			*
			* The contructor.
			* PRE: pollid must be an integer.
			**/
		function Poll($pollid)
		{//_9_5_1_5e60209_1150802712156_811024_219
			if(is_numeric($pollid) && $pollid > 0) {
				$this->pollID = $pollid;

				$q = new DBQuery ( "SELECT * FROM ns_poll WHERE id=".$pollid );

				if($q->numRows() == 0) {
					die("Fatal error!: SELECT * FROM ns_poll WHERE id=".$pollid);
				}

				$record = $q->Fetch();

				$this->title = $record['titel'];
				$this->active = $record['actief'];
				$this->description = $record['omschrijving'];
				$this->statistics = $record['statistiek'];
				$this->rootdir = $record['rootdir'];

				// Choice formations, row majority, values in width percentages
				$this->formation[2] = array(array(50,50));
				$this->formation[3] = array(array(33,33,33));
				$this->formation[4] = array(array(50,50),array(50,50));
				$this->formation[5] = array(array(33,33,33),array(50,50));
				$this->formation[6] = array(array(33,33,33),array(33,33,33));
				$this->formation[7] = array(array(50,50),array(33,33,33),array(50,50));
				$this->formation[8] = array(array(33,33,33),array(50,50),array(33,33,33));
				$this->formation[12] = array(array(33,33,33),array(33,33,33),array(33,33,33),array(33,33,33));
			}
			else {
				die("Fatal error!");
			}
		}//_9_5_1_5e60209_1150802712156_811024_219

		/** - return type: boolean
			* - param choice: integer
			* 	The choice made.
			*
			* This method determines whether the given choice is valid for the poll.
			**/
		function validChoice($choice)
		{//_9_5_1_5e60209_1150877036625_620823_363
			if(is_numeric($choice)) {
				$q = new DBQuery("SELECT * FROM ns_antwoorden WHERE id=".$choice." AND pollid=".$this->pollID);
				return $q->NumRows() == 1;
			}
			return false;
		}//_9_5_1_5e60209_1150877036625_620823_363

		/** - return type: void
			*
			* This method shows the candidates belonging to the given poll, including
			* their voting percentages.
			**/
		function showCandidates()
		{//_9_5_1_5e60209_1150798168968_546676_90

			$this->loadCandidates();
			$this->calculatePercentages();

			$record = current($this->candidates);		// Get the first candidate

			foreach($this->formation[$this->candidateCount] as $row) {
				echo "<table class=\"candidates\">";
				echo "<tr>";
				foreach($row as $width) {

					echo "<td style=\"width: ".$width."%\">";
					if($record['biografie'] != '') {
						echo "<a href=\"prm_profile.php?pollid=".$this->pollID."&id=".$record['id']."\" title=\"".$record['titel']."\">";
					}
					echo "<img alt=\"".$record['titel']."\" src=\"primaries/".$this->rootdir."/".$record['foto']."\"/>";
					if($record['biografie'] != '') {
						echo "</a>";
					}
					echo "<p>".$record['titel']."</p>";
					echo "<p class=\"percentage\">".($record['percentage'])."%</p>";
					echo "</td>";
					$record = next($this->candidates); // Get the next candidate
				}
				echo "</tr>\n";
				echo "</table>";
			}

			echo "<p style=\"text-align: center; color: #BBBBBB; font-size: 9px\">Het kan voorkomen dat de som van de percentages ongelijk is aan 100 i.v.m. afrondingsfouten.</p>";
		}//_9_5_1_5e60209_1150798168968_546676_90

		/** - return type: void
			*
			* This method displays an xhtml form with which users can vote
			* for the given poll.
			**/
		function showVotingBooth()
		{//_9_5_1_5e60209_1150807760578_603079_311

			if($this->active === false) {
				return;
			}

			echo <<<FRM
				<div class="attention">stem nu zelf!</div>
				<form action="prm_vote.php" method="post" id="votingform">
					<table id="votingbooth">
						<tr>
							<td colspan="4">
								<p style="margin-top: 0px" id="help1" class="help"><b>Stap 1:</b> Hier kunt u uw keuze bepalen door op het gewenste
								kiesrondje te klikken. Deze keuze kunt u veranderen totaan het moment dat u uw
								stem bevestigt met behulp van uw stemcode.</p>
							</td>
						</tr>
FRM;

			$first = true;

			foreach($this->candidates as $value) {
				echo "<tr>";
				if($first) {
					$first = false;
					echo "<td class=\"label\">1. ik stem op:<input type=\"hidden\" name=\"selectedcandidate\" id=\"selectedcandidate\"/><input type=\"hidden\" name=\"pollid\" value=\"".$this->pollID."\"/></td>";
					echo "<td class=\"checkbox\"><img title=\"klik en kies!\" onclick=\"selectCandidate(this,".$value['id'].")\" class=\"vote_check\" alt=\"keuzerondje\" src=\"layout/checkbox.png\"/></td>";
					echo "<td class=\"choice\">".$value['titel']."</td>";
					echo "<td><a onfocus=\"this.blur()\" href=\"javascript: toggleView('help1')\" title=\"Uitleg\">uitleg</a></td>";
				}
				else {
					echo "<td></td>";
					echo "<td class=\"checkbox\"><img title=\"klik en kies!\" onclick=\"selectCandidate(this,".$value['id'].")\" class=\"vote_check\" alt=\"keuzerondje\" src=\"layout/checkbox.png\"/></td>";
					echo "<td class=\"choice\">".$value['titel']."</td>";
				}
				echo "</tr>";
			}

			echo <<<FRM
				<tr>
					<td colspan="4">
						<p id="help2" class="help"><b>Stap 2:</b> Vul het nummer van uw mobiele telefoon in en klik op de knop.
						Binnen enkele ogenblikken ontvangt u een <b>gratis</b> SMS met daarin een code waarmee u
						uw stem kunt bevestigen. U kunt uw bij stap 1 gemaakte keuze nu nog veranderen.<br/>
						U heeft al gestemd? Dan kunt u uw stem veranderen door uw telefoonnummer en reeds toegezonden
						stemcode in te vullen.</p>
					</td>
				</tr>
				<tr>
					<td class="label">2. mijn mobiele nummer:</td>
					<td colspan="2">
						<input onfocus="processPhoneNumber('{$this->pollID}')" onkeyup="processPhoneNumber('{$this->pollID}')" disabled="disabled" name="phonenumber" id="phonenumber" style="width: 160px" type="text"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2">
						<div style="width: 164px" class="button" id="phone_button" onclick="requestCode('{$this->pollID}',false)">
							<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
							<p class="button">klik en stem!</p>
						</div>
						<div style="" id="sms_confirm">conf</div>
						<div style="width: 164px; display: none" class="button" id="resend_button" onclick="requestCode('{$this->pollID}',true)">
								<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
								<p class="button">herzend stemcode</p>
							</div>
					</td>
					<td><a onfocus="this.blur()" href="javascript: toggleView('help2')" title="Uitleg">uitleg</a></td>
				</tr>

				<tr>
					<td colspan="4">
						<p id="help3" class="help"><b>Stap 3:</b> Vul de ontvangen code in. Deze code bestaat uit 8 cijfers en/of
						letters. Controleer nogmaals of u bij stap 1 de juiste keuze heeft gemaakt en klik vervolgens op de knop
						om uw stem te bevestigen.</p>
					</td>
				</tr>
				<tr>
					<td class="label">3. mijn stemcode:</td>
					<td colspan="2">
						<input disabled="disabled" name="votingcode" id="votingcode" style="width: 160px" type="text"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2">
						<div style="width: 164px" class="button" id="code_button" onclick="checkCode()">
							<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
							<p class="button">bevestig uw stem!</p>
						</div>
					</td>
					<td><a onfocus="this.blur()" href="javascript: toggleView('help3'); toggleView('help4')" title="Uitleg">uitleg</a></td>
				</tr>
				<tr>
					<td colspan="4">
						<p id="help4" class="help">U heeft nu <b>eenvoudig</b> en <b>gratis</b> uw stem laten horen!
						Voor verdere informatie kunt u het menu aan de linker zijde van de pagina gebruiken.</p>
					</td>
				</tr>

			</table>
		</form>
FRM;
		}//_9_5_1_5e60209_1150807760578_603079_311

		/** - return type: boolean
			*
			* This method loads the candidates for the given poll from the database. It
			* return 'true' on success, and 'false' otherwise.
			**/
		function loadCandidates()
		{//_9_5_1_5e60209_1150802489812_581718_213
			/** Candidates have the following attributes:
				* id, pollid, titel, foto, biofoto, biofrafie
				**/
			if($this->candidateCount == 0) {
				$q = new DBQuery ( "SELECT id, titel, foto FROM ns_antwoorden WHERE pollid=".$this->pollID." ORDER BY titel" );
				$this->candidateCount = $q->NumRows();

				while($record = $q->Fetch()) {
					$record['percentage'] = 0;			// Default percentage of '0' is added.
					$record['votecount'] = 0;			  // Default votecount of '0' is added.
					$this->candidates[$record['id']] = $record;
					//print_r($this->candidates[$record['id']]);
				}
				return true;
			}

			return false;
		}//_9_5_1_5e60209_1150802489812_581718_213

		/** - return type: boolean
			*
			* This method calculates what percentage of the votes each candidate has
			* received. It returns 'false' on error, and 'true' otherwise.
			**/
		function calculatePercentages()
		{//_9_5_1_5e60209_1150803583140_149323_232
			$q = new DBQuery ( "SELECT antwoordid, COUNT(*) as c FROM ns_stemmen WHERE pollid=".$this->pollID." GROUP BY antwoordid" );

			$sum = 0;

			while($row = $q->Fetch()) {
				$this->candidates[$row['antwoordid']]['votecount'] = $row['c'];
				$sum += $row['c'];
			}

			if($sum == 0) {
				return false;
			}
			else {
				foreach($this->candidates as $key => $val ) {
					$percentage = round(((($val['votecount'])/$sum)*1000)/10,1);
					$this->candidates[$key]['percentage'] = $percentage + 0.0;
				}
			}
			return true;
		}//_9_5_1_5e60209_1150803583140_149323_232

		/** - return type: string
			*
			* Returns the title of the poll.
			**/
		function getTitle()
		{//_9_5_1_5e60209_1150804756828_497669_294
			return $this->title;
		}//_9_5_1_5e60209_1150804756828_497669_294

		/** - return type: boolean
			*
			* Indicates whether the poll is still running.
			**/
		function isActive()
		{//_9_5_1_5e60209_1150804756828_466572_296
			return $this->active;
		}//_9_5_1_5e60209_1150804756828_466572_296

		/** - return type: string
			*
			* Returns the HTML description of the poll.
			**/
		function getDescription()
		{//_9_5_1_5e60209_1150804756828_705228_298
			return $this->description;
		}//_9_5_1_5e60209_1150804756828_705228_298

		/** - return type: string
			*
			* Returns the statistical information about the poll, in xhtml format.
			**/
		function getStatistics()
		{//_9_5_1_5e60209_1150804756828_997508_300
			return $this->statistics;
		}//_9_5_1_5e60209_1150804756828_997508_300

	}

?>
