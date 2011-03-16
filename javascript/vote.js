/* Event.observe(window, 'load', primaryInit, false); */

var lastButton = null;
var candidateSelected = false;
var validNumber = false;
var sms_sent = false;
var verbose = (location.href.indexOf("jsverbose") != -1);
var vrkst = (location.href.indexOf("vrkst") != -1);

var logwin;

function primaryInit() {
	logwin = new LogWindow("logwin");
	logwin.pl("Log initialized");
	if(verbose) {
		Element.show($("logwin"));
	}

	enableButton("phone_button", false);
	enableButton("code_button", false);
	enableButton("resend_button", false);

	if(vrkst) {
		//enableButton("phone_button", true);
		$("phonenumber").disabled = false;
		$("phonenumber").focus();
		candidateSelected = true;
	}
}

/* Event handlers */

/** This function processes the mouse clicks on the
	* candidate select buttons. It performs the following actions
	* - Changes the look of the button to reflect the selection.
	* - Sets the hidden form element to the ID value of the selected candidate.
	* When no code has been sent, it also:
	* - Enables the mobile phone number field and puts the focus on it.
	* - Enables the phone button.
 	**/
function selectCandidate(currentButton, candidate)
{
	// If a checkbox was already selected, reset it
	if(lastButton != null) {
		lastButton.src = "layout/checkbox.png";
	}

	// Change the image of the selected checkbox
	lastButton = currentButton;
	currentButton.src = "layout/checkbox_checked.png";

	// Set the value of the selected candidate
	$("selectedcandidate").value = candidate;
	candidateSelected = true;
	logwin.pl("Candidate " + candidate + " selected");

	// If an SMS was not yet sent, enable the phone field and button
	if (!sms_sent) {
		$("phonenumber").disabled = false;
		$("phonenumber").focus();
		logwin.pl("Phone field enabled");
	}
}

/** This function validates the given phone number and
	* tries to request a voting code.
	**/
function processPhoneNumber(pollid)
{
	try {
		// Stop if no candidate was selected.
		if (!candidateSelected) {
			return;
		}

		// Get the entered phone number
		var phonenumber = $F("phonenumber");
		len = phonenumber.length;

		if (len > 0) {
			// Validate the phone number.

			// Clean number, removing spaces and dashes
			var i = 0;
			var cleanNum = "";

			while(i < len) {
				if (phonenumber.charAt(i) != '-' && phonenumber.charAt(i) != ' ') {
					cleanNum += phonenumber.charAt(i);
				}
				i++;
			}

			phonenumber = cleanNum;

			// Regular expression accepts the following:
			// (\+316|316|06)	-> only ductch numbers, starting with +316, 316 or 06
			// [0-9]{8}			-> squence of 8 numbers
			// The characters between the braces can be extracted from the string
			// Further server-side number validation is still required!
			rePhoneNumber = new RegExp(/^((\+316|316|06)[0-9]{8})$/);

			if (rePhoneNumber.test(phonenumber)) { // Number is valid
				// Update the form field to display the cleaned up phone number
				$("phonenumber").value = phonenumber;
				validNumber = true;
				enableButton("phone_button", true);
				logwin.pl("Number " + phonenumber + " valid");
				return;
			}
		}
	}
	catch (e) {
		alert("Error1: " + e);
	}
	validNumber = false;
	enableButton("phone_button", false);
	logwin.pl("Number " + phonenumber + " invalid", "#940C12");
}

function requestCode(pollid, resend)
{
	if(validNumber && !sms_sent && candidateSelected) {
		// re-Request a voting code
		var uri  = "http://www.primaries.nl/action.php";
		var pars = "action=sendcode&phonenumber=" + $F("phonenumber").replace('+','%2B') + "&pollid=" + pollid;	// '+' sign needs to be escaped (the 'escape()' function doesn't do that)

		if(resend) {
			pars += "&resend=1";
		}
		else {
			enableButton("phone_button", false);
		}

		var myAjax = new Ajax.Request(
						uri,
						{
							method: 'get',
							parameters: pars,
							onComplete: showRequestResult
						});
		logwin.pl("Requesting code:");
		logwin.pl("Url: " + pars, "darkgreen");
	}
	else {
		logwin.pl("Illegal to request code!", "#940C12");
	}
}

/* Server response handlers */

/** This function processes the response given by the server
	* on an SMS request.
	**/
