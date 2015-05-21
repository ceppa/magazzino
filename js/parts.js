/*----------------------------------------------------------------------------*/
/*					PARTS	PARTS	PARTS	PARTS					*/
/*						PARTS	PARTS	PARTS						*/
/*					PARTS	PARTS	PARTS	PARTS					*/
/*----------------------------------------------------------------------------*/

function bind_menu_part_list_click()
{
	$("#menu_part_list").unbind('click');
	$("#menu_part_list").bind(	"click",
	function()
	{
		listPartsButton_Click(false);
	});
}

function bind_menu_part_search_click()
{
	$("#menu_part_search").unbind('click');
	$("#menu_part_search").bind("click",
	function()
	{
		listPartsButton_Click(true);
	});
}




function listPartsButton_Click(search)
{
	hideRelevantMenus();
	selectedPartId = false;
	$("#form_container").hide();
	$("#flexi_container").show();
	$("#loading_container").hide();
	/********************************/
	/* setup flexigrid parts table  */
	/********************************/
	
	/* reset flexi_container in case there's an active flexigrid already */
	$("#flexi_container").html
	(
		"<table	class=\"flexi\"></table>"
	);
	$(".flexigrid").css({width:"100%"});
	$(".flexi").flexigrid
	(
		{
			url: 'include/parts.php',
			params: [{name:'func',value:'list'}],
			dataType: 'json',
			colModel : 
			[
				{
					display: 'Supplier P/N', 
					name : 'parts.pn_supplier', 
					width : 150, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Manufacturer P/N', 
					name : 'parts.pn_manufacturer', 
					width : 150, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Description', 
					name : 'parts.description', 
					width : 150, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Subsystem', 
					name : 'subsystem', 
					width : 150, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Supplier', 
					name : 'suppliers_name', 
					width : 150, 
					sortable : true, 
					align: 'center'
				},
				{
					display: 'Manufacturer', 
					name : 'manufacturers_name', 
					width : 150, 
					sortable : true, 
					align: 'center'
				},
				{
					display: 'Fotografia', 
					name : 'fotografia', 
					width : 150, 
					sortable : true, 
					align: 'center'
				}
			],
			searchitems : 
			[
				{
					display: 'P/N', 
					name : 'pn',
					isdefault: true
				},
				{
					display: 'Description', 
					name : 'parts.description'
				},
				{
					display: 'Subsystem', 
					name : 'subsystem'
				},
				{
					display: 'Supplier', 
					name : 'supplier'
				},
				{
					display: 'Manufacturer', 
					name : 'manufacturer'
				}
			],
			sortname: "parts.pn_manufacturer",
			sortorder: "asc",
			usepager: true,
			singleSelect: true,
			useRp: true,
			rp: global_rp,
			qtype: "subsystem",
			query: (search?"ß":""),
			width: 'auto',
			height: 'auto',
			onRowSelected:parts_row_selected,
			onRowSelectedClick:parts_row_selected_click,
			onRowDeselected:parts_row_deselected,
			onNumRowsChange: onNumRowsChange
		}
	);
	if(search)
	{
		$('.sDiv').show();
		$('.qsbox').focus();
	}
	tb_parts_list();
	$("#toolbar_container").show();
}



function editPartButton_Click(com,grid)
{
	if(selectedPartId)
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		/*	load new user form into right frame	*/
		$("#form_container").load(
			"include/parts.php",
			{func:'editPartForm',partId:selectedPartId},
			function() {
				$('#partForm').ajaxForm(
				{
					beforeSubmit:	
					function(formData, jqForm, options)
					{
						var out=checkPartForm("partForm");
						if(out)
							flexiShowWait()
						return(out);
					},
					success:	function(data)
					{
						flexiShowFlexi()
						notify(data);
						tb_parts_list();
						flexiReload();
					}
				});
				tb_edit_part();
				$("#loading_container").hide();
				$("#form_container").show();
			}
		);
	}
}

function detailsPartButton_Click(com,grid)
{
	if(selectedPartId)
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		/*	load new user form into right frame	*/
		$("#form_container").load(
			"include/parts.php",
			{func:'details',partId:selectedPartId},
			function() {
				tb_details_part();
				$("#loading_container").hide();
				$("#form_container").show();
			}
		);
	}
}

function bind_menu_part_edit_click() {
	$("#menu_part_edit").unbind('click');
	$("#menu_part_edit").bind(	"click",
	function () {
		editPartButton_Click();
	});
}

function parts_row_selected(partId,row,grid)
{
	$("#menu_part_edit").show();
	$("#tbDetails").show();
	$("#tbEdit").show();

	selectedPartId = partId.substr(3);
	//notify("users_row_click" + selectedUserId);
}

