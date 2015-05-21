function bind_menu_places_search_click()
{
	$("#menu_places_search").unbind('click');
	$("#menu_places_search").bind("click",
	function()
	{
		listPlacesButton_Click(true);
	});
}

function bind_menu_places_list_click()
{
	$("#menu_places_list").unbind('click');
	$("#menu_places_list").bind("click",
	function()
	{
		listPlacesButton_Click(false);
	});
}

function listPlacesButton_Click(search)
{
	hideRelevantMenus();
	selectedPlaceId = false;
	$("#form_container").hide();
	$("#flexi_container").show();
	$("#loading_container").hide();
	$("#flexi_container").html
	(
		"<table	class=\"flexi\"></table>"
	);
	$(".flexigrid").css({width:"100%"});
	$(".flexi").flexigrid
	(
		{
			url: 'include/places.php',
			params: [{name:'func',value:'list'}],
			dataType: 'json',
			colModel : 
			[
				{
					display: 'Name', 
					name : 'places_name', 
					width : 100, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Notes', 
					name : 'places_description', 
					width : 300, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Address', 
					name : 'places_address', 
					width : 100, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Type', 
					name : 'places_type', 
					width : 100, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Contact', 
					name : 'contact_name', 
					width : 100, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Email', 
					name : 'contact_email', 
					width : 100, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Active', 
					name : 'active', 
					width : 30, 
					sortable : true, 
					align: 'center'
				}
			],
			searchitems : 
			[
				{
					display: 'Name', 
					name : 'name'
				},
				{
					display: 'Description', 
					name : 'description'
				}
			],
			sortname: "places_name",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			qtype: "name",
			query: (search?"ß":""),
			rp: global_rp,
			singleSelect: true,
			width: 'auto',
			height: 'auto',
			onRowSelected:places_row_selected,
			onRowSelectedClick:places_row_selected_click,
			onRowDeselected:places_row_deselected,
			onNumRowsChange: onNumRowsChange,
			resizable: true
		}
	);
	if(search)
	{
		$('.sDiv').show();
		$('.qsbox').focus();
	}
	tb_places_list();
	$("#toolbar_container").show();
} 

function addPlaceButton_Click(com,grid)
{
	$("#flexi_container").hide();
	$("#loading_container").show();
	$("#form_container").load(
		"include/places.php",
		{func:'newPlaceForm'},
		function()
		{
			$('#placeForm').ajaxForm(
			{
				beforeSubmit:	
				function(formData, jqForm, options)
				{
					var out=checkPlaceForm("placeForm");
					if(out)
						flexiShowWait()
					return(out);
				},
				success:	function(data)
				{
					flexiShowFlexi()
					notify(data);
				}
			});
			$("#loading_container").hide();
			$("#toolbar_container").show();
			$("#form_container").show();
			$("#placeTypeSelect").focus();
			tb_add_place();
		}
	);
}

function editPlaceButton_Click(com,grid)
{
	if(selectedPlaceId)
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		$("#form_container").load(
			"include/places.php",
			{func:'editPlaceForm',placeId:selectedPlaceId},
			function() {
				$('#placeForm').ajaxForm(
				{
					beforeSubmit: function(formData, jqForm, options)
					{
						var out=checkPlaceForm("placeForm");
						if(out)
							flexiShowWait();
						return(out);
					},
					success: function(data)
					{
						flexiShowFlexi();
						notify(data);
						flexiReload();
					}
				});
				$("#loading_container").hide();
				$("#form_container").show();
				$("#placeTypeSelect").focus();
				tb_edit_place();
			}
		);
	}
}

function detailsPlaceButton_Click(com,grid)
{
	if(selectedPlaceId)
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		/*	load new user form into right frame	*/
		$("#form_container").load(
			"include/places.php",
			{func:'details',placeId:selectedPlaceId},
			function() {
				tb_details_place();
				$("#loading_container").hide();
				$("#form_container").show();
			}
		);
	}
}

function bind_menu_places_new_click() {
	$("#menu_places_new").unbind('click');
	$("#menu_places_new").bind(	"click",
	function () {
		addPlaceButton_Click(null,null);
	});
}

function bind_menu_places_edit_click()
{
	$("#menu_places_edit").unbind('click');
	$("#menu_places_edit").bind(	"click",
	function ()
	{
		editPlaceButton_Click(null,null);
	});
}


