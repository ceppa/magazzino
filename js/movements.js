/******************************************************************************/
/*																			*/
/*	MOVEMENTS.JS															*/
/*																			*/
/******************************************************************************/

function bind_menu_movements_list_click()
{
	$("#menu_movements_list").unbind('click');
	$("#menu_movements_list").bind(	"click",
	function()
	{
		listMovementButton_Click(false);
	});
}

function bind_menu_movements_search_click()
{
	$("#menu_movements_search").unbind('click');
	$("#menu_movements_search").bind("click",
	function()
	{
		listMovementButton_Click(true);
	});
}

function listMovementButton_Click(search)
{
	hideRelevantMenus();
	selectedMovementId = false;
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
			url: 'include/movements.php',
			params: [{name:'func',value:'list'}],
			dataType: 'json',
			colModel : 
			[
				{
					display: 'Date', 
					name : 'movements.insert_date', 
					width : 115, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'From', 
					name : 'places_from.name', 
					width : 120, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'To', 
					name : 'places_to.name', 
					width : 120, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Note', 
					name : 'movements.note', 
					width : 150, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'P/N',
					name : 'parts.pn_supplier', 
					width : 100, 
					sortable : false, 
					align: 'left'
				},
				{
					display: 'Description', 
					name : 'parts.description', 
					width : 100, 
					sortable : false, 
					align: 'left'
				},
				{
					display: 'S/N', 
					name : 'items.sn', 
					width : 100, 
					sortable : false, 
					align: 'left'
				},
				{
					display: 'Qty', 
					name : 'qty', 
					width : 30, 
					sortable : false, 
					align: 'center'
				},
				{
					display: 'User', 
					name : 'username', 
					width : 60, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Docs', 
					name : 'docs', 
					width : 60, 
					sortable : false, 
					align: 'left'
				}
			],
			searchitems : 
			[
				{
					display: 'From', 
					name : 'from',
					isdefault: true
				},
				{
					display: 'To', 
					name : 'to'
				},
				{
					display: 'Date', 
					name : 'date'
				},
				{
					display: 'Note', 
					name : 'note'
				},
				{
					display: 'P/N', 
					name : 'pn'
				},
				{
					display: 'S/N', 
					name : 'sn'
				},
				{
					display: 'Description', 
					name : 'description'
				},
				{
					display: 'User', 
					name : 'user'
				}
			],
			sortname: "movements.insert_date",
			sortorder: "desc",
			usepager: true,
			singleSelect:true,
			showTableToggleBtn: true,
			useRp: true,
			rp: global_rp,
			qtype: "date",
			query: (search?"ß":""),
			width: 'auto',
			height: 'auto',
			onRowSelected:movements_row_selected,
			onRowSelectedClick:movements_row_selected_click,
			onRowDeselected:movements_row_deselected,
			onNumRowsChange: onNumRowsChange
		}
	);
	if(search)
	{
		$('.sDiv').show();
		$('.qsbox').focus();
	}
	tb_list_movements();
	$("#toolbar_container").show();
}

function movements_row_selected(itemId,row,grid)
{
	$("#menu_movements_edit").show();
	$("#tbDetails").show();
	$("#tbEdit").show();
	selectedMovementId = itemId.substr(3);
}

function movements_row_selected_click(itemId,row,grid)
{
	editMovementButton_Click(null,null);
}


function movements_row_deselected(itemId,row,grid)
{
	$("#menu_movements_edit").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	selectedMovementId = false;
}

function addMovementButton_Click(itemId)
{
	$("#flexi_container").hide();
	$("#loading-div-background").show();
//	$("#loading_container").show();
	/*	load new movement form into right frame	*/
	$("#form_container").load(
		"include/movements.php",
		{
			func:'newMovementForm',
			itemId:(itemId?itemId:0)
		},
		function()
		{
			$('#movementForm').ajaxForm(
			{
				beforeSubmit:	
				function(formData, jqForm, options)
				{
					var out=checkMovementForm(true);
					if(out)
						flexiShowWait()
					return(out);
				},
				success:	function(data)
				{
 					addMovementClearForm();
					flexiShowForm();
//						$("#flexi_container").hide();
					notify(data);
				}
			});
			$("#loading_container").hide();
			$("#toolbar_container").show();
			$("#form_container").show();
			addDocumentRow(-1);
			addDocumentExRow();
			avoidFromToCollision($("#fromPlaceSelect"));
			tb_add_movement();
			$("#form_container").show();
			$("#loading-div-background").hide();
		}
	);
}