function parts_row_selected_click(partId,row,grid)
{
	$("#menu_part_edit").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();

	editPartButton_Click(null,null);
}

/* callback when row is deselected in users table */
function parts_row_deselected(partId,row,grid)
{
	selectedPartId = false;
	//notify("users_row_click" + selectedUserId);
}

function deletePartButton_Click(com,grid)
{
	if(selectedPartId)
	{
		if(confirm("Really delete part " + selectedPartId + "?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/parts.php",
				data:	"func=delete&partId="+selectedPartId,
				success: function(msg)
				{
					notify( msg );
					$(".flexi").flexReload();
				}
			});
		}
	}
}

function deactivatePartButton_Click(com,grid)
{
	if(selectedPartId)
	{
		if(confirm("Really deactivate part " + selectedPartId + "?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/parts.php",
				data:	"func=deactivate&partId="+selectedPartId,
				success: function(msg)
				{
					notify( msg );
					$(".flexi").flexReload();
				}
			});
		}
	}
}

function activatePartButton_Click(com,grid)
{
	if(selectedPartId)
	{
		if(confirm("Really activate part " + selectedPartId + "?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/parts.php",
				data:	"func=activate&partId="+selectedPartId,
				success: function(msg)
				{
					notify( msg );
					$(".flexi").flexReload();
				}
			});
		}
	}
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
	var subsystemInputSelector = "#"+formId+" #subsystemSelect";
	var subsystemCheckSelector = "#"+formId+" #subsystemCheck";
/*
	var nameInputSelector = "#"+formId+" #nameInput";
	var nameCheckSelector = "#"+formId+" #partNameCheck";
*/
	var badColor = '#F00';
	var goodColor = '#0F0';
	var formIsGood = true;
	var pnSupplierExists = false;
	var pnManExists = false;
/*
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
			.html("<img src='img/stock_no_s.png' alt='error'> - required");
		$(pnManCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}
	else
	{
		$(pnManCheckSelector)
			.html("&nbsp;");
	}
*/
	if( jQuery.trim($(pnSupplierInputSelector).attr("value")) == "" )
	{
		$(pnSupplierCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - required");
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
			.html("<img src='img/stock_no_s.png' alt='error'> - required");
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
			.html("<img src='img/stock_no_s.png' alt='error'> - required");
		$(supplierCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(supplierCheckSelector)
			.html("&nbsp;");
	}
	if($(subsystemInputSelector).attr("value") == "")
	{
		$(subsystemCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - required");
		$(subsystemCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(subsystemCheckSelector)
			.html("&nbsp;");
	}

	/* check if simulator P/N already exists */
/*
	if(	(jQuery.trim($(pnManInputSelector).attr("value")) != "")
		&&
		($(simulatorInputSelector).attr("value") != "")
		&&
		($('#func').val()=='edit' || $('#func').val()=='add')
	)
	{
		var ajaxData =	{
			whereString: "parts.pn_manufacturer=\"" +
			jQuery.trim($(pnManInputSelector).attr("value")) +
			"\" AND " +
			"parts.id!=\"" + selectedPartId + "\""
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
					$(pnManCheckSelector)
						.html("<img src='img/stock_no_s.png' alt='error'> - exists");
					$(pnManCheckSelector)
						.css({color:badColor});
				}
				else
				{
					$(pnManCheckSelector)
						.html("&nbsp;");
					$(pnManCheckSelector)
						.css({color:goodColor});
				}
			}
		});
	}
*/

	/* check if supplier P/N already exists */
	if(	(jQuery.trim($(pnSupplierInputSelector).attr("value")) != "")
		&&
		($(supplierInputSelector).attr("value") != "")
		&&
		(jQuery.trim($(pnManInputSelector).attr("value")) != "")
		&&
		($('#func').val()=='edit' || $('#func').val()=='add')
		)
	{
		var ajaxData =	{	
			whereString:"parts.id_suppliers = \"" +
			$(supplierInputSelector).attr("value") +
			"\" AND " +
			"parts.pn_supplier = \"" +
			jQuery.trim($(pnSupplierInputSelector).attr("value")) +
			"\" AND " +
			"parts.pn_manufacturer = \"" +
			jQuery.trim($(pnManInputSelector).attr("value")) +
			"\" AND " +
			"parts.id != \"" + selectedPartId + "\"",
			func:'list'
		};
		$.ajax({
			type:'post',
			async:false,
			url:"include/parts.php",
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

/*----------------------------------------------------------------------------*/
