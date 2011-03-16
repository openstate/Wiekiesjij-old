Event.observe(window, 'load', helpInit, false);

var partyselected = false;
var checkies = new Array(100);
var numcheckies = 20;

function handleUnload()
{
	return "----------------------------------------------------------------\nKlik op OK om de voorkeurstemhulp te verlaten.\n\nGebruik de 'terug' en 'verder' knoppen onderaan\nde pagina om tussen de vragen te navigeren.\n----------------------------------------------------------------";
} 

function helpInit()
{
	for(i=1;i<numcheckies+1;i++) {
		checkies[i-1] = $('party'+i);
		Event.observe('party'+i, 'click', checkCheck, true);
	}
}

function checkCheck()
{
	partyselected = false;
	for(i=0;i<numcheckies;i++) {
		if(checkies[i]) {
			if(checkies[i].checked) {
				partyselected = true;
			}
		}
	}
}

function toggle_spect($box, $div) {
	if (document.getElementById($box).checked == true) {
		Element.show($div);
	} else {
		Element.hide($div);
	}
}

function getadvise()
{
	window.onbeforeunload = null;
	$('stemhulpform').submit();
}

function nextPage(cur,nxt, check)
{
	if(nxt == 9) {
		window.onbeforeunload = null;
	}
	else {
		//window.onbeforeunload = handleUnload;
	} 
	if (nxt == 9) {
		if (!$('sector5').checked) {
			nxt=10;
		}
	}

	checkCheck();

	if(!partyselected && check) {
		alert("U moet minmaal 1 partij selecteren!");
	}
	else {
		Element.hide('page' + cur);
		Element.show('page' + nxt);
	}

	window.scrollTo(0,0);
}

function previousPage(cur,prv)
{
	if(prv == 0) {
		window.onbeforeunload = null;
	}
	else {
		//window.onbeforeunload = handleUnload;
	}

	if (prv == 9) {
		if (!$('sector5').checked) {
			prv=8;
		}
	}

	checkCheck();

	Element.hide('page' + cur);
	Element.show('page' + prv);
}

function checkages()
{
	if(Form.Element.getValue('startage') > Form.Element.getValue('endage')) {
		$('startage').selectedIndex = 0;
	}
}
