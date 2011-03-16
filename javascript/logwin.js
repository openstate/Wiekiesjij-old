/** LogWin Object
  *
  * This object allows easy logging of messages to a connected XHTML page
  * element.
  **/

var LogWindow = Class.create();

LogWindow.prototype = {

	initialize:function(elem)
	{
		this.elem = $(elem);
		this.defcolor = "#666666";
		this.defelem = "p";
		this.fontsize = "10px";
		this.showdate = false;
		this.otherformatting = "line-height: 10px; padding: 0; margin: 0";
	},

	pl: function(text, color)
	{
		try {
			new Insertion.Top(this.elem, this._buildLine(text,color));
		}
		catch(e) {
			alert(e.name + ": " + e.message);
		}
	},

	al: function(text, color)
	{
		try {
			new Insertion.Bottom(this.elem, this._buildLine(text,color));
		}
		catch(e) {
			alert(e.name + ": " + e.message);
		}
	},

	clearLog: function()
	{
		this.elem.innerHTML = "";
	},

	_buildLine: function(text, color)
	{
		if(color == null) {
			color = this.defcolor;
		}
		return '<' + this.defelem + ' style="font-size: ' + this.fontsize + '; color: ' + color + '; ' + this.otherformatting + '">' + this._getTheTime() + " " + text + '</' + this.defelem + '>';
	},

	_getTheTime: function()
	{
		if(this.showdate) {
			var thedate = new Date();
			var hours = thedate.getHours().toString();
			var minutes = thedate.getMinutes().toString();
			var seconds = thedate.getSeconds().toString();

			if (seconds.length < 2) {	seconds = "0" + seconds; }

			if (minutes.length < 2) { minutes = "0" + minutes; }

			if (hours.length < 2) {	hours = "0" + hours; }

			return hours + ":" + minutes+ ":" + seconds;
		}
		return ""
	}
};
