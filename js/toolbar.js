function tb_parts_list()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_parts").addClass("active");
	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbSave").hide();
	$("#tbApply").hide();
	$("#tbReset").hide();
	$("#tbClear").hide();
	$("#tbDelete").hide();
	$("#tbMove").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();

	/**** show needed buttons ****/
	if(selectedPartId)
	{
		$("#tbDetails").show();
		$("#tbEdit").show();
	}
	else
	{
		$("#tbDetails").hide();
		$("#tbEdit").hide();
	}
	$("#tbClose").show();

	/**** add handlers for vieweable buttons ****/
	$("#tbDetails").unbind('click');
	$("#tbDetails").click(function(){
		detailsPartButton_Click(null,null);
	});
	$("#tbEdit").unbind('click');
	$("#tbEdit").click(function()
	{
		editPartButton_Click(null,null);
	});
	$("#tbDeactivate").unbind('click');
	$("#tbDeactivate").click(function()
	{
		deactivatePartButton_Click(null,null);
	});
	$("#tbReactivate").unbind('click');
	$("#tbReactivate").click(function()
	{
		activatePartButton_Click(null,null);
	});
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").hide();
		$("#flexi_container").hide();
		$("#form_container").hide();
	});
}

function tb_add_part()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_parts").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDelete").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbMove").hide();
	$("#tbApply").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbClear").show();
	$("#tbClose").show();


	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function(){
		$("#partForm").submit();
	});
	$("#tbClear").unbind('click');
	$("#tbClear").click(function()
	{
		$("#partForm").clearForm();
		$("#supplierSelect")[0].selectedIndex=0;
		$("#supplierCheck").html("");
		$("#pnSupplierCheck").html("");
		$("#manufacturerSelect")[0].selectedIndex=0;
		$("#subsystemSelect")[0].selectedIndex=0;
		$("#subsystemCheck").html("");
		$("#crdSelect")[0].selectedIndex=1;
		$("#shelfLifeUnitSelect")[0].selectedIndex=0;
		$("#supplierSelect").focus();
	});
	
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#menu_part_list").click();
	});
}

function tb_edit_part()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_parts").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbMove").hide();
	$("#tbDelete").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbReset").show();
	$("#tbClose").show();	

	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function()
	{
		$("#partForm").submit();
	});
	$("#tbReset").unbind('click');
	$("#tbReset").click(function()
	{
		$("#partForm").resetForm();
	});
	$('#tbClear').unbind('click');
	$("#tbClear").click(function()
	{
		$("#partForm").clearForm();
	});

	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#flexi_container").show();
		$("#form_container").hide();
		tb_parts_list();
	});
}

function tb_details_item()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_items").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbMove").hide();
	$("#tbDelete").hide();

	/**** show viewable buttons ****/
	$("#tbClose").show();	

	/**** add handlers for viewable buttons ****/
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#flexi_container").show();
		$("#form_container").hide();
		tb_items_list();
	});
}

function tb_edit_item()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_items").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbMove").hide();
	$("#tbDelete").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbReset").show();
	$("#tbClose").show();	

	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function()
	{
		$("#itemForm").submit();
	});
	$("#tbReset").unbind('click');
	$("#tbReset").click(function()
	{
		$("#itemForm").resetForm();
	});
	$('#tbClear').unbind('click');
	$("#tbClear").click(function()
	{
		$("#itemForm").clearForm();
	});

	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#flexi_container").show();
		$("#form_container").hide();
		tb_items_list();
	});
}

function tb_details_part()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_parts").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbDelete").hide();
	$("#tbSave").hide();
	$("#tbMove").hide();
	$("#tbReset").hide();

	/**** show viewable buttons ****/
	$("#tbClose").show();	

	/**** add handlers for viewable buttons ****/
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#flexi_container").show();
		$("#form_container").hide();
		tb_parts_list();
	});
}

function tb_details_movement()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_movements").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbDelete").hide();
	$("#tbSave").hide();
	$("#tbMove").hide();
	$("#tbReset").hide();

	/**** show viewable buttons ****/
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#form_container").hide();
		$("#flexi_container").show();
		tb_list_movements();
	});

}

