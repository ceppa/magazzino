/*----------------------------------------------------------------------------*/
/*					PARTS	PARTS	PARTS	PARTS					*/
/*						PARTS	PARTS	PARTS						*/
/*					PARTS	PARTS	PARTS	PARTS					*/
/*----------------------------------------------------------------------------*/

function bind_menu_part_list_click()
{
	$("#menu_part_list").bind(	"click",
	function()
	{
		selectedPartId = false;
        $("#form_container").hide();
        $("#flexi_container").hide();
        $("#loading_container").show();

		/********************************/
		/* setup flexigrid parts table  */
		/********************************/
		
		/* reset flexi_container in case there's an active flexigrid already */
		$("#flexi_container").html
		(
			"<table	class=\"flexi\"></table>"
		);
		$(".flexi").flexigrid
		(
			{
				url: 'include/parts.php',
				params: [{name:'func',value:'list'}],
				dataType: 'json',
				colModel : 
				[
					{
						display: 'P/N', 
						name : 'parts_pn_sim', 
						width : 100, 
						sortable : true, 
						align: 'left'
					},
					{
						display: 'Supplier P/N', 
						name : 'parts.pn_supplier', 
						width : 100, 
						sortable : true, 
						align: 'left'
					},
					{
						display: 'Description', 
						name : 'parts.description', 
						width : 100, 
						sortable : true, 
						align: 'left'
					},
					{
						display: 'Simulator', 
						name : 'simulator', 
						width : 100, 
						sortable : true, 
						align: 'left'
					},
					{
						display: 'Supplier', 
						name : 'suppliers_name', 
						width : 100, 
						sortable : true, 
						align: 'center'
					}
				],
				buttons : 
				[
					{
						name: 'Add', 
						bclass: 'add', 
						onpress : addPartButton_Click
					},
                    {
						name: 'Delete', 
						bclass: 'delete', 
						onpress : deletePartButton_Click
					},
					{
						name: 'Deactivate', 
						bclass: 'deactivate', 
						onpress : deactivatePartButton_Click
					},
					{
						name: 'Reactivate', 
						bclass: 'activate', 
						onpress : activatePartButton_Click
					},
					{
						name: 'Edit', 
						bclass: 'edit', 
						onpress : editPartButton_Click
					}
				],
				searchitems : 
				[
					{
						display: 'P/N', 
						name : 'parts.pn_sim'
					},
					{
						display: 'Description', 
						name : 'parts.description'
					}
				],
				sortname: "parts_pn_sim",
				sortorder: "asc",
				usepager: true,
				useRp: true,
				rp: 15,
                singleSelect: true,
				width: 'auto',
				height: 'auto',
				onRowSelected:parts_row_selected,
				onRowDeselected:parts_row_deselected
			}
		);
		$(".flexigrid").css({width:"100%"});
        $("#loading_container").hide();
        $("#flexi_container").show();
		$(".flexigrid").show();
	});
}

function addPartButton_Click(com,grid)
{
    $("#flexi_container").hide();
    $("#loading_container").show();
    /*	load new user form into right frame	*/
    $("#form_container").load(
        "include/parts.php",
        {func:'newPartForm'},
        function()
        {
            $('#partForm').ajaxForm(
            {
                beforeSubmit:	
                function(formData, jqForm, options)
                {
                    return(checkPartForm("partForm"));
                },
                success:	function(data)
                {
                    //alert(data);
                    //$(".flexi").flexReload();
                    //editPartButton_Click(com,grid);
                }
            }
            );
            $("#loading_container").hide();
            $("#form_container").show();

        }
    );
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
			function()
			{
				$('#partForm').ajaxForm(
				{
					beforeSubmit:	
					function(formData, jqForm, options)
					{
						return(checkPartForm("partForm"));
					},
					success:	function(data)
					{
						alert(data);
						$(".flexi").flexReload();
						//editPartButton_Click(com,grid);
					}
				}
				);
                alert('$("#form_container").load() success!');
                $("#loading_container").hide();
                $("#form_container").show();
			}
		);
	}
}



function parts_row_selected(partId,row,grid)
{
	selectedPartId = partId.substr(3);
	//alert("users_row_click" + selectedUserId);
}

/* callback when row is deselected in users table */
function parts_row_deselected(partId,row,grid)
{
	selectedPartId = false;
	//alert("users_row_click" + selectedUserId);
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
					alert( msg );
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
					alert( msg );
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
					alert( msg );
					$(".flexi").flexReload();
				}
			});
		}
	}
}

function checkPartForm(formId)
{
	/* prepare useful variables */
	var pnAstaInputSelector = "#"+formId+" #pnAstaInput";
	var pnAstaCheckSelector = "#"+formId+" #pnAstaCheck";
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
	var pnAstaExists = false;
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
	if( jQuery.trim($(pnAstaInputSelector).attr("value")) == "" )
	{
		$(pnAstaCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - required");
		$(pnAstaCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(pnAstaCheckSelector)
			.html("&nbsp;");
	}
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
	
	/* check if simulator P/N already exists */
	if(	(jQuery.trim($(pnAstaInputSelector).attr("value")) != "")
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
							"parts.pn_asta = '" +
							jQuery.trim($(pnAstaInputSelector).attr("value")) +
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
					pnAstaExists = true;
					formIsGood = false;
				}
				if(pnAstaExists == true)
				{
					$(pnAstaCheckSelector)
						.html("<img src='img/stock_no_s.png' alt='error'> - exists");
					$(pnAstaCheckSelector)
						.css({color:badColor});
				}else
				{
					$(pnAstaCheckSelector)
						.html("&nbsp;");
					$(pnAstaCheckSelector)
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

/*----------------------------------------------------------------------------*/