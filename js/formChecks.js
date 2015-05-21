function checkUserForm(formId)
{

	/* prepare useful variables */
	var userNameInputSelector = "#"+formId+" #userNameInput";
	var userNameCheckSelector = "#"+formId+" #userNameCheck";
	var nameInputSelector = "#"+formId+" #nameInput";
	var nameCheckSelector = "#"+formId+" #nameCheck";
	var surnameInputSelector = "#"+formId+" #surnameInput";
	var surnameCheckSelector = "#"+formId+" #surnameCheck";
	var emailInputSelector = "#"+formId+" #emailInput";
	var emailCheckSelector = "#"+formId+" #emailCheck";
	var badColor = '#F00';
	var goodColor = '#0F0';
	var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;
	var formIsGood = true;
	var usernameExists = false;

	/* test empty username */
	if( jQuery.trim($(userNameInputSelector).attr("value")) == "" )
	{
		$(userNameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - username required");
		$(userNameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		if($('#func').val()=='add')
		{
			var ajaxData = "";
		}
		else if($('#func').val()=='edit')
		{
			var ajaxData = {	
							qtype:"users.id",
							qcond:"not like",
							query:selectedUserId
						};
		}
		$.ajax({
			type:'post',
			async:false,
			url:"include/users.php?func=list",
			data:ajaxData,
			dataType:'json',
			success:function(JSONdata,textStatus)
			{
				jQuery.each(	JSONdata.rows,
					function()
					{
						if	(	this.cell[0] == 
							jQuery.trim	(
								$(userNameInputSelector).attr("value")
										)
							)
						{
							formIsGood = false;
							usernameExists = true;
						}
					}
				);
				if(usernameExists==true)
				{
					$(userNameCheckSelector)
						.html("<img src='img/stock_no_s.png' alt='error'> - username already exists");
					$(userNameCheckSelector)
						.css({color:badColor});
				}else
				{
					$(userNameCheckSelector)
						.html("<img src='img/stock_ok_s.png' alt='ok'>");
					$(userNameCheckSelector)
						.css({color:goodColor});
				}
			}
		});
	}

	if( jQuery.trim($(nameInputSelector).attr("value")) == "" )
	{
		$(nameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - name required");
		$(nameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(nameCheckSelector)
			.html("<img src='img/stock_ok_s.png' alt='ok'>");
		$(nameCheckSelector)
			.css({color:goodColor});
	}

	if( jQuery.trim($(surnameInputSelector).attr("value")) == "" )
	{
		$(surnameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - surname required");
		$(surnameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(surnameCheckSelector)
			.html("<img src='img/stock_ok_s.png' alt='ok'>");
		$(surnameCheckSelector)
			.css({color:goodColor});
	}
	
	
	if( jQuery.trim($(emailInputSelector).attr("value")) == "" )
	{
		$(emailCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - email address required");
		$(emailCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else 
	if	(!emailRegex.test(
			jQuery.trim($(emailInputSelector).attr("value"))
			)
		)
	{
		$(emailCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - incorrect email format");
		$(emailCheckSelector)
			.css({color:badColor,padding:'0px'});
		formIsGood = false;
	}else
	{
		$(emailCheckSelector)
			.html("<img src='img/stock_ok_s.png' alt='ok'>");
		$(emailCheckSelector)
			.css({color:goodColor});
	}
	
	return(formIsGood);
}

function checkWarehouseForm(formId)
{

	/* prepare useful variables */
	var nameInputSelector = "#"+formId+" #nameInput";
	var nameCheckSelector = "#"+formId+" #warehouseNameCheck";
	var badColor = '#F00';
	var goodColor = '#0F0';
	var formIsGood = true;
	var nameExists = false;
	
	/* test empty username */
	if( jQuery.trim($(nameInputSelector).attr("value")) == "" )
	{
		$(nameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - name required");
		$(nameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		if($('#func').val()=='add')
		{
			var ajaxData = "";
		}
		else if($('#func').val()=='edit')
		{
			var ajaxData =	{	
								qtype:"places.id",
								qcond:"not like",
								query:selectedWarehouseId
							};
		}
		$.ajax({
			type:'post',
			async:false,
			url:"include/warehouses.php?func=list",
			data:ajaxData,
			dataType:'json',
			success:function(JSONdata,textStatus)
			{
				jQuery.each(	JSONdata.rows,
					function()
					{
						if	(	this.cell[0] == 
								jQuery.trim	
								(
									$(nameInputSelector).attr("value")
								)
							)
						{
							formIsGood = false;
							nameExists = true;
						}
					}
				);
				if(nameExists==true)
				{
					$(nameCheckSelector)
						.html("<img src='img/stock_no_s.png' alt='error'> - warehouse name already exists");
					$(nameCheckSelector)
						.css({color:badColor});
				}else
				{
					$(nameCheckSelector)
						.html("<img src='img/stock_ok_s.png' alt='ok'>");
					$(nameCheckSelector)
						.css({color:goodColor});
				}
			}
		});
	}
	return formIsGood;
}

function checkPartForm(formId)
{
	/* prepare useful variables */
	var pnManInputSelector = "#"+formId+" #pnManInput";
	var pnManCheckSelector = "#"+formId+" #pnManCheck";
	var pnSupplierInputSelector = "#"+formId+" #pnSupplierInput";
	var pnSupplierCheckSelector = "#"+formId+" #pnSupplierCheck";
	var simulatorInputSelector = "#"+formId+" #simulatorSelect";
	var simulatorCheckSelector = "#"+formId+" #simulatorCheck";
	var supplierInputSelector = "#"+formId+" #supplierSelect";
	var supplierCheckSelector = "#"+formId+" #supplierCheck";
	var nameInputSelector = "#"+formId+" #nameInput";
	var nameCheckSelector = "#"+formId+" #partNameCheck";
	var badColor = '#F00';
	var goodColor = '#0F0';
	var formIsGood = true;
	var pnManExists = false;
	var pnSupplierExists = false;
	
	if( jQuery.trim($(nameInputSelector).attr("value")) == "" )
	{
		$(nameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - part name required");
		$(nameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(nameCheckSelector)
			.html("&nbsp;");
	}
	if( jQuery.trim($(pnManInputSelector).attr("value")) == "" )
	{
		$(pnManCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - simulator P/N required");
		$(pnManCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(pnManCheckSelector)
			.html("&nbsp;");
	}
	if( jQuery.trim($(pnSupplierInputSelector).attr("value")) == "" )
	{
		$(pnSupplierCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - supplier P/N required");
		$(pnSupplierCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(pnSupplierCheckSelector)
			.html("&nbsp;");
	}
	if($(simulatorInputSelector).attr("value") == "")
	{
		$(simulatorCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - must select a simulator");
		$(simulatorCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(simulatorCheckSelector)
			.html("&nbsp;");
	}
	if($(supplierInputSelector).attr("value") == "")
	{
		$(supplierCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - must select a supplier");
		$(supplierCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(supplierCheckSelector)
			.html("&nbsp;");
	}
	
	/* check if simulator P/N already exists */
	if(	(jQuery.trim($(pnManInputSelector).attr("value")) != "")
		&&
		($(simulatorInputSelector).attr("value") != "")
		&&
		($('#func').val()=='edit' || $('#func').val()=='add')
	)
	{
		var ajaxData =	{	
							qtype:"parts.id_simulators = '" +
							$(simulatorInputSelector).attr("value") +
							"' AND " +
							"parts.pn_man = '" +
							jQuery.trim($(pnManInputSelector).attr("value")) +
							"' AND " +
							"parts.id != '" + selectedPartId + "'"
		};
		$.ajax({
			type:'post',
			async:false,
			url:"include/parts.php?func=list",
			data:ajaxData,
			dataType:'json',
			success:function(JSONdata,textStatus)
			{
				if(JSONdata.rows.length > 0)
				{
					pnManExists = true;
					formIsGood = false;
				}
				if(pnManExists == true)
				{
					$(pnManCheckSelector)
						.html("<img src='img/stock_no_s.png' alt='error'> - exists");
					$(pnManCheckSelector)
						.css({color:badColor});
				}else
				{
					$(pnManCheckSelector)
						.html("&nbsp;");
					$(pnManCheckSelector)
						.css({color:goodColor});
				}
			}
		});
	}

	/* check if supplier P/N already exists */
	if(	(jQuery.trim($(pnSupplierInputSelector).attr("value")) != "")
		&&
		($(supplierInputSelector).attr("value") != "")
		&&
		($('#func').val()=='edit' || $('#func').val()=='add')
	)
	{
		var ajaxData =	{	
							qtype:"parts.id_suppliers = '" +
							$(supplierInputSelector).attr("value") +
							"' AND " +
							"parts.pn_supplier = '" +
							jQuery.trim($(pnSupplierInputSelector).attr("value")) +
							"' AND " +
							"parts.id != '" + selectedPartId + "'"
		};
		$.ajax({
			type:'post',
			async:false,
			url:"include/parts.php?func=list",
			data:ajaxData,
			dataType:'json',
			success:function(JSONdata,textStatus)
			{
				if(JSONdata.rows.length > 0)
				{
					pnSupplierExists = true;
					formIsGood = false;
				}
				if(pnSupplierExists == true)
				{
					$(pnSupplierCheckSelector)
						.html("<img src='img/stock_no_s.png' alt='error'> - exists");
					$(pnSupplierCheckSelector)
						.css({color:badColor});
				}else
				{
					$(pnSupplierCheckSelector)
						.html("&nbsp;");
					$(pnSupplierCheckSelector)
						.css({color:goodColor});
				}
			}
		});
	}
	return formIsGood;
}