function tb_users_list()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_users").addClass("active");

	/**** hide unused buttons ****/
	$("#tbSave").hide();
	$("#tbApply").hide();
	$("#tbReset").hide();
	$("#tbClear").hide();
	$("#tbDetails").hide();
	$("#tbDelete").hide();
	$("#tbMove").hide();

	/**** show needed buttons ****/
	$("#tbAdd").show();
	if(selectedUserId)
	{
		$("#tbEdit").show();
	}
	else
	{
	//	$("#tbDetails").hide();
		$("#tbEdit").hide();
	}

	$("#tbDeactivate").show();
	$("#tbReactivate").show();
	$("#tbClose").show();

	/**** add handlers for vieweable buttons ****/
	$("#tbAdd").unbind('click');
	$("#tbAdd").click(function()
	{
		addUserButton_Click(null,null);
	});
	$("#tbEdit").unbind('click');
	$("#tbEdit").click(function()
	{
		editUserButton_Click(null,null);
	});
	$("#tbDeactivate").unbind('click');
	$("#tbDeactivate").click(function()
	{
		deactivateUserButton_Click(null,null);
	});
	$("#tbReactivate").unbind('click');
	$("#tbReactivate").click(function()
	{
		activateUserButton_Click(null,null);
	});
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").hide();
		$("#flexi_container").hide();
		$("#form_container").hide();
	});
}

function tb_edit_user()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_users").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbDelete").hide();
	$("#tbMove").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbReset").show();
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function()
	{
		$("#userForm").submit();
	});
	$("#tbReset").unbind('click');
	$("#tbReset").click(function()
	{
		$("#userForm").resetForm();
	});
	$("#tbClear").unbind('click');
	$("#tbClear").click(function()
	{
		$("#userForm").clearForm();
	});
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#flexi_container").show();
		$("#form_container").hide();
		tb_users_list();
	});
}

function tb_add_user()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_users").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDelete").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbApply").hide();
	$("#tbMove").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbClear").show();
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function()
	{
		$("#userForm").submit();
	});
	$("#tbClear").unbind('click');
	$("#tbClear").click(function()
	{
		$("#userForm").clearForm();
	});
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#menu_users_list").click();
	});
}

function tb_add_movement()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_movements").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDelete").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbApply").hide();
	$("#tbMove").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbClear").show();
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function()
	{
		$("#movementForm").submit();
	});
	$("#tbClear").unbind('click');
	$("#tbClear").click(function()
	{
		$("#movementForm").clearForm();
		addMovementClearForm();
	});
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#menu_movements_list").click();
	});
}

function tb_edit_movement() 
{
	$("#jd_menu li").removeClass("active");
	$("#jd_movements").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDelete").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbApply").hide();
	$("#tbMove").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbClear").show();
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function()
	{
		$("#movementForm").submit();
	});
	$("#tbClear").unbind('click');
	$("#tbClear").click(function()
	{
		$("#movementForm").clearForm();
	});
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#form_container").hide();
		$("#flexi_container").show();
		tb_list_movements();
	});
}

function tb_list_movements()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_movements").addClass("active");

	/**** hide unused buttons ****/

	$("#tbDelete").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbApply").hide();
	$("#tbClear").hide();
	$("#tbSave").hide();
	$("#tbMove").hide();

	/**** show viewable buttons ****/
	$("#tbAdd").show();
	if(selectedMovementId)
	{
		$("#tbEdit").show();
		$("#tbDetails").show();
	}
	else
	{
		$("#tbDetails").hide();
		$("#tbEdit").hide();
	}
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbAdd").unbind('click');
	$("#tbAdd").click(function()
	{
		addMovementButton_Click(null)
	});
	$("#tbEdit").unbind('click');
	$("#tbEdit").click(function()
	{
		editMovementButton_Click(null)
	});
	$("#tbDetails").unbind('click');
	$("#tbDetails").click(function()
	{
		detailsMovementButton_Click(null,null);
	});

	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").hide();
		$("#flexi_container").hide();
		$("#form_container").hide();
	});
}