function checkPlaceForm(formId)
{
	/* prepare useful variables */
	var placeTypeInputSelector = "#"+formId+" #placeTypeSelect";
	var placeTypeCheckSelector = "#"+formId+" #placeTypeCheck";
	var simulatorInputSelector = "#"+formId+" #simulatorSelect";
	var simulatorCheckSelector = "#"+formId+" #simulatorCheck";
	var placeNameInputSelector = "#"+formId+" #placeNameInput";
	var placeNameCheckSelector = "#"+formId+" #placeNameCheck";
	var addressInputSelector = "#"+formId+" #addressInput";
	var addressCheckSelector = "#"+formId+" #addressCheck";
	var contactNameInputSelector = "#"+formId+" #contactNameInput";
	var contactNameCheckSelector = "#"+formId+" #contactNameCheck";
	var contactEmailInputSelector = "#"+formId+" #contactEmailInput";
	var contactEmailCheckSelector = "#"+formId+" #contactEmailCheck";

	var badColor = '#F00';
	var goodColor = '#0F0';
	var formIsGood = true;
	var placeExists = false;
	var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;
	
	if( $(placeTypeInputSelector).attr("value") == "" )
	{
		$(placeTypeCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - required");
		$(placeTypeCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(placeTypeCheckSelector)
			.html("&nbsp;");
	}
	
	if( $(simulatorInputSelector).attr("value") == "" )
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

	if( jQuery.trim($(placeNameInputSelector).attr("value")) == "" )
	{
		$(placeNameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - required");
		$(placeNameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(placeNameCheckSelector)
			.html("&nbsp;");
	}
	
	/* check if place + simulator is unique */
	if(	(jQuery.trim($(placeNameInputSelector).attr("value")) != "")
		&&
		($(simulatorInputSelector).attr("value") != "")
		&&
		($('#func').val()=='edit' || $('#func').val()=='add')
	)
	{
		if($('#func').val()=='add') {
			var ajaxData =	{
				whereString:"places.id_simulators=\"" +
				$(simulatorInputSelector).attr("value") +
				"\" AND " +
				"places.name=\"" +
				jQuery.trim($(placeNameInputSelector).attr("value")) +
				"\""
			};
		} else if($('#func').val()=='edit') {
			var ajaxData =	{
				whereString:"places.id_simulators=\"" +
				$(simulatorInputSelector).attr("value") +
				"\" AND " +
				"places.name=\"" +
				jQuery.trim($(placeNameInputSelector).attr("value")) +
				"\" AND " +
				"places.id != \"" + 
				$('#placeId').attr('value') +
				"\""
			}
		}
		$.ajax({
			type:'post',
			async:false,
			url:"include/places.php?func=list",
			data:ajaxData,
			dataType:'json',
			success:function(JSONdata,textStatus)
			{
				if(JSONdata.rows.length > 0)
				{
					placeExists = true;
					formIsGood = false;
				}
				if(placeExists == true)
				{
					$(placeNameCheckSelector)
						.html(	"<img src='img/stock_no_s.png' alt='error'>"
							+	" - place already exists for this simulator");
					$(placeNameCheckSelector)
						.css({color:badColor});
				}else
				{
					$(placeNameCheckSelector)
						.html("&nbsp;");
					$(placeNameCheckSelector)
						.css({color:goodColor});
				}
			}
		});
	}
	
	if((	jQuery.trim($(contactEmailInputSelector).attr("value")) != "" )
			&&
			!emailRegex.test(
				jQuery.trim($(contactEmailInputSelector).attr("value"))
			)
	)
	{
		$(contactEmailCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - incorrect email format");
		$(contactEmailCheckSelector)
			.css({color:badColor,padding:'0px'});
		formIsGood = false;
	}else
	{
		$(contactEmailCheckSelector)
			.html("&nbsp;");
		$(contactEmailCheckSelector)
			.css({color:goodColor});
	}
	
	return formIsGood;
}

function places_row_selected(placeId,row,grid)
{
	var enabled=(row[0].cells[row[0].cells.length-1].outerText.indexOf("YES")==0);
	
	$("#menu_places_edit").show();
	$("#tbDetails").show();
	$("#tbEdit").show();
	if(enabled)
	{
		$("#tbDeactivate").show();
		$("#tbReactivate").hide();
	}
	else
	{
		$("#tbDeactivate").hide();
		$("#tbReactivate").show();
	}

	selectedPlaceId = placeId.substr(3);
	//notify("users_row_click" + selectedUserId);
}

function places_row_selected_click(placeId,row,grid)
{
	editPlaceButton_Click(null,null);
}

/* callback when row is deselected in places table */
function places_row_deselected(placeId,row,grid)
{
	$("#menu_places_edit").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();

	selectedPlaceId = false;
	//notify("users_row_click" + selectedUserId);
}

function deactivatePlaceButton_Click()
{
	doActivatePlace(false)
}

function activatePlaceButton_Click()
{
	doActivatePlace(true);
}

function doActivatePlace(active)
{
	$.ajax({
		type:'post',
			async:true,
			url:"include/places.php",
			data:{
					func:"activate",
					active:(active?"1":"0"),
					placeId:selectedPlaceId
				},
			success:function(affectedRows)
			{
				if(Number(affectedRows)>0)
				{
					flexiReload();
				}
			}
		});
}
