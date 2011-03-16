Event.observe(window, 'load', mainInit, false);

function mainInit()
{
	daysUntil();
	//initMenu()
}

function fold(elem) {
	var nextnode = elem.nextSibling;
	while (nextnode.nodeType == 3) {
		nextnode = nextnode.nextSibling;
	}
	if (nextnode.style.display != 'block') {
		nextnode.style.display = 'block';
//		elem.style.backgroundImage = "url('layout/arr_down.gif')";
	}
	else {
		nextnode.style.display = 'none';
//		elem.style.backgroundImage = "url('layout/arr_right.gif')";
	}
}

function daysUntil() {
	if($("countdown")) {
		var today = new Date();
		var difference = Date.UTC(2006,11,22) - Date.UTC(today.getFullYear(),today.getMonth()+1,today.getDate(),0,0,0);
		$("countdown").innerHTML = "<p style=\"line-height: 1em; font-size: 0.7em; font-weight: bold\">Nog " + difference/1000/60/60/24 + " dagen tot de Tweede Kamerverkiezingen!</p>";
	}
}

var menu;
var theTop = 0;
var topDiff = 440;
var old = theTop;

function initMenu() {
	menu = $('slidemenu');
	movemenu();

}

function movemenu()
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


	if (pos == old) 	{
		menu.style.marginTop = old + "px";
	}

	old= pos;
	temp = setTimeout('movemenu()',200);
}

function toggleView(strid) {
	var elm = $(strid);
	if(elm.style.display == "block") {
		elm.style.display = "none";
	}
	else {
		elm.style.display = "block";
		location.hash = strid;
	}
}

var base;
var runanimation = false;
var sinSteps = 200;
var sin = null;
var cos = null;
var counter = 0;

function toggleanim()
{
	runanimation = !runanimation;

	if(runanimation) {
		sin = new Array(sinSteps);
		cos = new Array(sinSteps);
		for(i=0; i<sinSteps; i++) {
			sin[i] = Math.round(sinSteps*Math.sin((i/sinSteps)*2*Math.PI));
			cos[i] = Math.round(sinSteps*Math.cos((i/sinSteps)*2*Math.PI));
		}
	}
	else {
		sin = null;
		cos = null;
		base.style.backgroundPosition = '0px 0px';
	}

	base = $("base");
	animate();
}

function animate()
{
	if(runanimation) {
		if(counter<sinSteps) {
			base.style.backgroundPosition =  cos[counter]+'px ' + sin[counter]+'px';
			counter++;
		}
		else {
			counter = 0;
			base.style.backgroundPosition =  cos[counter]+'px ' + sin[counter]+'px';
		}
		setTimeout("animate()",20);
	}
}

var eventFunctions = new Array();

function enableButton(thebutton, enable)
{
	if(enable) {
		if($(thebutton).onclick == null) {
			$(thebutton).style.display = "block";
			$(thebutton).style.color = "black";
			$(thebutton).style.cursor = "pointer";
	 		$(thebutton).onclick = eventFunctions[thebutton];
	 		try {
	 			logwin.pl(thebutton + " enabled");
	 		}
	 		catch(e) {
	 			//
	 		}
	 	}
	}
	else {
		if($(thebutton).onclick != null) {
			$(thebutton).style.color = "#b4afa9";
			$(thebutton).style.cursor = "default";
			eventFunctions[thebutton] = $(thebutton).onclick;
			$(thebutton).onclick = null;
			try {
	 			logwin.pl(thebutton + " disabled");
	 		}
	 		catch(e) {
	 			//
	 		}
		}
	}
}

function postForm(formid, postaction)
{
	var postString = "";
	activeForm = $(formid);

 	for(var i = 0;i < activeForm.elements.length;i++)
	{
		switch(activeForm.elements[i].type)
	    {
		  	case "text":
		  	case "hidden":
		  	case "textarea":
	      	postString += activeForm.elements[i].name + "=" + escape(activeForm.elements[i].value) + "&";
					break;
				case "radio":
					if(activeForm.elements[i].checked) {
						postString += activeForm.elements[i].name + "=" + escape(activeForm.elements[i].value) + "&";
					}
					break;
				default:
					//alert(activeForm.elements[i].type);
	  	}
	}

	postString = postString.substr(0,(postString.length - 1));

	//alert(postString);
}

function showCascadeResult()
{
	if (httpObject.readyState != 4 || httpObject.status != 200) return false;

	try {
		// Get the status value and message node.
		//alert(httpObject.responseText);
		var status = httpObject.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
		var message = httpObject.responseXML.getElementsByTagName('message')[0];
		// Dig down to the message node's text value
		while(message.nodeType != 3  && message.hasChildNodes()) {
			message = message.firstChild;
		}
		if(status == 1) {
			document.getElementById("emailaddress").value = "";
		}

		alert(message.nodeValue);
	}
	catch (e) {
		alert("-Error: " + e.mess + "\n" + e);
	}
}