function editMovementButton_Click(com,grid)
{
	if(selectedMovementId)
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		$("#form_container").load
		(
			"include/movements.php",
			{
				func:'editMovementForm',
				movementId:(selectedMovementId?selectedMovementId:0)
			},
			function()
			{
				$('#movementForm').ajaxForm(
				{
					beforeSubmit:	
					function(formData, jqForm, options)
					{
						var out=checkMovementForm(false);
						if(out)
							flexiShowWait();
						return(out);
					},
					success:function(data)
					{
						flexiShowFlexi();
						notify(data);
						tb_list_movements();
						flexiReload();
					}
				});
				$("#loading_container").hide();
				$("#toolbar_container").show();
				$("#form_container").show();
				addDocumentRow(-1);
				tb_edit_movement();
			}
		);
	}
}

function bind_menu_movements_new_click()
{
	$("#menu_movements_new").bind(	"click",
	function()
	{
		addMovementButton_Click(null);
	});
}

function bind_menu_movements_edit_click()
{
	$("#menu_movements_edit").bind(	"click",
	function()
	{
		editMovementButton_Click(null);
	});
}


function detailsMovementButton_Click(com,grid)
{
			
	if(selectedMovementId)
	{
		$("#flexi_container").hide();
		$("#loading_container").show();
		/*	load new user form into right frame	*/
		$("#form_container").load(
			"include/movements.php",
			{
				func:'getMovementDetails',
				movementId:selectedMovementId
			},
			function()
			{
				tb_details_movement();
				$("#loading_container").hide();
				$("#form_container").show();
			}
		);
	}
}


function fillSnFromPn(li)
{
	var n=li.rowNumber;


	if($("#cellPartNumber_"+n).val()==$('#pnInput_'+n).val())
		return;

	$('#description_'+n).html("");
	selectedSn=li.selectedSn;

	if((typeof myVar != 'undefined')&&(Number(selectedSn)!=0))
		li.itemPresent=1;
// ma quanto sono buoni i taralli....
	if($('#pnInput_'+n).val().length)
	{
		if(li.itemPresent)
		{
			var desc_array=li.innerHTML.split("<br>");
			if(desc_array.length>1)
				var desc=desc_array[1];
			else
			{
				var desc="";
//				alert("i'm empty");
			}
			$('#description_'+n).html(desc);
//			alert("fillSnFromPn "+$('#pnInput_'+n).val()+" _ "+selectedSn);
			$('#snTd'+n).html("<img src='img/indicator.gif' alt='loading...'>");
			$('#id_parts_'+n).val(li.extra[1]);
			$.ajax(
				{
					type: 'POST',
					url: "include/movements.php",
					data: { ajax_pn: $('#pnInput_'+n).val(), 
							ajax_id_places: $('#fromPlaceSelect').val(), 
							ajax_id_places_to: $('#toPlaceSelect').val(), 
							ajax_index: n, 
							ajax_selectedSn: selectedSn 
						},
					success: function(data)
							{
								if($('#snTd'+n).html().indexOf("loading...")!=-1)
								{
									$('#snTd'+n).html(data);
									if($('#snTd'+n+' input[@type=checkbox]')[0])
										$('#snTd'+n+' input[@type=checkbox]')[0].focus();
									else if($('#ni_'+n+'_snInput'))
									{
										$('#ni_'+n+'_snInput').focus();
										addElement(null);
									}
									checkFotografiaShow();
								}
							},
					async:false
				});
			if(isItemSelected(n))
				$('#del_'+n).show();
		}
		else
		{
			var check=0;
			for(j=0;j<n;j++)
				if($('#pnInput_'+j).val().toUpperCase()==$('#pnInput_'+n).val().toUpperCase())
					check++;
			if(!check)
			{
				$('#snTd'+n).html("<img src='img/indicator.gif' alt='loading...'>");
				$.post("include/movements.php", 
						{ 
							func: "addPart",
							ajax_id_places_from: $('#fromPlaceSelect').val(), 
							ajax_id_places_to: $('#toPlaceSelect').val(), 
							ajax_pn_supplier: $('#pnInput_'+j).val(),
							ajax_index: n
						},
						function(data)
						{
							if($('#snTd'+n).html().indexOf("loading...")!=-1)
							{
								$('#snTd'+n).html(data);
								$('#np_'+n+'_manufacturerSelect')[0].focus();
							}
						}
					);
			}
			else
			{
				$('#pnInput_'+n).val("");
				fillSnFromPn(li);
				notify("part "+$('#pnInput_'+n).val()+" already involved");
				$('#pnInput_'+n).focus();
			}
		}
	}
	else
	{
		$('#del_'+n).hide();
		var maxNumber=newRowNumber()-1;
		if((numRows()>1)&&(n<maxNumber))
		{
			removeElement(n);
			$('#pnInput_'+maxNumber).focus();
		}
		else
		{
			$('#id_parts_'+n).val('');
			var snInput_id = 'snInput'+n;
			if($('#'+snInput_id).attr('name')!=snInput_id)
			{
				var snTd_id='#snTd'+n;
				var toFocus=($.trim($(snTd_id).html()).length>0);
				var excludeString="";
				$('#snTd'+n).html('<table class="itemsSubtable"><tr><td class="fieldTitle">S/N</td></tr><tr><td><input type="text" class="longTextInput" id="'+
								snInput_id+'" name="'+snInput_id+'"></td></tr></table>');
				$('#'+snInput_id).autocomplete("include/itemsAutocompleteBackend.php", 
					{
						minChars:0, 
						matchSubset:1, 
						matchContains:1, 
						cacheLength:10, 
						formatItem:function(row) {
							return "<b>" + row[0] + "</b>" 
							+ "<br><i>" + row[1] + "</i>";
						},
						onItemSelect:fillPnFromSn,
						extraParams:{from:$('#fromPlaceSelect').val().split("_")[0],sn: 1},
						rowNumber:n,
						selectOnly:1,
						mustMatch:1
					}); 

				if(toFocus)
					$('#'+snInput_id).focus();
			}
		}
	}
//	updateAutocompleter(n);
	updateAutocompleters();
}

