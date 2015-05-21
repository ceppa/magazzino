/*----------------------------------------------------------------------------*/
/*																	*/
/*							WAREHOUSES								*/
/*																	*/
/*----------------------------------------------------------------------------*/

function bind_btn_magazzini_click()
{
	$("#btn_magazzini").bind(	"click",
	function()
	{
		selectedWarehouseId = false;
		$("#right_frame_1 > .frame_title").text("Loading");

		$("#right_frame_1 > .frame_body").html(
			"<img 	id='loading_img'"
				+"	src='img/bball.gif'" 
				+"	alt='loading...'/>"
				+"	<br/>Loading warehouse list");

		$("#right_frame_1 > .frame_body")
			.css({textAlign:'center'});

		/********************************/
		/* setup flexigrid users table  */
		/********************************/
		
		/* reset flexi_container in case there's an active flexigrid already */
		$("#flexi_container").html
		(
			"<table	class=\"flexi\"></table>"
		);
		$(".flexi").flexigrid
		(
			{
				url: 'include/warehouses.php',
				params: [{name:'func',value:'list'}] ,
				dataType: 'json',
				colModel : 
				[
					{
						display: 'Name', 
						name : 'places_name', 
						width : 150, 
						sortable : true, 
						align: 'left'
					},
					{
						display: 'Description', 
						name : 'places_description', 
						width : 200, 
						sortable : true, 
						align: 'left'
					},
					{
						display: 'Type', 
						name : 'places_types_name', 
						width : 150, 
						sortable : true, 
						align: 'left'
					},
					{
						display: 'Active', 
						name : 'places_active', 
						width : 150, 
						sortable : true, 
						align: 'center'
					}
				],
				buttons : 
				[
					{
						name: 'Add', 
						bclass: 'add', 
						onpress : addWarehouseButton_Click
					},
					{
						name: 'Deactivate', 
						bclass: 'delete', 
						onpress : deactivateWarehouseButton_Click
					},
					{
						name: 'Reactivate', 
						bclass: 'activate', 
						onpress : activateWarehouseButton_Click
					},
					{
						name: 'Edit', 
						bclass: 'edit', 
						onpress : editWarehouseButton_Click
					}
				],
				/*searchitems : 
				[
					{
						display: 'Username', 
						name : 'username'
					},
					{
						display: 'Name', 
						name : 'name', 
						isdefault: true
					}
				],*/
				sortname: "places_name",
				sortorder: "asc",
				usepager: true,
				title: 'Warehouses',
				useRp: true,
				rp: 10,
				/*showTableToggleBtn: true,*/
				width: 'auto',
				height: 'auto',
				onRowSelected:warehouses_row_selected,
				onRowDeselected:warehouses_row_deselected
			}
		);

		$("#right_frame_1").hide();
		$(".flexigrid").css({width:"940px"});
		$(".flexigrid").show();
	});
}

function addWarehouseButton_Click(com,grid)
{
	/*shrink flexigrid user table*/
	$(".flexigrid").animate
	(
		{
			width:"460px"
		},
		600
	);

	$("#right_frame_1").removeClass("grid_12");
	$("#flexi_container").removeClass("grid_12");
	$("#right_frame_1").addClass("grid_6");
	$("#flexi_container").addClass("grid_6");
	$("#right_frame_1").show();

	/* 	set loading graphics and text in right frame while loading
		new user form	*/
	$("#right_frame_1 > .frame_title").text("Loading");

	$("#right_frame_1 > .frame_body").html(
		"<img 	id='loading_img'"
			+"	src='img/bball.gif'" 
			+"	alt='loading...'/>"
			+"	<br/>Loading warehouse form");

	$("#right_frame_1 > .frame_body")
		.css({textAlign:'center'});

	/*	load new user form into right frame	*/
	$("#right_frame_1 > .frame_body").load(
		"include/warehouses.php",
		{func:'newWarehouseForm'},
		function()
		{
			$("#right_frame_1 > .frame_body").css(
				{textAlign:'left'}
			);
			
			/**********************************/
			/* Setup right frame close button */
			/**********************************/
			$("#right_frame_1 > .frame_title")
				.html("	<span class='close_button'>"+
							"Close"+
						"</span>&nbsp;Add warehouse");
			$(".close_button").hover
			(
				function()
				{
					$(this).css({cursor:'pointer'});
				},
				function()
				{
					$(this).css({cursor:'default'});
				}
			);
			$(".close_button").click
			(
				function()
				{
					$("#flexi_container").removeClass("grid_6");
					$("#flexi_container").addClass("grid_12");
					$(".flexigrid").animate
					(
						{
							width:"940px"
						},
						600
					);
					$("#right_frame_1").hide();
				}
			);

			$('#warehouseForm').ajaxForm(
			{
				beforeSubmit:	
				function(formData, jqForm, options)
				{
					return(checkWarehouseForm("warehouseForm"));
				},
				success:	function(data)
				{
					notify(data);
					$(".flexi").flexReload();
				}
			}
			);
		}
	);
}

