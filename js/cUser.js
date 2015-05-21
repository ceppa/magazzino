/******************************************************************************/
/* Class:    cUser                                                            */
/* Purpose:  Represent a user within the application                          */
/* Requires: jsExtensions.js                                                  */
/******************************************************************************/

function CUser()
{
/************************/
/** PRIVATE PROPERTIES **/
/************************/
    var priId       = 0;
    var priUsername = '';
    var priName     = '';
    var priSurname  = '';
    var priEmail    = '';
    var priActive   = false;
    var priExpired  = false;
    var that        = this;

/**********************************/
/** ACCESS METHOD "DECLARATIONS" **/
/**********************************/
    this.getId       = getId;
    this.setId       = setId;
    this.getUsername = getUsername;
    this.setUsername = setUsername;
    this.getName     = getName;
    this.setName     = setName;
    this.getSurname  = getSurname;
    this.setSurname  = setSurname;
    this.getEmail    = getEmail;
    this.setEmail    = setEmail;
    this.isActive    = isActive;
    this.isExpired   = isExpired;

/*************************************/
/** ACCESS METHOD "IMPLEMENTATIONS" **/
/*************************************/
	function getId(){
		return priId;
	}
	function setId(id){
		if(!isNaN(parseInt(id,10)))
		{
			priId = parseInt(id,10);
		}
		else
		{
			return false;
		}
	}
	function getUsername(){
		return priUsername;
	}
	function setUsername(username){
		priUsername = username.trim();
		return this;
	}
	function getName(){
		return priName;
	}
	function setName(name){
		priName = name.trim();
		return this;
	}
	function getSurname(){
		return priSurname;
	}
	function setSurname(surname){
		priSurname = surname.trim();
		return this;
	}
	function getEmail(){
		return priEmail;
	}
	function setEmail(email){
		var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;
		if(emailRegex.test(email.trim()))
		{
			priEmail = email;
			return this;
		}
		else
		{
			return false;
		}
	}

	// ritorna il valore di active se chiamata senza parametri
	// accetta boolean, number o string per settare il valore active 
	function isActive(optionalBool)
	{
		if( optionalBool == undefined )
		{
			return priActive;
		}
		else if( typeof(optionalBool) == 'boolean' )
		{
			priActive = optionalBool;
			return this;
		}
		else if( typeof(optionalBool) == 'number' ) 
		{
			if( optionalBool == 0 )
			{
				priActive = false;
				return this;
			}
			else if( optionalBool == 1 )
			{
				priActive = true;
				return this;
			}
		}
		else if( typeof(optionalBool) == 'string' ) 
		{
			if( optionalBool == 'false' )
			{
				priActive = false;
				return this;
			}
			else if( optionalBool == 'true' )
			{
				priActive = true;
				return this;
			}
		}
	}

	// ritorna il valore di active se chiamata senza parametri
	// accetta boolean, number o string per settare il valore active 
	function isExpired(optionalBool)
	{
		if( optionalBool == undefined )
		{
			return priExpired;
		}
		else if( typeof(optionalBool) == boolean )
		{
			priExpired = optionalBool;
			return this;
		}
		else if( typeof(optionalBool) == 'number' ) 
		{
			if( optionalBool == 0 )
			{
				priExpired = false;
				return this;
			}
			else if( optionalBool == 1 )
			{
				priExpired = true;
				return this;
			}
		}
		else if( typeof(optionalBool) == 'string' ) 
		{
			if( optionalBool == 'false' )
			{
				priExpired = false;
				return this;
			}
			else if( optionalBool == 'true' )
			{
				priExpired = true;
				return this;
			}
		}
	}
}