function tb_items_list()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_items").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbSave").hide();
	$("#tbApply").hide();
	$("#tbReset").hide();
	$("#tbClear").hide();
	$("#tbDelete").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	/**** show needed buttons ****/
	if(selectedItemId)
	{
		$("#tbDetails").show();
		$("#tbEdit").show();
		$("#tbMove").show();
		if(session_level==1)
			$("#tbDelete").show();
	}
	else
	{
		$("#tbDetails").hide();
		$("#tbEdit").hide();
		$("#tbMove").hide();
	}

	$("#tbClose").show();

	/**** add handlers for vieweable buttons ****/
	$("#tbDelete").unbind('click');
	$("#tbDelete").click(function()
	{
		deleteItemButton_Click(null,null);
	});
	$("#tbMove").unbind('click');
	$("#tbMove").click(function()
	{
		moveItemButton_Click(null,null);
	});
	$("#tbDetails").unbind('click');
	$("#tbDetails").click(function()
	{
		detailsItemButton_Click(null,null);
	});
	$("#tbEdit").unbind('click');
	$("#tbEdit").click(function()
	{
		editItemButton_Click(null,null);
	});
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").hide();
		$("#flexi_container").hide();
		$("#form_container").hide();
	});
}

function tb_places_list()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_places").addClass("active");

	/**** hide unused buttons ****/
	$("#tbDelete").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbApply").hide();
	$("#tbReset").hide();
	$("#tbMove").hide();
	$("#tbSave").hide();
	$("#tbClear").hide();

	/**** show viewable buttons ****/
	$("#tbAdd").show();
	if(selectedPlaceId)
	{
		$("#tbDetails").show();
		$("#tbEdit").show();
	}
	else
	{
		$("#tbDetails").hide();
		$("#tbEdit").hide();
	}
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/

	$("#tbDeactivate").unbind('click');
	$("#tbDeactivate").click(function()
	{
		deactivatePlaceButton_Click();
	});
	$("#tbReactivate").unbind('click');
	$("#tbReactivate").click(function()
	{
		activatePlaceButton_Click();
	});

	$("#tbAdd").unbind('click');
	$("#tbAdd").click(function()
	{
		addPlaceButton_Click(null,null);
	});
	$("#tbDetails").unbind('click');
	$("#tbDetails").click(function()
	{
		detailsPlaceButton_Click(null,null);
	});
	$("#tbEdit").unbind('click');
	$("#tbEdit").click(function()
	{
		editPlaceButton_Click(null,null);
	});
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").hide();
		$("#flexi_container").hide();
		$("#form_container").hide();
	});
}

function tb_add_place()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_places").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDelete").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbMove").hide();
	$("#tbApply").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbClear").show();
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function()
	{
		$("#placeForm").submit();
	});
	$("#tbClear").unbind('click');
	$("#tbClear").click(function()
	{
		$("#placeForm").clearForm();
	});
	
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#menu_places_list").click();
	});
}

function tb_edit_place() 
{
	$("#jd_menu li").removeClass("active");
	$("#jd_places").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDelete").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbMove").hide();
	$("#tbApply").hide();
	$("#tbClear").hide();

	/**** show viewable buttons ****/
	$("#tbSave").show();
	$("#tbReset").show();
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbSave").unbind('click');
	$("#tbSave").click(function()
	{
		$("#placeForm").submit();
	});
	$("#tbReset").unbind('click');
	$("#tbReset").click(function()
	{
		$("#placeForm").resetForm();
	});
	
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#flexi_container").show();
		$("#form_container").hide();
		tb_places_list();
	});
}

function tb_details_place()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_places").addClass("active");

	/**** hide unused buttons ****/
	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbDelete").hide();
	$("#tbSave").hide();
	$("#tbMove").hide();
	$("#tbReset").hide();

	/**** show viewable buttons ****/
	$("#tbClose").show();

	/**** add handlers for viewable buttons ****/
	$("#tbClose").unbind('click');
	$("#tbClose").click(function()
	{
		$("#toolbar_container").show();
		$("#flexi_container").show();
		$("#form_container").hide();
		tb_places_list();
	});
}

function tb_reports()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_reports").addClass("active");

	hideRelevantMenus();
	$("#toolbar_container").hide();
/*	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbDelete").hide();
	$("#tbSave").hide();
	$("#tbMove").hide();
	$("#tbReset").hide();
	$("#tbClose").hide();*/
}

function tb_admin()
{
	$("#jd_menu li").removeClass("active");
	$("#jd_admin").addClass("active");

	hideRelevantMenus();
	$("#toolbar_container").hide();
/*	$("#tbAdd").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();
	$("#tbApply").hide();
	$("#tbDeactivate").hide();
	$("#tbReactivate").hide();
	$("#tbClear").hide();
	$("#tbDelete").hide();
	$("#tbSave").hide();
	$("#tbMove").hide();
	$("#tbReset").hide();
	$("#tbClose").hide();*/
}
