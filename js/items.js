/*----------------------------------------------------------------------------*/
/*				   ITEMS   ITEMS   ITEMS   ITEMS							*/
/*						ITEMS	ITEMS	ITEMS						*/
/*					ITEMS	ITEMS	ITEMS	ITEMS					*/
/*----------------------------------------------------------------------------*/

function bind_menu_items_list_click()
{
	$("#menu_inventory_list").unbind('click');
	$("#menu_inventory_list").bind("click",
	function()
	{
		listItemsButton_Click(false);
	});
}

function bind_menu_items_search_click()
{
	$("#menu_inventory_search").unbind('click');
	$("#menu_inventory_search").bind("click",
	function()
	{
		listItemsButton_Click(true);
	});
}


function listItemsButton_Click(search)
{
	hideRelevantMenus();
	selectedItemId = false;
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
			url: 'include/items.php',
			params: [{name:'func',value:'list'}],
			dataType: 'json',
			colModel : 
			[
				{
					display: 'Description', 
					name : 'parts_name', 
					width : 170, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Supplier ',
					name : 'supplier_name', 
					width : 100, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Supplier P/N ',
					name : 'parts_pn_supplier', 
					width : 80, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Manufacturer ',
					name : 'manufacturer_name', 
					width : 100, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Manufacturer P/N ',
					name : 'parts_pn_manufacturer', 
					width : 80, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Qty', 
					name : 'items_quantity', 
					width : 30, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Serial Number', 
					name : 'items_sn', 
					width : 80, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Warehouse', 
					name : 'places_name', 
					width : 80, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Location / note', 
					name : 'items_location', 
					width : 80, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Fotografia', 
					name : 'fotografia', 
					width : 100, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Repair', 
					name : 'to_repair', 
					width : 30, 
					sortable : true, 
					align: 'center'
				},
				{
					display: 'Repl', 
					name : 'replaced_itemId', 
					width : 30, 
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
					display: 'description', 
					name : 'description'
				},
				{
					display: 'S/N', 
					name : 'sn'
				},
				{
					display: 'location', 
					name : 'location'
				},
				{
					display: 'Warehouse', 
					name : 'place'
				},
				{
					display: 'Manufacturer', 
					name : 'manufacturer'
				},
				{
					display: 'Supplier', 
					name : 'supplier'
				},
				{
					display: 'Repair', 
					name : 'to_repair'
				}
			],
			sortname: "items_location",
			sortorder: "asc",
			usepager: true,
			singleSelect:true,
			useRp: true,
			rp: global_rp,
			qtype: "place",
			query: (search?"ß":""),
			width: 'auto',
			height: 'auto',
			onRowSelected:items_row_selected,
			onRowSelectedClick:items_row_selected_click,
			onRowDeselected:items_row_deselected,
			onNumRowsChange: onNumRowsChange
		}
	);
	$(".flexigrid").css({width:"100%"});
	tb_items_list();
	if(search)
	{
		$('.sDiv').show();
		$('.qsbox').focus();
	}
/*
$('select[name=qtype]'*/

	$("#toolbar_container").show();
}

function bind_menu_items_move_click()
{
	$('#menu_inventory_move').unbind('click');
	$('#menu_inventory_move').bind('click',
	function () 
	{
		moveItemButton_Click(null,null)
	});
}

function bind_menu_items_edit_click()
{
	$('#menu_inventory_edit').unbind('click');
	$('#menu_inventory_edit').bind('click',
	function () 
	{
		 editItemButton_Click(null,null)
	});
}

function detailsItemButton_Click(com,grid)
{
	if(selectedItemId)
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		$("#form_container").load(
			"include/items.php",
			{
					func:'detailItemForm',
					itemId:selectedItemId
			},
			function() 
			{
				$('#itemForm').ajaxForm(
				{
					beforeSubmit:	
					function(formData, jqForm, options)
					{
						return(checkItemForm("itemForm"));
					},
					success:	function(data)
					{
						notify(data);
						$("#form_container").hide();
						$("#flexi_container").hide();
//						$("#menu_inventory_list").click();
					}
				});
				tb_details_item();
				$("#loading_container").hide();
				$("#form_container").show();
			}
		);
	}

}