function fillPnFromSn(li)
{
	var n=li.rowNumber;
	if(li.extra[1] && li.extra[1].length)
	{
		$("#loading-div-background").show();
		var sn=li.extra[1]; // in realtà è itemId
		$.ajax({
			type: 'POST',
			url: "include/movements.php",
			data: { ajax_sn: sn },
			success: function(data)
					{
						var json = $.evalJSON(data);
						var id=json.id_parts;
						var ac = $("#pnInput_"+n)[0].autocompleter;
						ac.setSelected(json.pn_supplier,false);
						li.innerHTML="<br>"+json.description;
						li.extra[1]=id;
						li.selectedSn=sn;
						li.itemPresent=true;
						fillSnFromPn(li);
						removeElements(n+1);
						addElement(null);
					},
			async:false
		});
/*		$.post("include/movements.php", { ajax_sn: sn },
					function(data)
					{
						var strData=String(data);
						var exploded=strData.split(",",2);
						var id=exploded[0];
						strData=strData.substr(1+id.length);
						var ac = $("#pnInput_"+n)[0].autocompleter;
						ac.setSelected(strData,false);
						li.extra[1]=id;
						li.selectedSn=sn;
						li.itemPresent=true;
						fillSnFromPn(li);
						removeElements(n+1);
						addElement(null);
					}
				);*/
		$("#loading-div-background").hide();
	}
}

function addElement(sn)
{
	var location="";
	var owner="";
	var numrows=numRows();
	var newrownumber=newRowNumber();
	var excludeString="";
	var pnInput_id='pnInput_'+newrownumber;
	var id_parts_id='id_parts_'+newrownumber;
	var row_id = 'row_'+newrownumber;
	var qtyInput_id = 'quantityInput'+newrownumber;
	var rowStyle;
//alert("addElement "+sn);

	if(numrows % 2)
		rowStyle="even";
	else
		rowStyle="odd";
	var row='<tr class="'+rowStyle+'" id="'+row_id+'">\n'+
				'<td>\n'+
					'<table>\n'+
						'<tr>'+
							'<td class="fieldTitle">\n'+
								'P/N'+
							'</td>\n'+
						'</tr>'+
						'<tr>'+
							'<td>\n'+
								'<img id="del_'+newrownumber+'" '+
									'src="img/trash_can_delete3.png" '+
									'style="cursor: pointer;width:16px;vertical-align:middle;" '+
									'onclick="removeElement('+newrownumber+')">'+
								'&nbsp;<input type="text" style="vertical-align:middle" '+
								'class="longTextInput" id="'+
									pnInput_id+'" name="'+pnInput_id+'"'+
									' onchange="removeElements('+(newrownumber+1)+')">\n'+
								'<p class="description" id="description_'+newrownumber+'"></p>'+
								'<input type="hidden" id="'+id_parts_id+
									'" name="'+id_parts_id+'">\n'+
							'</td>\n'+
						'</tr>'+
					'</table>'+
				'</td>'+
				'<td id="snTd'+newrownumber+'">\n'+
				'</td>\n'+
			'</tr>\n';

	$('#table_id').append(row);
	$('#'+pnInput_id).autocomplete("include/itemsAutocompleteBackend.php", 
			{
				minChars:0, 
				matchSubset:1, 
				matchContains:1, 
				cacheLength:10, 
				formatItem:function(row) 
				{
					var pns,pnm;
					if(Number(row[5]))
					{
						pns=row[4];
						pnm=row[0];
					}
					else
					{
						pns=row[0];
						pnm=row[4];
					}

					if(Number(row[3]))
						return "<b>"+pns+"</b> - <b>"+pnm+"</b>"
							+ "<br>"+row[1];
					else
						return pns+"<b> - </b>"+pnm+"<br><i>"+row[1]+"</i>";

				},
				onItemSelect:itemSelected,
				extraParams:
				{
					mustMatch:mustMatch(),
					from:$('#fromPlaceSelect').val().split("_")[0]
				},
				rowNumber:newrownumber,
				selectedSn:sn,
				selectOnly:1,
				mustMatch:mustMatch()
			});
	$('#'+pnInput_id).focus();
	return(newrownumber);
}



