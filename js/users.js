/*----------------------------------------------------------------------------*/
/*																	*/
/*								USERS								*/
/*																	*/
/*----------------------------------------------------------------------------*/

var flexiData;

function bind_menu_users_list_click()
{
	$("#menu_users_list").unbind('click');
	$("#menu_users_list").bind(	"click",
	function()
	{
		listUsersButton_Click(false);
	});
}

function bind_menu_users_search_click()
{
	$("#menu_users_search").unbind('click');
	$("#menu_users_search").bind("click",
	function()
	{
		listUsersButton_Click(true);
	});
}

function listUsersButton_Click(search)
{
	hideRelevantMenus();
	selectedUserId = false;
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
			url: 'include/users.php',
			params: [{name:'func',value:'list'}] ,
			dataType: 'json',
			colModel : 
			[
				{
					display: 'Username', 
					name : 'username', 
					width : 80, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Name', 
					name : 'name', 
					width : 80, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Surname', 
					name : 'surname', 
					width : 90, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Email', 
					name : 'email', 
					width : 180, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Level', 
					name : 'usertype', 
					width : 90, 
					sortable : true, 
					align: 'left'
				},
				{
					display: 'Active', 
					name : 'active', 
					width : 90, 
					sortable : true, 
					align: 'center'
				},
				{
					display: 'Expired', 
					name : 'expired', 
					width : 90, 
					sortable : true, 
					align: 'center'
				}
			],/*
			buttons : 
			[
				{
					name: 'Add', 
					bclass: 'add', 
					onpress : addUserButton_Click
				},
				{
					name: 'Delete', 
					bclass: 'delete', 
					onpress : deleteUserButton_Click
				},
				{
					name: 'Deactivate', 
					bclass: 'deactivate', 
					onpress : deactivateUserButton_Click
				},
				{
					name: 'Reactivate', 
					bclass: 'activate', 
					onpress : activateUserButton_Click
				},
				{
					name: 'Expire', 
					bclass: 'expire', 
					onpress : expireUserButton_Click
				},
				{
					name: 'Edit', 
					bclass: 'edit', 
					onpress : editUserButton_Click
				}
			],*/
			searchitems : 
			[
				{
					display: 'Username', 
					name : 'username'
				},
				{
					display: 'Name', 
					name : 'users.name', 
					isdefault: true
				},
				{
					display: 'Email', 
					name : 'users.email'
				}
			],
			sortname: "username",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			qtype: "username",
			query: (search?"x":""),
			rp: global_rp,
			width: 'auto',
			height: 'auto',
			singleSelect: true,
			onRowSelected:users_row_selected,
			onRowSelectedClick:users_row_selected_click,
			onRowDeselected:users_row_deselected,
			onNumRowsChange: onNumRowsChange,
			hideOnSubmit:false,
			preProcess:copyFlexiData
		}
	);
	if(search)
	{
		$('.sDiv').show();
		$('.qsbox').focus();
	}
	tb_users_list();
	$("#toolbar_container").show();
}

/* callback when row is selected in users table */
function users_row_selected(userId,row,grid)
{
	$("#menu_users_edit").show();
	$("#tbDetails").show();
	$("#tbEdit").show();

	selectedUserId = userId.substr(3);
	//notify("users_row_click" + selectedUserId);
}

function users_row_selected_click(userId,row,grid)
{
	editUserButton_Click(null,null);
}

/* callback when row is deselected in users table */
function users_row_deselected(userId,row,grid)
{
	$("#menu_users_edit").hide();
	$("#tbDetails").hide();
	$("#tbEdit").hide();

	selectedUserId = false;
	//notify("users_row_click" + selectedUserId);
}

function deactivateUserButton_Click(com,grid)
{
	if(selectedUserId)
	{
		if(confirm("Really deactivate user " + selectedUserId + "?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/users.php",
				data:	"func=deactivate&userId="+selectedUserId,
				success: function(msg)
				{
					notify( msg );
					$(".flexi").flexReload();
				}
			});
		}
	}
}

function activateUserButton_Click(com,grid)
{
	if(selectedUserId)
	{
		if(confirm("Really activate user " + selectedUserId + "?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/users.php",
				data:	"func=activate&userId="+selectedUserId,
				success: function(msg)
				{
					notify( msg );
					flexiReload();
				}
			});
		}
	}
}

function editUserButton_Click(com,grid)
{
	$("#flexi_container").hide();
	$("#loading_container").show();
	/*	load new user form into right frame	*/
	$("#form_container").load(
		"include/users.php",
		{func:'editUserForm',userId:selectedUserId},
		function() {
			$('#userForm').ajaxForm(
			{
				beforeSubmit: function(formData, jqForm, options)
				{
					var out=checkUserForm("userForm");
					if(out)
						flexiShowWait();
					return(out);
				},
				success: function(data)
				{
					flexiShowFlexi();
					tb_users_list();
					notify(data);
					flexiReload();
				}
			});
			tb_edit_user();
			$("#loading_container").hide();
			$("#form_container").show();
			$("#userNameInput").focus();
		}
	);
}


function expireUserButton_Click(com,grid)
{
	if(selectedUserId)
	{
		if(confirm("Really expire user " + selectedUserId + "?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/users.php",
				data:	"func=expire&userId="+selectedUserId,
				success: function(msg)
				{
					notify( msg );
					flexiReload();
				}
			});
		}
	}
}