function editItemButton_Click(com,grid)
{
	if(selectedItemId)
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		$("#form_container").load
		(
			"include/items.php",
			{
				func:'editItemForm',
				itemId:selectedItemId
			},
			function() 
			{
				$('#itemForm').ajaxForm(
				{
					beforeSubmit:	
					function(formData, jqForm, options)
					{
						var out=checkItemForm("itemForm");
						if(out)
							flexiShowWait();
						return(out);
					},
					success:	function(data)
					{
						flexiShowFlexi();
						notify(data);
						tb_items_list();
						flexiReload();
					}
				});
				tb_edit_item();
				$("#snInput").change(function()
					{
						if($.trim($("#snInput").val()).length>0)
							$("#fotografiaRow").show();
						else
							$("#fotografiaRow").hide();
					});
				if(($('#to_repair').length==0)||(!$('#to_repair')[0].checked))
				{
					$('#replacedRow').hide();
/*					$('#replaced_itemId').val(0);
					$('#replaced_itemId_text').val("");*/
				}
				$('#to_repair').click(function()
					{
						if($(this)[0].checked)
							$('#replacedRow').show();
						else
						{
							$('#replacedRow').hide();
/*							$('#replaced_itemId').val(0);
							$('#replaced_itemId_text').val("");*/
						}
					});
				var replaced_itemId_text=($('#replaced_itemId_text').length>0?$('#replaced_itemId_text').val():"");
				$('#replaced_itemId_text').autocomplete("include/itemsAutocompleteBackend.php", 
					{
						minChars:0, 
						matchSubset:1, 
						matchContains:1, 
						cacheLength:10, 
						formatItem:function(row) {
							return "<b>" + row[0] + "</b>" 
							+ "<br><i>" + row[1] + "</i>";
						},
						onItemSelect:function(li)
						{
							if((li.extra[1])&&(li.extra[1].length))
							{
								var itemId=li.extra[1];
								$("#replaced_itemId").val(itemId);
							}
							else
							{
								if($("#replaced_itemId_text").val().length==0)
									$("#replaced_itemId").val(0);
							}
						},
						extraParams:{sn: 1},
						selectOnly:1,
						mustMatch:1
					}); 
				if(replaced_itemId_text.length)
					$('#replaced_itemId_text')[0].autocompleter.setSelected(replaced_itemId_text,false);


				$("#snInput").change();
				$("#loading_container").hide();
				$("#form_container").show();
			}
		);
	}
}


function moveItemButton_Click(com,grid)
{
	if(selectedItemId)
	{
		addMovementButton_Click(selectedItemId);
	}
}

function items_row_selected(itemId,row,grid)
{
	$("#menu_inventory_edit").show();
	$("#menu_inventory_move").show();
	$("#tbDetails").show();
	$("#tbEdit").show();
	$("#tbMove").show();
	if(session_level==1)
		$("#tbDelete").show();

	selectedItemId = itemId.substr(3);
}

function items_row_selected_click(itemId,row,grid)
{
	editItemButton_Click(null,null);
}


/* callback when row is deselected in items table */
function items_row_deselected(itemId,row,grid)
{
	$("#menu_inventory_edit").hide();
	$("#menu_inventory_move").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbMove").hide();

	selectedItemId = false;
}

function pnInput_onItemSelect(li) 
{
	if(li.extra[1] != undefined) 
	{
		$("#partId").val(li.extra[1]);
		var partId = li.extra[1];
		selectedPartId = partId;
		$('#pnCheck').html('OK!');
		$('#pnCheck').removeClass('dark_red').addClass('dark_green');
		$('#part_details_container').load(
			"include/items.php",
			{
				func:'getPartDetails',partId:partId
			}
		);
	} else {
		$('#part_details_container').html('');
		$('#pnCheck').html('Insert a part number.');
		$('#pnCheck').removeClass('dark_green').addClass('dark_red');
		$("#partId").val(false);
		selectedPartId = false;
	}
	return;
}


function checkItemForm(formId)
{
	/* prepare useful variables */
	var itemId = "#"+formId+" #itemId";
	var partId = "#"+formId+" #partId";
	var snInputSelector = "#"+formId+" #snInput";
	var snCheckSelector = "#"+formId+" #snCheck";
	var id_simulators = "#"+formId+" #id_simulators";
	var badColor = '#F00';
	var goodColor = '#0F0';
	var formIsGood = true;
	var snExists = false;

	if((jQuery.trim($(snInputSelector).attr("value")) != "")
		&& (($('#func').val()=='edit')||($('#func').val()=='add')))
	{
		var ajaxData =
		{
			whereString:"items.id!=\"" + $(itemId).attr("value") +
			"\" AND " +
			"items.sn=\"" + $(snInputSelector).attr("value") +
			"\" AND " +
			"items.id_parts=\"" + $(partId).attr("value") +
			"\" AND " +
			"parts.id_simulators=\"" + $(id_simulators).attr("value") + "\""
		};
		$.ajax({
			type:'post',
			async:false,
			url:"include/items.php?func=list",
			data:ajaxData,
			dataType:'json',
			success:function(JSONdata,textStatus)
			{
				if(JSONdata.rows.length > 0)
				{
					snExists = true;
					$(snCheckSelector)
						.html("<img src='img/stock_no_s.png' alt='error'> - exists");
					$(snCheckSelector)
						.css({color:badColor});
					formIsGood = false;
				}
				else
				{
					$(snCheckSelector)
						.html("&nbsp;");
					$(snCheckSelector)
						.css({color:goodColor});
				}
			}
		});
	}
	if($('#func').val()=='add')
	{
		var pnInputSelector = "#"+formId+" #pnInput";
		var warehouseSelector = "#"+formId+" #warehouseSelect";

		if((jQuery.trim($(pnInputSelector).val())=="")
				||($(warehouseSelector).val()==""))
			formIsGood=false;
	}
	return formIsGood;
}

function deleteItemButton_Click()
{
	if((selectedItemId)&&confirm("Really delete item and its history?\nIt cannot be undone!"))
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		$.ajax({
				type: 'POST',
				url: 'include/items.php',
				data: { func: "delete", items_id:selectedItemId},
				async: false,
				success: function(html)
				{
					if(html.length!=0)
					{
						$("#flexi_container").show();
						$("#loading_container").show();
						notify(html);
					}
					else
					{
						flexiShowFlexi();
						tb_items_list();
						flexiReload();
						notify("0item deleted");
					}
				}
			});

	}

}
/*----------------------------------------------------------------------------*/