function itemSelected(li)
{
	n=li.rowNumber;
//	alert($('#description_'+n).html().length);
	if((li.innerHTML.length==0)&&($('#description_'+n).html())&&($('#description_'+n).html().length>0))
		li.innerHTML='<br>'+$('#description_'+n).html();
	fillSnFromPn(li);
}

function mustMatch()
{
	var places_type=$("#fromPlaceSelect").val().split("_")[1];
	return ((places_type!=3)&&(places_type!=4)?1:0);
}

function removeElements(index)
{
	$('#table_id tr[id^="row_"]').each( function()
		{
			var id=$(this)[0].id.split("_")[1];
			if(Number(id)>=Number(index))
				$(this).remove();
		});
	if(numRows()==0)
	{
		addElement(null);
	}
}

function removeElement(numrow)
{
	$('#table_id tr[id^="row_'+numrow+'"]').remove();
	var n=newRowNumber()-1;
	if((numRows()==0)||($("#pnInput_"+n).val().length>0))
		if(checkLastRowChecked())
		{
			addElement(null);
		}
}


function numRows()
{
	var rows = $('#table_id tr[id^="row_"]');
	return rows.size();
}

function newRowNumber()
{
	var num=0;
	var rows = $('#table_id tr[id^="row_"]');
	if(rows.size()>0)
		num=1+Number(rows[rows.size()-1].id.split("row_")[1]);
	return num;
}


function canAddElement_newPart(index)
{
	var numrows=numRows();
	var result=($("#np_"+index+"_subsystemSelect").val().length);
	if(!result)
		$("#np_"+index+"_subsystemCheck").html("must be selected");
	else
		$("#np_"+index+"_subsystemCheck").html("");

	if(result)
	{
		if(!isLastRowEmpty())
		{
			addElement(null);
		}
	}
	else
		if(isLastRowEmpty())
			removeElements(index);
}

function canAddElement(sender,children)
{
	children = typeof children !== 'undefined' ? children : '';
	var checked=sender.checked;
	var splitted=sender.name.split("_");
	var senderType=splitted[0];
	var index=splitted[1];
	var sn;
	if(senderType=="sn")
	{
		sn=splitted[2].split("§")[0];

		var toPlaceSplitted=$("#toPlaceSelect").val().split("_");
		if((toPlaceSplitted.length>1)&&(Number(toPlaceSplitted[1])>2))
		{
			var c=$("input[name='repair_"+sender.name.substr(3)+"']");
			if(sender.checked)
				c.show();
			else
			{
				c.attr("checked",false);
				c.hide();
			}
			checkRepairShow();
		}
	}
	var numrows=numRows();
	var result=false;
	var checkBoxes=$('#snTd'+index+' checkbox');
	var sonId=Number(index)+1;


	var childrenArray=new Array();
	var sonadded=false;
//	alert(children);
	if(children.length)
		childrenArray=children.split(",");

	$('#snTd'+index+' INPUT[type="checkbox"]').each( function()
		{
			if((!result)&&($(this).attr('checked')))
			{
				for(var i=0;i<childrenArray.length;i++)
				{
					var childrenArraySplitted=childrenArray[i].split("§");
					var itemsId=childrenArraySplitted[0];
					var partsId=childrenArraySplitted[1];
					var pn=childrenArraySplitted[2];
					var description=childrenArraySplitted[3];

					// itemsId partsId pn
					// add row with itemId=childrenArray[i]

					var id_row=-1;
					$('INPUT[id^="id_parts_"]').each( function()
					{
						if($(this).val()==childrenArraySplitted[1])
							id_row=$(this).attr('id').split("_")[2];
					});

					if(id_row!=-1)
					{
						// parte già presente
						// check corresponding checkbox 

						$('#snTd'+id_row+' INPUT[type="checkbox"]').each( function()
						{
							if($(this)[0].getAttribute('itemId', 0)==itemsId)
							{
								$(this).attr("checked",true);
							}
						});
					}
					else
					{
						removeElements(sonId);
						addElement(itemsId);
						fillPnAndSn(sonId,
							partsId,pn,description);
						sonId++;
						sonadded=true;
					}
				}
				result=true;
			}
		});


	var maxRowNumber=newRowNumber()-1;
	if(result)
	{
		$('#del_'+index).show();
		if((index==maxRowNumber)||(sonadded==true))
		{
			addElement(null);
		}
	}
	else
	{
		$('#del_'+index).hide();
		if(index<maxRowNumber)
			removeElements(Number(index)+1);
	}
}