function showRequestResult(req)
{
	try {
		logwin.pl("Requesting xml: \n" + req.responseText, "darkgreen");
		// Get the status value and message node.
		var status = req.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
		logwin.pl("Response status: " + status);

		var message = req.responseXML.getElementsByTagName('message')[0];
		//Element.cleanWhitespace(message);

		// Get the sms request information div node
		var targetElement = $("sms_confirm");
		//Element.cleanWhitespace(targetElement);

		// Insert the response text into the information div and display it.
		targetElement.firstChild.nodeValue = message.firstChild.nodeValue;

		$("sms_confirm").style.display = "block";

		if(status == 0) {		// If the server tells us something went wrong...
			sms_sent = false;
			targetElement.style.color = "red";
		}
		else {
			// If everything went according to plan...
			sms_sent = true;
			targetElement.style.color = "black";

			// Disable phone field and button
			$("phonenumber").disabled = true;
			enableButton("phone_button", false);

			// Enable confirmation field and button
			$("votingcode").disabled = false;
			$("votingcode").focus();
			enableButton("code_button", true);

			if(status == 2) {	// Allow for a resend
				enableButton("resend_button", true);
				sms_sent = false;
			}
			else {
				enableButton("resend_button", false);
			}
		}
	}
	catch (e) {
		alert("Error5: " + e);
	}
}

function checkCode()
{
	// Get the entered voting code
	votingcode = $F("votingcode");
	len = votingcode.length;

	logwin.pl("Checking code " + votingcode);

	if (len == 0) {	// If nothing was entered
		alert("U moet uw stemcode invoeren \nom uw stem te bevestigen!");
		$("votingcode").focus();
	}
	else if (len != 8) { // If an invalid code was entered
		alert("Onjuiste stemcode!\n Een stemcode bestaat uit 8 cijfers en/of letters.");
		$("votingcode").focus();
	}
	else {
		$("votingcode").disabled = false;
		$("phonenumber").disabled = false;
		var uri = "http://www.primaries.nl/action.php";
		var pars ="action=checkcode&";
		pars += "phonenumber=" + $("phonenumber").value.replace('+','%2B');	// '+' sign needs to be escaped (the 'escape()' function doesn't do that)
		pars += "&votingcode=" + $("votingcode").value;

		var myAjax = new Ajax.Request(
						uri,
						{
							method: 'get',
							parameters: pars,
							onComplete: submitVote
						});
	}
}

function voteVrk()
{
	// Get the entered voting code
	votingcode = $F("votingcode");
	len = votingcode.length;

	logwin.pl("Checking code " + votingcode);

	if (len == 0) {	// If nothing was entered
		alert("U moet uw stemcode invoeren \nom uw stem te bevestigen!");
		$("votingcode").focus();
	}
	else if (len != 8) { // If an invalid code was entered
		alert("Onjuiste stemcode!\n Een stemcode bestaat uit 8 cijfers en/of letters.");
		$("votingcode").focus();
	}
	else {
		$("votingcode").disabled = false;
		$("phonenumber").disabled = false;
		var uri = "http://www.primaries.nl/action.php";
		var pars ="action=votevrk&";
		pars += "phonenumber=" + $("phonenumber").value.replace('+','%2B');	// '+' sign needs to be escaped (the 'escape()' function doesn't do that)
		pars += "&votingcode=" + $("votingcode").value;
		pars += "&candidates=" + $("selectedcandidates").value;

		var myAjax = new Ajax.Request(
						uri,
						{
							method: 'get',
							parameters: pars,
							onComplete: vrkstVoted
						});
	}
}

function vrkstVoted(req)
{
	try {
		logwin.pl("Requesting xml: \n" + req.responseText, "darkgreen");
		// Get the status value and message node.
		var status = req.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
		logwin.pl("Response status: " + status);

		var message = req.responseXML.getElementsByTagName('message')[0];
		//Element.cleanWhitespace(message);

		if(status == 1) {
			location.href = "vrkst_bedankt.php";
		}
		else {
			alert(message.firstChild.nodeValue);
		}
	}
	catch (e) {
		alert("Error3: " + e + "\n:" + req.responseText);
	}
}

function submitVote(req)
{
	try {
		logwin.pl("Requesting xml: \n" + req.responseText, "darkgreen");
		// Get the status value and message node.
		var status = req.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
		logwin.pl("Response status: " + status);

		var message = req.responseXML.getElementsByTagName('message')[0];
		//Element.cleanWhitespace(message);

		if(status == 1) {
			logwin.pl("Code OK, voting...");
			$("votingform").submit();
		}
		else {
			alert("Uw stemcode is onjuist! Controleer a.u.b. op typefouten.");
			$("votingcode").focus();
		}
	}
	catch (e) {
		alert("-Error4: " + e.mess + "\n" + e);
	}
}