function editWarehouseButton_Click(com,grid)
{
	if(selectedWarehouseId)
	{
		/*shrink flexigrid user table*/
		$(".flexigrid").animate
		(
			{
				width:"460px"
			},
			600
		);

		$("#right_frame_1").removeClass("grid_12");
		$("#flexi_container").removeClass("grid_12");
		$("#right_frame_1").addClass("grid_6");
		$("#flexi_container").addClass("grid_6");
		$("#right_frame_1").show();

		/* 	set loading graphics and text in right frame while loading
			new user form	*/
		$("#right_frame_1 > .frame_title").text("Loading");

		$("#right_frame_1 > .frame_body").html(
			"<img 	id='loading_img'"
				+"	src='img/bball.gif'" 
				+"	alt='loading...'/>"
				+"	<br/>Loading warehouse form");

		$("#right_frame_1 > .frame_body")
			.css({textAlign:'center'});

		/*	load edit user form into right frame	*/
		$("#right_frame_1 > .frame_body").load(
			"include/warehouses.php",
			{func:'editWarehouseForm',warehouseId:selectedWarehouseId},
			function(){

				$("#right_frame_1 > .frame_body").css(
					{textAlign:'left'}
				);

				/**********************************/
				/* Setup right frame close button */
				/**********************************/
				$("#right_frame_1 > .frame_title")
					.html("	<span class='close_button'>"+
								"Close"+
							"</span>&nbsp;Edit warehouse");
				$(".close_button").hover
				(
					function()
					{
						$(this).css({cursor:'pointer'});
					},
					function()
					{
						$(this).css({cursor:'default'});
					}
				);
				$(".close_button").click
				(
					function()
					{
						$("#flexi_container").removeClass("grid_6");
						$("#flexi_container").addClass("grid_12");
						$(".flexigrid").animate
						(
							{
								width:"940px"
							},
							600
						);
						$("#right_frame_1").hide();
					}
				);

				$('#warehouseForm').ajaxForm(
				{
					beforeSubmit:	
					function(formData, jqForm, options)
					{
						return checkWarehouseForm("warehouseForm");
					},
					success:	function(data)
					{
						notify(data);
						$(".flexi").flexReload();
					}
				}
				);
			}
		);
	}
}

/* callback when row is selected in warehouses table */
function warehouses_row_selected(warehouseId,row,grid)
{
	selectedWarehouseId = warehouseId;
	//notify("users_row_click" + selectedWarehouseId);
}

/* callback when row is deselected in warehouses table */
function warehouses_row_deselected(warehouseId,row,grid)
{
	selectedWarehouseId = false;
	//notify("users_row_click" + selectedWarehouseId);
}

function deactivateWarehouseButton_Click(com,grid)
{
	if(selectedWarehouseId)
	{
		if(confirm("Really deactivate warehouse " + selectedWarehouseId + "?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/warehouses.php",
				data:	"func=deactivate&warehouseId="+selectedWarehouseId,
				success: function(msg)
				{
					notify( msg );
					$(".flexi").flexReload();
				}
			});
		}
	}
}

function activateWarehouseButton_Click(com,grid)
{
	if(selectedWarehouseId)
	{
		if(confirm("Really activate warehouse " + selectedWarehouseId + "?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/warehouses.php",
				data:	"func=activate&warehouseId="+selectedWarehouseId,
				success: function(msg)
				{
					notify( msg );
					$(".flexi").flexReload();
				}
			});
		}
	}
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


/*----------------------------------------------------------------------------*/