function checkLastRowChecked()
{
	var index=newRowNumber()-1;
	return isItemSelected(index);
}

function qtyChanged(obj,max)
{
	if(Number(obj.value) > max)
		obj.value=max;
	else if(Number(obj.value) < 1) 
		obj.value=1;
}

function fillPnAndSn(n,id_parts,pn,description)
{
	$('#id_parts_'+n).val(id_parts);
	if(typeof description != 'undefined')
		$('#description_'+n).html(description);
	var ac = $("#pnInput_"+n)[0].autocompleter;
	ac.setSelected(pn,true,true);
}

function checkMovementForm(add)
{
	var obj=$("#documents tr");
	var simulator_id=$("#simulator_id").val();
	var snCheckSelector;
	var snInputSelector;
	var partsIdSelector;
	var badColor = '#F00';
	var goodColor = '#0F0';
//document file and description are mandatory
	var nook=0;
	for(i=1;i<obj.size()-1;i++)
	{
		if(obj.get(i).id.substr(0,8)=="doc_row_")
		{
			var m=obj.get(i).id.substr(8);
			if((($.trim($("#doc_"+m).val()).length!=0) || ($.trim($("#doc_desc_"+m).val()).length!=0))
					&& (($.trim($("#doc_"+m).val()).length==0) || ($.trim($("#doc_desc_"+m).val()).length==0)))
			{
				$('#doc_check_'+m).html("both fields are mandatory");
				nook++;
			}
			else
				$('#doc_check_'+m).html("");
		}
	}
	var numrows=numRows();
	if(add)
	{
		if($('#toPlaceSelect').val()==-1)
		{
			notify("Select destination");
			return false;
		}
		if(numrows<2)
		{
			notify("No item to move");
			return false;
		}
		for(i=0;i<numrows;i++)
		{
			if($("#np_"+i+"_subsystemSelect")[0]
				&&($("#np_"+i+"_subsystemSelect")[0].selectedIndex==0))
			{
				notify("subsystem for a new part is mandatory");
				return false;
			}

			snCheckSelector = "#snCheck_"+i;
			snInputSelector = "#ni_"+i+"_snInput";
			partsIdSelector = "#id_parts_"+i;

			if($.trim($(snInputSelector).val()) != "")
			{
				
				var ajaxData =
					{
						whereString:"items.id_parts=\"" + $(partsIdSelector).val() +
							"\" AND " +
							"items.sn=\"" + $(snInputSelector).val() +
							"\" AND " +
							"parts.id_simulators=\""+simulator_id+"\""
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
							nook++;
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
		}
	}
	if(nook>0)
		return false;
	return true;
}

function addDocumentRow(m)
{
	if((m<0)||($.trim($("#doc_"+m).val()).length && $.trim($("#doc_desc_"+m).val()).length))
	{
		var n=String(1+Number(m));
		var row='<tr id="doc_row_'+n+'">';
		row+='<td><input name="rem_'+n+'" type="button" id="rem_'+n+'" ';
		row+='class="button" value="remove" onclick="delDocumentRow('+n+')"></td>';
		row+='<td><input name="doc_'+n+'" type="file" size="40" ';
		row+='id="doc_'+n+'" value="browse"></td>';
		row+='<td><input name="doc_desc_'+n+'" type="text" ';
		row+='id="doc_desc_'+n+'" ></td>';
		row+='<td id="doc_check_'+n+'" class="fieldCheck dark_red"></td>';
		row+='</tr>';
		row+='<tr id="add_row"><td colspan="4"><input name="add" ';
		row+='type="button" class="button" id="add_button" ';
		row+='value="new doc"></td></tr>';
		if($("#add_row"))
			$("#add_row").remove();
		$("#documents").append(row);
		$("#add_button").bind("click",function()
		{
			addDocumentRow(n);
		});
	}
	if(m>=0)
	{
		if(($.trim($("#doc_"+m).val()).length==0)||($.trim($("#doc_desc_"+m).val()).length==0))
			$('#doc_check_'+m).html("both fields are mandatory");
		else
			$('#doc_check_'+m).html("");
	}
}

function addDocumentExRow(li)
{
	var rowNumber;
	var newId="";
	var oldId="";
	if(li)
	{
		rowNumber=li.rowNumber;
		if(li.extra.length==0)
		{
			if($('#doc_ex_'+rowNumber).val().length==0)
			{
				$('#doc_ex_id_'+rowNumber).val("");
				$('#doc_ex_desc_'+rowNumber).val("");
				fixDocExAutocomplete(rowNumber);
			}
			return;
		}
		$('#doc_ex_id_'+rowNumber).val(li.extra[1]);
		id=$('#doc_ex_desc_'+rowNumber).val(li.extra[0]);
		if(li.text)
		{
			var ac = $("#doc_ex_"+rowNumber)[0].autocompleter;
			ac.setSelected(li.text,false);
		}
		fixDocExAutocomplete(rowNumber);
	}
	else
		rowNumber=0;
	var n;
	var excludeString="";
	var obj=$('#documents_ex tr');
	var index=obj.size();
	if(index==1)
		n=0;
	else
	{
		n=1+Number(obj.get(index-1).id.substr(11));
		for(var i=0;i<index;i++)
		{
			j=obj.get(i).id.substr(11);
			if(($('#doc_ex_id_'+j).val()!=undefined)
					&&($('#doc_ex_id_'+j).val()!=""))
				excludeString+=($('#doc_ex_id_'+j).val()+",");
		}
	}
	if(n-rowNumber>1)
	{
		if(li)
			$('#doc_ex_'+(rowNumber+1)).focus();
		return;
	}

	var row='<tr id="doc_ex_row_'+n+'">';
	row+='<td><input name="doc_ex_'+n+'" type="text" ';
	row+='id="doc_ex_'+n+'" >';
	row+='<input name="doc_ex_id_'+n+'" type="hidden" ';
	row+='id="doc_ex_id_'+n+'" value=""></td>';
	row+='<td><input type="text" disabled="disabled" id="doc_ex_desc_'+n+'"></td></tr>';
	$("#documents_ex").append(row);
	$('#doc_ex_'+n).autocomplete("include/documentsAutocompleteBackend.php", 
			{
				minChars:0, 
				matchSubset:1, 
				matchContains:1, 
				cacheLength:10, 
				formatItem:function(row) 
				{
					return "<b>" + row[0] + "</b>" 
					+ "<br><i>" + row[1] + "</i>";
				},
				extraParams:
				{
						exclude: excludeString
				},
				onItemSelect:addDocumentExRow,
				rowNumber:n,
				selectOnly:1,
				mustMatch:1
			});
	if(li)
		$('#doc_ex_'+n).focus();
}


function delDocumentRow(m)
{
	var index=$('#documents tr').size();
	if(index==3)
	{
		$("#doc_"+m).val("");
		$("#doc_desc_"+m).val("");
	}
	else
	{
		$("#doc_row_"+m).remove();
		var n=$('#documents tr').get(index-3).id.substr(8);
		$("#add_button").unbind("click");
		$("#add_button").attr('onclick','')
		$("#add_button").bind("click",function()
			{
				addDocumentRow(n);
			});
	}
}

function fixDocExAutocomplete(rowNumber)
{
	var obj=$('#documents_ex tr');
	var index=obj.size();
	var i=1;
	var n;
	var row=obj.get(i);
	var excludeString="";

	while((i<index)&&(row.id!=("doc_ex_row_"+rowNumber))) //search for affected row i
	{
		n=obj.get(i).id.substr(11);
		excludeString+=($('#doc_ex_id_'+n).val()+",");
		i++;
		row=obj.get(i);
	}
	if(i<index-1)	//affected row is not the last
	{
		n=obj.get(i).id.substr(11);//id of affected row
		var val=$('#doc_ex_id_'+n).val();
		if(val.length==0)//new value is null->remove line
			$("#doc_ex_row_"+n).remove();
		else
			excludeString+=(val+",");
		i++;
		for(i;i<index;i++)
		{
			n=obj.get(i).id.substr(11);
			if(val.length 
				&& ($('#doc_ex_id_'+n).val()==val))
				$("#doc_ex_row_"+n).remove();
			else
			{
				var ac = $('#doc_ex_'+n)[0].autocompleter;
				ac.setExtraParams({exclude: excludeString});
				if($('#doc_ex_id_'+n).val().length)
					excludeString+=($('#doc_ex_id_'+n).val()+",");
			}
		}
	}
}

function addMovementClearForm()
{
	var obj=$("#documents tr");
	for(i=1;i<obj.size();i++)
		$("#"+obj.get(i).id).remove();

	obj=$("#documents_ex tr");
	for(i=1;i<obj.size();i++)
		$("#"+obj.get(i).id).remove();


	$("#fromPlaceSelect")[0].selectedIndex=0;
	$("#toPlaceSelect")[0].selectedIndex=0;
	$("#movementDescriptionInput").val("");
	addDocumentRow(-1);
	addDocumentExRow();
	removeElements(0);
	avoidFromToCollision($("#fromPlaceSelect"));
}

function avoidFromToCollision(sender)
{
	var idSender,idSent;
	if(sender.id!=undefined)
		idSender=sender.id;
	else
		idSender=sender.attr("id");
	if(idSender=="fromPlaceSelect")
		idSent="toPlaceSelect";
	else
		idSent="fromPlaceSelect";
	
	if(($("#"+idSender)[0].selectedIndex>0)&&
		($("#"+idSent)[0].selectedIndex>0)&&
		($("#"+idSender)[0].selectedIndex==$("#"+idSent)[0].selectedIndex))
	{
		i=0;
		do
		{
			$("#"+idSent)[0].selectedIndex=i;
			i++;
		}
		while(($("#"+idSender)[0].selectedIndex==$("#"+idSent)[0].selectedIndex)
					&&(i<$("#"+idSender)[0].length));
		if(idSent=="fromPlaceSelect")
			$("#fromPlaceSelect").change();
		else
			$("#toPlaceSelect").focus();
	}

/*	if(($("#"+idSender)[0].selectedIndex>0)&&($("#"+idSent)[0].selectedIndex>0))
		$('#table_id').show();
	else
		$('#table_id').hide();*/
}

function updateFotografiaCombo()
{
	var fotoCombo;
	var pn;
	var sn;
	var sns;
	var id_cell;
	var id_places_exploded=$("#toPlaceSelect").val().split("_");

	id_places_to=id_places_exploded[0];
	if(id_places_exploded.length>1)
		places_type=Number(id_places_exploded[1]);
	else
		places_type=0;

	if(places_type>1)
	{
		$("td[id^='fotografia_cell_']").html("");
		$("span[name='fotografia_header']").hide();


		$('#table_id tr[id^="row_"]').each( function()
			{
				var i=$(this)[0].id.split("_")[1];
	
				pn=$("#pnInput_"+String(i)).val();
				sn_selector="sn_"+String(i)+"_";
				if(pn.length)
				{
					sns=$("input[name^='"+sn_selector+"']");
					for(j=0;j<sns.length;j++)
					{
						var c=$("input[name='repair_"+sns[j].name.substr(3)+"']");
						if((sns[j].checked)&&(sns[j].name.substr(5,1)!="§"))
						{
							$("span[name='repair_header']").show();
							c.show();
						}
						else
						{
							$("span[name='repair_header']").hide();
							c.attr('checked', false);
							c.hide();
						}
					}
				}
			});
	}
	else
	{
		$("input[name^='repair_']").attr('checked', false).hide();

		$("#loading-div-background").show();
		$("span[name='repair_header']").hide();

		$('#table_id tr[id^="row_"]').each( function()
			{
				var i=$(this)[0].id.split("_")[1];
	
				pn=$("#pnInput_"+String(i)).val();
				sn_selector="sn_"+String(i)+"_";
				if(pn.length)
				{
					sns=$("input[name^='"+sn_selector+"']");
					for(j=0;j<sns.length;j++)
					{
						sn_string=sns[j].name.substr(sn_selector.length).split("§");
						sn=sn_string[0];
						if(sn.length)
						{
							id_owners=sn_string[1];
							loc=sn_string[2];
							id_cell="#fotografia_cell_"+String(i)+"_"+String(j);
	
							$.ajax({
								type: 'POST',
								url: "include/movements.php",
								data: { 
									func: "updateFotografiaCombo",
									id_places_to: id_places_to,
									ajax_pn: pn, 
									sn: sn,
									index: i,
									id_owners: id_owners,
									location: loc
									},
								async:false,
								success: function ( data ) 
									{
										$(id_cell).html(data);
									}
							});
						}
					}
				}
	
	
	
	
	
			});
		$("#loading-div-background").hide();
		checkFotografiaShow();
	}
}

function checkFotografiaShow()
{
	var rows=$("tr[id^='row_']");
	for(i=0;i<rows.length;i++)
	{
		var n=rows[i].id.split("_")[1];
		$("#row_"+n+" span[name='fotografia_header']").hide();

		var cells=$("td[id^='fotografia_cell_"+n+"_']");
		for(j=0;j<cells.length;j++)
			if(cells[j].innerHTML.trim().length>0)
			{
				$("#row_"+n+" span[name='fotografia_header']").show();
				break;
			}
	}
}

function checkRepairShow()
{
	var toPlaceSplitted=$("#toPlaceSelect").val().split("_");
	if((toPlaceSplitted.length==1)||(Number(toPlaceSplitted[1])<3))
	{
		$("input[name^='repair_']").attr("checked",false);
		$("input[name^='repair_']").hide();
		$("span[name='repair_header']").hide();
		return;
	}

	var rows=$("tr[id^='row_']");
	for(i=0;i<rows.length;i++)
	{
		var n=rows[i].id.split("_")[1];
		$("#row_"+n+" span[name='repair_header']").hide();

		var cells=$("input[name^='repair_"+n+"_']");
		for(j=0;j<cells.length;j++)
			if($(cells[j]).is(':visible'))
			{
				$("#row_"+n+" span[name='repair_header']").show();
				break;
			}
	}
}

function doChangeFotografia(sender)
{
	var senderIndex=sender.name.split("_")[1];
	var combos=$('select[name^="fotografia_'+senderIndex+'"]');
	for(i=0;i<combos.length;i++)
		if((combos[i]!=sender)&&(combos[i].selectedIndex==sender.selectedIndex))
			combos[i].selectedIndex=0;
}


function updateAutocompleters()
{
	var excludeString=new Array();
	$('input[id^="id_parts_"]').each(function()
		{
			if($(this).val().length)
				excludeString.push($(this).val());
		});
	$('input[id^="pnInput_"]').each(function()
		{
			var i=$(this)[0].id.split("pnInput_")[1];
			var id_parts=$('#id_parts_'+i).val();
			var thisExclude=excludeString.slice();
			var index;
			if($(this)[0].autocompleter!=undefined)
			{
				if($(this).val().length)
					if((index=thisExclude.indexOf(id_parts))!=-1)
						thisExclude.splice(index, 1);
				var excluded=thisExclude.toString();
				$(this)[0].autocompleter.flushCache();
				$(this)[0].autocompleter.setExtraParams({exclude:excluded});
			}
		});

	$('input[id^="snInput"]').each(function()
		{
			if($(this)[0].autocompleter!=undefined)
			{
				$(this)[0].autocompleter.flushCache();
				$(this)[0].autocompleter.setExtraParams({exclude:excludeString});			
			}
		});
}

function updateAutocompleter(rowNumber)
{
	var excludeString=new Array();
	$('input[id^="id_parts_"]').each(function()
		{
			if($(this).val().length)
				excludeString.push($(this).val());
		});

	var pnInput=$('#pnInput_'+rowNumber);
	if(pnInput[0].autocompleter!=undefined)
	{
		pnInput[0].autocompleter.setExtraParams({exclude:excludeString});
	}

	var snInput=$('#snInput'+rowNumber);
	if((typeof snInput[0] != 'undefined')&&(snInput[0].autocompleter!=undefined))
	{
		snInput[0].autocompleter.flushCache();
		snInput[0].autocompleter.setExtraParams({exclude:excludeString});			
	}
}


function isLastRowEmpty()
{
	var out=false;
	var n=newRowNumber();
	if(n>0)
		out=($('#pnInput_'+(n-1)).val().length==0);
	return out;
}

function isItemSelected(n)
{
	var out=false;
	$('#snTd'+n+' input[@type=checkbox]').each( function()
		{
			if($(this).attr('checked'))
				out=true;
		});
	return out;
}

function getNewItemTable(sender)
{
	var splitted=sender.id.split("_");
	var index=splitted[1];
	var n=Number(splitted[2]);


	var empty=0;
	$('input[id^="ni_'+index+'_snInput_"]').each( function()
		{
			var m=$(this).attr('id').split("_")[3];
			var checkbox=$('#newItem_'+index+'_'+m);
			if(($(this).val().trim().length==0)&&(checkbox.attr('checked')))
				empty++;
		});
	if((empty>1)
		&&($('#ni_'+index+'_snInput_'+n).val().trim().length==0))
	{
		sender.checked=false;
		return;
	}

	n++;
	var nextitem=$('#newItem_'+index+'_'+n);
	if((typeof nextitem.attr('name') == 'undefined')
		&&(sender.checked))
	{
	
		$.ajax(
		{
			type: 'post',
			url: "include/movements.php", 
			async:false,
			data: 
			{
				index: index, 
				n: n,
				func: 'newItemTable'
			},
			success: function(data)
			{
				$('#snTd'+index).append(data);
			}
		});
	}
}
