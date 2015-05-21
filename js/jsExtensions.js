/**********************************************/
/* Add the trim functions to the string class */
/**********************************************/

/*************** TRIM *************************/
if (String.prototype.trim == null)
{
	String.prototype.trim = function()
	{
		var trimmedString = this;
		while (this.substring(0,1) == ' ')
		{
			trimmedString = this.substring(1, this.length);
		}
		while (this.substring(this.length-1, this.length) == ' ')
		{
			trimmedString = this.substring(0,this.length-1);
		}
		return trimmedString;
	}
}

/*************** LTRIM **********************/
if (String.prototype.ltrim == null)
{
	String.prototype.ltrim = function()
	{
        var trimmedString = this;
		while (this.substring(0,1) == ' ')
		{
			trimmedString = this.substring(1, this.length);
		}
		return trimmedString;
	}
}

/*************** RTRIM ************************/
if (String.prototype.rtrim == null)
{
	String.prototype.trim = function()
	{
        var trimmedString = this;
		while (this.substring(this.length-1, this.length) == ' ')
		{
			trimmedString = this.substring(0,this.length-1);
		}
		return trimmedString;
	}
}