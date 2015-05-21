function test(com,grid)
{
	return true;
}

function initTopButtons()
{
	$("img[@id^='btn']").bind(	"mouseover",
		function(){
			$(this).fadeTo(100, 0.5);
			$(this).css({cursor:'pointer'});
		});

	$("img[@id^='btn']").bind(	"mouseout",
		function(){
			$(this).fadeTo(100, 1.0);
			$(this).css({cursor:'default'});
		});

	$("#btn_users").bind(	"click",
		function(){
			loadUsersMenu();
		}
	);
}

function initAjax()
{
	$("body").ajaxError
	(
	function(event, request, settings)
	{
		alert(settings.url); 
	}
	);
}

function loadUsersMenu()
{
	$("#left_frame_1 > .frame_body").load(
		"include/usersMenu.php",
		{gino:"latino"},
		function(){

	$("img[@id^='btn']").bind(	"mouseover",
	function(){
		$(this).fadeTo(100, 0.5);
		$(this).css({cursor:'pointer'});
	});

	$("img[@id^='btn']").bind(	"mouseout",
	function(){
		$(this).fadeTo(100, 1.0);
		$(this).css({cursor:'default'});
	});

	/*************************/
	/* NEW USER BUTTON CLICK */
	/*#######################*/
	$("#btn_new_user").bind(	"click",
	function(){

		$("#right_frame_1").show();
		$(".flexigrid").hide();

		$("#right_frame_1 > .frame_title").text("Loading");

		$("#right_frame_1 > .frame_body").html(
			"<img 	id='loading_img'"
				+"	src='img/bball.gif'" 
				+"	alt='loading...'/>"
				+"	<br/>Loading user form");

			$("#right_frame_1 > .frame_body")
				.css({textAlign:'center'});

			$("#right_frame_1 > .frame_body").load(
				"include/userForm.php",
				{gino:"latino"},
				function(){

					$("#right_frame_1 > .frame_body").css(
						{textAlign:'left'}
					);
					
					$("#right_frame_1 > .frame_title")
						.text("New user");

					$('#userForm').ajaxForm(
					{
						beforeSubmit:	
						function(formData, jqForm, options)
						{
							return checkUserForm("userForm");
						},
						success:	function(data)
						{
							alert(data);
						}
			        }
					);
				}
			);
		}
	);
	/*###########################*/
	/* END NEW USER BUTTON CLICK */
	/*****************************/

	/***************************/
	/* LIST USERS BUTTON CLICK */
	/*#########################*/
	$("#btn_list_users").bind(	"click",
	function(){

		$("#right_frame_1 > .frame_title").text("Loading");

		$("#right_frame_1 > .frame_body").html(
			"<img 	id='loading_img'"
				+"	src='img/bball.gif'" 
				+"	alt='loading...'/>"
				+"	<br/>Loading user list");

		$("#right_frame_1 > .frame_body")
			.css({textAlign:'center'});

		/*$("#right_frame_1 > .frame_body")*/
		$("#flexigrid_frame")
			.flexigrid
			(
			{
			url: 'include/userList.php',
			dataType: 'json',
			colModel : [
				{display: 'Username', name : 'username', width : 80, sortable : true, align: 'left'},
				{display: 'Name', name : 'name', width : 80, sortable : true, align: 'left'},
				{display: 'Surname', name : 'surname', width : 90, sortable : true, align: 'left'},
				{display: 'Email', name : 'email', width : 90, sortable : true, align: 'left'},
				{display: 'Level', name : 'usertype', width : 90, sortable : true, align: 'left'},
				{display: 'Simulator', name : 'simulator', width : 90, sortable : true, align: 'left'},
				{display: 'Active', name : 'active', width : 90, sortable : true, align: 'left'},
				{display: 'Expired', name : 'expired', width : 90, sortable : true, align: 'right'}
				],
			/*buttons : [
				{name: 'Add', bclass: 'add', onpress : test},
				{name: 'Delete', bclass: 'delete', onpress : test}
				],*/
			/*searchitems : [
				{display: 'ISO', name : 'iso'},
				{display: 'Name', name : 'name', isdefault: true}
				],*/
			sortname: "username",
			sortorder: "asc",
			usepager: true,
			title: 'Users',
			useRp: true,
			rp: 10,
			showTableToggleBtn: true,
			width: 700,
			height: 255
			}
			);

		$("#right_frame_1").hide();
		$(".flexigrid").show();
	});
	/*#############################*/
	/* END LIST USERS BUTTON CLICK */
	/*******************************/
});}