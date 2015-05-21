/*----------------------------------------------------------------------------*/
/*																	*/
/*							GLOBAL VARIABLES							*/
/*																	*/
/*----------------------------------------------------------------------------*/

var global_rp = 15;
var selectedUserId = false;
var selectedWarehouseId = false;
var selectedPartId = false;
var selectedItemId = false;
var selectedPlaceId = false;

function flexiShowWait()
{
	$("#loading_container").show();
	$("#form_container").hide();
	$("#toolbar_container").hide();
}

function flexiShowForm()
{
	$("#loading_container").hide();
	$("#form_container").show();
	$("#toolbar_container").show();
}

function flexiShowFlexi()
{
	$("#loading_container").hide();
	$("#flexi_container").show();
	$("#toolbar_container").show();
}


function flexiReload()
{	
/*	$("#form_container").hide();
	$("#flexi_container").hide();
	$("#loading_container").show();*/
	$(".flexi").flexReload();
/*	$("#flexi_container").show();
	$("#loading_container").hide();*/
}

/*----------------------------------------------------------------------------*/


/*----------------------------------------------------------------------------*/
/*																	*/
/*							GENERIC FUNCTIONS							*/
/*																	*/
/*----------------------------------------------------------------------------*/

function initMenu() {
	$("ul.jd_menu").jdMenu({showDelay:0,hideDelay:0});
	bind_menu_part_list_click();
	bind_menu_part_search_click();
	bind_menu_part_edit_click();
	bind_menu_movements_list_click();
	bind_menu_movements_search_click();
	bind_menu_movements_new_click();
	bind_menu_movements_edit_click();
	bind_menu_items_list_click();
	bind_menu_items_move_click();
	bind_menu_items_edit_click();
	bind_menu_items_search_click();
	bind_menu_users_new_click();
	bind_menu_users_edit_click();
	bind_menu_users_list_click();
	bind_menu_users_search_click();
	bind_menu_places_list_click();
	bind_menu_places_search_click();
	bind_menu_places_new_click();
	bind_menu_places_edit_click();
	bind_reports_movements_click();
	bind_reports_warehouse_click();
	bind_reports_fotografia_click();
	bind_menu_admin_compatibles_click();
	bind_manu_admin_merge_parts_click();
}


function initAjax()
{
	$("body").ajaxError
	(
		function(event, request, settings)
		{
			//Debug, must remove for release
			alert(	
				"QUESTO E' UN ERRORE AJAX\n\n" 
				+ settings.url 
				+ "\n\n" 
				+ request.responseText
			); 
		}
	);
}
/*----------------------------------------------------------------------------*/

/*----------------------------------------------------------------------------*/
/*																	*/
/*						OTHER FUNCTIONS								*/
/*																	*/
/*----------------------------------------------------------------------------*/


/* controlla se è stato premuto enter nel password o username fields della login
form. Se si, crea la stringa malata di carlo e submitta la form */
function checkEnter(e,passwordInputId,loginFormId,randomString)
{ 
	var characterCode;
	if(e && e.which)
	{ 
		e = e;
		characterCode = e.which;
	}
	else
	{
		e = e;
		characterCode = e.keyCode;
	}

	if(characterCode == 13)
	{
		$("#"+passwordInputId).val( 
			hex_md5(randomString+hex_md5($("#"+passwordInputId).val())));
		$("#"+loginFormId).submit();
		return false;
	}
	else
		return true;
}

function hideRelevantMenus()
{
	$("#menu_movements_edit").hide();
	$("#menu_users_edit").hide();
	$("#menu_places_edit").hide();
	$("#menu_inventory_edit").hide();
	$("#menu_inventory_move").hide();
	$("#menu_part_edit").hide();
}

function onNumRowsChange(value)
{
	global_rp=value;
}

function doLogout()
{
	clearInterval(globalKeep);
	$("#form_container").hide();
	$("#flexi_container").hide();
	$("#loading_container").hide();
}

function notify(message)
{
	message=$.trim(message);
	if(message.substr(0,1)=='0')
		messagebox(message.substr(1));
	else
		errorbox(message);
}

function messagebox(message)
{

	$("#messageBox").css("height","40px");
	$("#messageBox").removeClass().addClass("messagebox").stop(true).hide().html(message).slideDown(2000).delay(3000).slideUp(2000);
}
function errorbox(message)
{
	$("#messageBox").css("height","40px");
	$("#messageBox").removeClass().addClass("errorbox").stop(true).hide().html(message).slideDown(2000).delay(3000).slideUp(2000);
}

$(document).ready(
function()
{
	$("#messageBox").hide();
}
)