function deleteUserButton_Click(com,grid)
{
	if(selectedUserId)
	{
		if(confirm("Really DELETE user " + flexiData[selectedUserId].cell[0] + " ?"))
		{
			$.ajax(
			{
				type:	"POST",
				url:	"include/users.php",
				data:	"func=delete&userId="+selectedUserId,
				success: function(msg)
				{
					notify( msg );
					flexiReload();
				}
			});
		}
	}
}

function addUserButton_Click(com,grid)
{
	$("#flexi_container").hide();
	$("#loading_container").show();
	$("#form_container").load(
		"include/users.php",
		{func:'newUserForm'},
		function()
		{
			$('#userForm').ajaxForm(
			{
				beforeSubmit:	
				function(formData, jqForm, options)
				{
					var out=checkUserForm("userForm");
					if(out)
						flexiShowWait();
					return(out);
				},
				success:	function(data)
				{
					flexiShowFlexi();
					notify(data);
					flexiReload();
				}
			});
			tb_add_user();
			$("#loading_container").hide();
			$("#toolbar_container").show();
			$("#form_container").show();
			$("#userNameInput").focus();
		}
	);
}

function bind_menu_users_new_click()
{
	$('#menu_users_new').unbind('click');
	$('#menu_users_new').bind('click',
	function ()
	{
		addUserButton_Click(null,null);
	});
}

function bind_menu_users_edit_click()
{
	$('#menu_users_edit').unbind('click');
	$('#menu_users_edit').bind('click',
	function ()
	{
		editUserButton_Click(null,null);
	});
}
function copyFlexiData(data)
{
	flexiData = new Array();
	jQuery.each(	data.rows,
					function()
					{
						flexiData[this.id]=this;
					}
				);
	return data;
}

function checkUserForm(formId)
{

	/* prepare useful variables */
	var userIdSelector = "#"+formId+" #userId";
	var userNameInputSelector = "#"+formId+" #userNameInput";
	var userNameCheckSelector = "#"+formId+" #userNameCheck";
	var nameInputSelector = "#"+formId+" #nameInput";
	var nameCheckSelector = "#"+formId+" #nameCheck";
	var surnameInputSelector = "#"+formId+" #surnameInput";
	var surnameCheckSelector = "#"+formId+" #surnameCheck";
	var emailInputSelector = "#"+formId+" #emailInput";
	var emailCheckSelector = "#"+formId+" #emailCheck";
	var badColor = '#F00';
	var goodColor = '#0F0';
	var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;
	var formIsGood = true;
	var usernameExists = false;

	/* test empty username */
	if( jQuery.trim($(userNameInputSelector).attr("value")) == "" )
	{
		$(userNameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - username required");
		$(userNameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}
	else
	{
		if(($('#func').val()=='edit')
			||($('#func').val()=='add'))
		{
			var ajaxData = {
							func:'list',
							whereString: ' AND users.username="'
								+jQuery.trim($(userNameInputSelector).val())
								+'" AND users.id!="'+$(userIdSelector).val()+'"'
						}
		}

		$.ajax({
			type:'POST',
			async:false,
			url:'include/users.php',
			data:ajaxData,
			dataType:'json',
			success:function(JSONdata,textStatus)
			{
				if(JSONdata.rows.length > 0)
				{
					formIsGood = false;
					$(userNameCheckSelector)
						.html("<img src='img/stock_no_s.png' alt='error'> - username already exists");
					$(userNameCheckSelector)
						.css({color:badColor});
					usernameExists = true;
				}
				else
				{
					$(userNameCheckSelector)
						.html("<img src='img/stock_ok_s.png' alt='ok'>");
					$(userNameCheckSelector)
						.css({color:goodColor});
				}
			}
		});
	}

	if( jQuery.trim($(nameInputSelector).attr("value")) == "" )
	{
		$(nameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - name required");
		$(nameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(nameCheckSelector)
			.html("<img src='img/stock_ok_s.png' alt='ok'>");
		$(nameCheckSelector)
			.css({color:goodColor});
	}

	if( jQuery.trim($(surnameInputSelector).attr("value")) == "" )
	{
		$(surnameCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - surname required");
		$(surnameCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else
	{
		$(surnameCheckSelector)
			.html("<img src='img/stock_ok_s.png' alt='ok'>");
		$(surnameCheckSelector)
			.css({color:goodColor});
	}
	
	
	if( jQuery.trim($(emailInputSelector).attr("value")) == "" )
	{
		$(emailCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - email address required");
		$(emailCheckSelector)
			.css({color:badColor});
		formIsGood = false;
	}else 
	if	(!emailRegex.test(
			jQuery.trim($(emailInputSelector).attr("value"))
			)
		)
	{
		$(emailCheckSelector)
			.html("<img src='img/stock_no_s.png' alt='error'> - incorrect email format");
		$(emailCheckSelector)
			.css({color:badColor,padding:'0px'});
		formIsGood = false;
	}else
	{
		$(emailCheckSelector)
			.html("<img src='img/stock_ok_s.png' alt='ok'>");
		$(emailCheckSelector)
			.css({color:goodColor});
	}
	
	return(formIsGood);
}
/*----------------------------------------------------------------------------*/
