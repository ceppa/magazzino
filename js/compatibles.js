function updateCompatibles()
{
	subsystem=$("#snapshotSubsystemSelect").val();
	$("#compatibles_output").html("<img src='img/indicator.gif'>");
	$.post("include/compatibles.php", { func: "updateCompatibles"
		, subsystem: subsystem }, 
	function(data) 
	{
		$("#compatibles_output").html(data);
		$('input[id^="pn_"]').each( function()
		{
			var splitted=$(this)[0].id.split("_");
			var id_compatible=splitted[1];
			var location=splitted.slice(2).join("_")

			var excluded="";
			$('tr[id^="row_'+id_compatible+'"]').each( function()
			{
				var id_parts=$(this)[0].id.split("_")[2];
				excluded+=","+id_parts;
			});
			if(excluded.length)
				excluded=excluded.substr(1);
			
			$('#pn_'+id_compatible+'_'+location).autocomplete("include/itemsAutocompleteBackend.php", 
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
						onItemSelect:setButtonClick,
						extraParams:{exclude:excluded},
						rowNumber:id_compatible+'_'+location,
						selectOnly:1,
						mustMatch:1
					});

		});
	});
}

function addCompatible(id_compatible,id_parts)
{
	$.ajax(
	{
		type: 'POST',
		url: "include/compatibles.php",
		data: { 
				func: "addCompatible",
				id_compatible: id_compatible, 
				id_parts: id_parts
			},
		success: function(data)
		{
			if($.trim(data)=="ok")
				updateCompatibles()
			else
				alert(data);
		},
		async:false
	});
}

function setButtonClick(li)
{
	if(li.extra[1] && li.extra[1].length)
	{
		var button=$('#add_'+li.rowNumber);
		button.removeAttr('disabled');
		var id_compatible=li.rowNumber.split("_")[0];
		var location=li.rowNumber.split("_")[1];
		var id_parts=li.extra[1];
		button.bind("click",
		function()
		{
			addCompatible(id_compatible,id_parts);
		});
	}
	
}

function deleteCompatible(id_compatible,id_parts)
{
	$.ajax(
	{
		type: 'POST',
		url: "include/compatibles.php",
		data: { 
				func: "deleteCompatible",
				id_compatible: id_compatible, 
				id_parts: id_parts
			},
		success: function(data)
		{
			if($.trim(data)=="ok")
			{
				updateCompatibles()
			}
			else
				alert(data);
		},
		async:false
	});
}