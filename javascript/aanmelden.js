Event.observe(window, 'load', aanmeldenInit, false);
var currentButton = false;
var currentImage = false;
var hints;
var timer = 60000;
var shouldsave = false;

function aanmeldenInit()
{
	var inputs = document.getElementsByTagName('input');

	for (var i=0;i<inputs.length;i++)
	{
		inputs[i].onchange = markRow;
	}

	inputs = document.getElementsByTagName('select');

	for (var i=0;i<inputs.length;i++)
	{
		inputs[i].onchange = markRow;
	}

	inputs = document.getElementsByTagName('textarea');

	for (var i=0;i<inputs.length;i++)
	{
		inputs[i].onchange = markRow;
	}

	hints = document.getElementsByClassName("subhint");
	setTimeout("save()", timer);
}

function showQuestionaire()
{
	Element.toggle($('disclaimer'));
	Element.toggle($('enquete'));
}


function save()
{
	if(shouldsave) {
		for (var i=0;i<hints.length;i++)
		{
			hints[i].innerHTML = "Automatisch opslaan...";
		}
		var myAjax = new Ajax.Request(
						    "action.php",
						    {method: 'post', parameters: Form.serialize("questionaireform"), onComplete: autoSave}
						);
		shouldsave = false;
	}
	else {
		setTimeout("save()", timer);
	}
}

function autoSave(req)
{
	for (var i=0;i<hints.length;i++)
	{
		hints[i].innerHTML = "U kunt tussentijds uw antwoorden opslaan, zonder dat dit uw invoerproces verstoort.";
	}
	var status = req.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
	if(status == 1) {
		setTimeout("save()", timer);
	}
}

function verzendAanmelding(nummer)
{
	//alert(Form.serialize("questionaireform"));
	currentButton = "enquetebutton" + nummer;
	currentImage = "loadingimg" + nummer;

	$(currentImage).src = "layout/loading.gif";
	enableButton(currentButton, false);
	var myAjax = new Ajax.Request(
					    "action.php",
					    {method: 'post', parameters: Form.serialize("questionaireform"), onComplete: ajax_response}
					);
}

function ajax_response(req)
{
	$(currentImage).src = "layout/checkbox_checked.png";
	enableButton(currentButton, true)

	//alert(req.responseText);
	var servstatus = req.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;

	var message = req.responseXML.getElementsByTagName('message')[0];
	Element.cleanWhitespace(message);

	if(servstatus == 1){
		alert(message.firstChild.nodeValue);
	}
	else {
		alert("Er heeft zich een fout voorgedaan: " + message.firstChild.nodeValue);
	}
}

function markRow(e){
	if (!e) var e = window.event
	var node = Event.findElement(e, 'tr');

	shouldsave = true;

	if (node.tagName) {
	    node.style.backgroundColor = 'transparent';
	}
}

function setDate(dname)
{
	var val = "";
	if($(dname+"year")) {
		val += $F(dname+"year");
	}
	else {
		val += "01";
	}

	if($(dname+"month")) {
		val += $F(dname+"month");
	}
	else {
		val += "01";
	}

	if($(dname+"day")) {
		val += $F(dname+"day");
	}
	else {
		val += "01";
	}

	$(dname).value =  val;
}

function mailSurvey()
{
	var allNodes = Form.getElements("questionaireform");
	var node;

	for(i=0; i<allNodes.length;i++) {
		if(allNodes[0].type == 'hidden') {
			alert(allNodes[0].name);
			return;
		}
	}

}
