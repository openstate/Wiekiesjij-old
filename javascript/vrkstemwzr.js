// Event.observe(window, 'load', voorkeurInit, false);

var cache = null;
var activebutt = null;
var selectedCandidates = new Array(0,0,0,0,0);
var selectedNames = new Array("","","","","");
var oldstyle = null;
var dropbar = null;
var candidates = null;
var actionCache = null;
var lastDrag = null;
var dropCache = null;
var dropImage = null;
var busy = false;

function voorkeurInit() {
	loadSelection("data_plaatsen.php", setPlaces);
	loadSelection("data_provincies.php", setProvinces);
	loadSelection("data_landen.php", setCountries);

	// alles initieren
	fold($("menupersonal"));
	fold($("menuliving"));
	fold($("menuborn"));
	fold($("menuraised"));
	fold($("menufamily"));
	fold($("menupolitics"));
	fold($("menuervaring"));

	// alles behalve personal inklappen
	fold($("menuliving"));
	fold($("menuborn"));
	fold($("menuraised"));
	fold($("menufamily"));
	fold($("menupolitics"));
	fold($("menuervaring"));	

	initDropBar();

	var profile = getQueryVariable('profile');
	if(profile > 0) {
		getProfile(profile);
	}

	for(var i=0; i<6; i++) {
//		Event.observe('dropzone'+i, 'click', showProfile, false);
		Droppables.add('dropzone'+i, {
			hoverclass:'drophover',
			onDrop: selectCandidate
			});
	}
}

function resetForm()
{
	Form.reset('filterform');
	$('grospics').innerHTML = "";
}

function initDropBar() {
	dropbar = $('dropheader');
	moveDropBar();

}

function dropHoverDone(e)
{
	//Event.stopObserving(dropCache, 'mouseout', dropHoverDone, false);
	dropCache.style.backgroundImage = dropImage;
	dropCache = null;
	dropImage = null;
	alert("?");
}

function onDropHover(drag, drop, perc) {
	dropCache = drop;
	dropImage = drop.style.backgroundImage;
	dropCache.style.backgroundImage = "url(voorkeurstemmer/release.gif)";
//	Event.observe(dropCache, 'mouseout', dropHoverDone, false);
}

function startDrag(event)
{
	actionCache = Draggables.activeDraggable.element.onclick;
	Draggables.activeDraggable.element.onclick = null;
	//alert(Draggables.activeDraggable.element.onclick);
}

function endDrag(event)
{
	lastDrag = Draggables.activeDraggable.element;
	setTimeout('fixDrag()',500);
	//alert(Draggables.activeDraggable.element.onclick);
}

function fixDrag()
{
	 lastDrag.onclick = actionCache;
	 lastDrag = null;
}

function moveDropBar()
{
	if (window.innerHeight)	{
		pos = window.pageYOffset
	}
	else if (document.documentElement && document.documentElement.scrollTop) {
		pos = document.documentElement.scrollTop
	}
	else if (document.body) {
		pos = document.body.scrollTop
	}

	if (pos - topDiff < theTop) { // If not scrolled enough to move
		pos = theTop;
	}
	else {	// else move to 20px from top edge
		pos += 20 - topDiff;
	}

/*	if (pos == old) 	{
		dropbar.style.marginTop = old + "px";
	} */

	old= pos;
	temp = setTimeout('moveDropBar()',20);
}



function startVote() {
	var gogo = false;
	var getdata = "?c=";

	for(var i=0; i<selectedCandidates.length;i++) {
		if (selectedCandidates[i] != 0) {
			gogo = true;
			getdata += selectedCandidates[i] + ";";
		}
	}

	if(gogo) {
		if (selectedCandidates[i] != 0) {
			//http://www.primaries.nl/
			location.href = "http://www.primaries.nl/vrkst_vote.php" + getdata.substr(0,getdata.length - 1);
			return;
		}
	}
	else {
		alert("U moet minimaal 1 kandidaat selecteren om te kunnen stemmen!");
	}
}

function showProfile (e)
{
	var orig = Event.findElement(e, 'div');
	var pos = orig.id.substr(8,1);
	if(selectedCandidates[pos] != 0) {
		getProfile(selectedCandidates[pos]);
	}
}

function removeFromSelection(can)
{
	for(var i=0; i<selectedCandidates.length;i++) {
		if (selectedCandidates[i] == can) {
			selectedCandidates[i] = 0;
			selectedNames[i] = "";
			$('dropzone'+i).style.backgroundImage = "url(voorkeurstemmer/drop.gif)";
		}
	}

	try {
		Element.toggle('candel');
		Element.toggle('canadd');
	}
	catch (e) {
		//no biggy
	}
}

function addToSelection(can,name,photo)
{
	var added = false;
	var pos = -1;
	for(var i=0; i<selectedCandidates.length;i++) {
		if (selectedCandidates[i] == 0) {
			pos = i;
		}
		if (selectedCandidates[i] == can) {
			return;
		}
	}

	if (pos != -1) {
		selectedCandidates[pos] = can;
		selectedNames[pos] = name;
		$('dropzone'+pos).style.backgroundImage = "url(voorkeurstemmer/50/"+photo+")";
		added = true;
	}

	if(!added) {
		alert("U kunt niet meer dan 5 kandidaten selecteren!");
	}
	else {
		try {
			Element.toggle('candel');
			Element.toggle('canadd');
		}
		catch (e) {
			//no biggy
		}
	}
}

