
function bind_menu_admin_compatibles_click()
{
	$("#menu_compatibles").unbind('click');
	$("#menu_compatibles").bind("click",
	function()
	{
		tb_admin();
		$("#form_container").hide();
		$("#flexi_container").hide();
		$("#loading_container").show();

		$("#form_container").load
		(
			"include/admin.php",
			{
				func:'compatiblesForm'
			},
			function()
			{
				$("#loading_container").hide();
				$("#form_container").show();
				updateCompatibles();
			}
		);
	});
}

function bind_manu_admin_merge_parts_click()
{
	$("#menu_merge_parts").unbind('click');
	$("#menu_merge_parts").bind("click",
	function()
	{
		tb_admin();
		$("#form_container").hide();
		$("#flexi_container").hide();
		$("#loading_container").show();

		$("#form_container").load
		(
			"include/admin.php",
			{
				func:'mergeForm'
			},
			function()
			{
				$("#loading_container").hide();
				$("#form_container").show();
				mergeAutocomplete();
			}
		);
	});
}

function mergeAutocomplete()
{
	$('#alivePart').autocomplete("include/itemsAutocompleteBackend.php", 
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
		onItemSelect:alivePartSelected,
		selectOnly:1,
		mustMatch:1
	}); 

	$('#deadPart').autocomplete("include/itemsAutocompleteBackend.php", 
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
		onItemSelect:deadPartSelected,
		selectOnly:1,
		mustMatch:1
	}); 
}


function alivePartSelected(li)
{
	if(li.extra[1] && li.extra[1].length)
	{
		var excluded=li.extra[1];
		$("#id_part_alive").val(excluded);
		var ac=$("#deadPart")[0].autocompleter;
		ac.flushCache();
		ac.setExtraParams({exclude:excluded});
	}
	else
		$('#id_part_alive').val("");
}

function deadPartSelected(li)
{
	if(li.extra[1] && li.extra[1].length)
	{
		var excluded=li.extra[1];
		$("#id_part_dead").val(excluded);
		var ac=$("#alivePart")[0].autocompleter;
		ac.flushCache();
		ac.setExtraParams({exclude:excluded});
	}
	else
		$('#id_part_dead').val("");
}

function doMerge()
{
	if(($("#alivePart").val().length)&&($("#deadPart").val().length))
	{
		var id_part_alive=$("#id_part_alive").val();
		var id_part_dead=$("#id_part_dead").val();
		$.ajax(
		{
			type: 'POST',
			url: "include/admin.php",
			data: { 
					func: "doMerge",
					id_part_alive: id_part_alive, 
					id_part_dead: id_part_dead
				},
			success: function(data)
			{
				if($.trim(data)=="ok")
				{
					$("#menu_merge_parts").click();
					notify("0done");				
				}
				else
					alert(data);
			},
			async:false
		});

	}
}

function survive_part(id_parts,pn)
{
	if($('#id_part_dead').val()!=id_parts)
	{
		$('#id_part_alive').val(id_parts);
		$('#alivePart').val(pn);
	}
}
function kill_part(id_parts,pn)
{
	if($('#id_part_alive').val()!=id_parts)
	{
		$('#id_part_dead').val(id_parts);
		$('#deadPart').val(pn);
	}
}
