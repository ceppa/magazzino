/******************************************************************************/
/* Class:    cItem                                                            */
/* Purpose:  Represent a unique item or collection of nonunique items         */
/* Requires: jsExtensions.js                                                  */
/******************************************************************************/

function cItem()
{
/****************/
/** PROPERTIES **/
/****************/
	this.supplierPN			= '';
	this.simPN				= '';
	this.SN					= '';
	this.quantity			= 0;

/**********************************/
/** ACCESS METHOD "DECLARATIONS" **/
/**********************************/
	this.getSupplierPN		= getSupplierPN;
	this.setSupplierPN		= setSupplierPN;
	this.getsimPN			= getSimPN;
	this.setSimPN			= setSimPN;
	this.getSN				= getSN;
	this.setSN				= setSN;
	this.getQuantity		= getQuantity;
	this.setQuantity		= setQuantity;

/*************************************/
/** ACCESS METHOD "IMPLEMENTATIONS" **/
/*************************************/
	function getSupplierPN(){
		return this.supplierPN;
	}
	function setSupplierPN(supplierPN){
		this.supplierPN = supplierPN;
	}
	function getSimPN(){
		return this.simPN;
	}
	function setSimPN(simPN){
		this.simPN = simPN;
	}
	function getSN(){
		return this.SN;
	}
	function setSN(SN){
		this.SN = SN;
	}
	function getQuantity(){
		return this.quantity;
	}
	function setQuantity(quantity){
		this.quantity = quantity;
	}
}