function selectCandidate(element, dropp)
{
	var candidate = element.id.substr(3);
	var name = element.alt;
	var pos = dropp.id.substr(8,1);
	var path = element.src.split("/");
	path = path[path.length - 1];

	if(pos == 5) {
		addToSelection(candidate,name,path);
	}
	else {
		for(var i=0; i<selectedCandidates.length;i++) {
			if (selectedCandidates[i] == candidate) {
				//alert(candidate);
				return;
			}
		}
		selectedCandidates[pos] = candidate;
		selectedNames[pos] = name;
		//alert(name);
		dropp.style.backgroundImage = "url(voorkeurstemmer/50/" + path + ")";
	}
}

function autoFilter()
{
	if(busy == false) {
		$("grospics").innerHTML = '<p style="width: 100px; margin: 10px auto; color: #ddd; font-weight: bold; "> Laden...<img alt="loading" style="display: inline" src="layout/loading.gif"/></p>';
		busy = true
		var myAjax = new Ajax.Updater(
						    "grospics",
						    "vrk_filter.php",
						    {method: 'post', parameters: Form.serialize("filterform"), onComplete: showResult, evalScripts: true}
						);
	}
}

function filter(butt) {
	if(activebutt == null) {
		activebutt = butt;
		var icon = activebutt.getElementsByTagName('img')
		icon[0].src = "layout/loading.gif";
		enableButton(butt, false);
		var myAjax = new Ajax.Updater(
						    "grospics",
						    "vrk_filter.php",
						    {method: 'post', parameters: Form.serialize("filterform"), onComplete: showResult, evalScripts: true}
						);
	}
}


function loadSelection(file, resolver)
{
	var myAjax = new Ajax.Request(
					    "def/" + file,
					    {method: 'get', onComplete: resolver}
					);
}

function setPlaces(request)
{
	$("woonplaats").parentNode.innerHTML = "<p class=\"sellabel\">gemeente</p><select name=\"woonplaats\" id=\"woonplaats\">" + request.responseText + "</select>";
	Event.observe('woonplaats', 'change', autoFilter, false);
	$("geboorteplaats").parentNode.innerHTML = "<p class=\"sellabel\">gemeente</p><select name=\"geboorteplaats\" id=\"geboorteplaats\">" + request.responseText + "</select>";
	Event.observe('geboorteplaats', 'change', autoFilter, false);
	$("opgroeiplaats").parentNode.innerHTML = "<p class=\"sellabel\">gemeente</p><select name=\"opgroeiplaats\" id=\"opgroeiplaats\">" + request.responseText + "</select>";
	Event.observe('opgroeiplaats', 'change', autoFilter, false);
}

function setProvinces(request)
{
	$("woonprovincie").parentNode.innerHTML = "<p class=\"sellabel\">provincie</p><select name=\"woonprovincie\" id=\"woonprovincie\">" + request.responseText + "</select>";
	Event.observe('woonprovincie', 'change', autoFilter, false);
	$("geboorteprovincie").parentNode.innerHTML = "<p class=\"sellabel\">provincie</p><select name=\"geboorteprovincie\" id=\"geboorteprovincie\">" + request.responseText + "</select>";
	Event.observe('geboorteprovincie', 'change', autoFilter, false);
	$("opgroeiprovincie").parentNode.innerHTML = "<p class=\"sellabel\">provincie</p><select name=\"opgroeiprovincie\" id=\"opgroeiprovincie\">" + request.responseText + "</select>";
	Event.observe('opgroeiprovincie', 'change', autoFilter, false);
}

function setCountries(request)
{
	$("geboorteland").parentNode.innerHTML = "<p class=\"sellabel\">land</p><select name=\"geboorteland\" id=\"geboorteland\">" + request.responseText + "</select>";
	Event.observe('geboorteland', 'change', autoFilter, false);
	$("opgroeiland").parentNode.innerHTML = "<p class=\"sellabel\">land</p><select name=\"opgroeiland\" id=\"opgroeiland\">" + request.responseText + "</select>";
	Event.observe('opgroeiland', 'change', autoFilter, false);
}

function filterSame(column, val)
{
	var param = column + "=" + val;
	//alert(param);
	var myAjax = new Ajax.Updater(
					    "grospics",
					    "vrk_filter.php",
					    {method: 'post', parameters: param, onComplete: showResult}
				);
	Form.reset('filterform');
	if($(column).type == 'select-one')
	{
		for (i=0;i<$(column).options.length;i++) {
	  	  if ($(column).options[i].value == val) {
	    	  $(column).selectedIndex = i;
	      	break;
	    	}
	  	}
	}
	else if($(column).type == 'text') {
		$(column).value = val.split("&")[0];
		$(column+"op").selectedIndex = 3;
	}
}

function showResult(request)
{
	cache = request.responseText;
	busy = false;

	if(activebutt != null) {
		var icon = activebutt.getElementsByTagName('img')
		icon[0].src = "layout/checkbox_checked.png";
		enableButton(activebutt, true);
		activebutt = null;
	}
	$("filtericon").src = "layout/checkbox_checked.png";
}


function getProfile(num)
{
	cache = $("grospics").innerHTML;

	if(num>0) {
		var myAjax = new Ajax.Updater(
				    "grospics",
				    "vrk_profile.php",
				    {method: 'post', parameters: "id="+num, evalScripts: true}//, onComplete: showResult}
				);
	}
}

function closeProfile()
{
	if(cache != null && cache !='') {
		Element.update($("grospics"),cache);
	}
	else {
		history.back();
	}
}

function getQueryVariable(variable) {
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (pair[0] == variable) {
      return pair[1];
    }
  }
  return 0;
}

