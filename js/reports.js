
function bind_reports_movements_click()
{
	$("#menu_reports_movements").unbind('click');
	$("#menu_reports_movements").bind("click",
	function()
	{
		tb_reports();
		$("#form_container").hide();
		$("#flexi_container").hide();
		$("#loading_container").show();

		$("#form_container").load
		(
			"include/reports.php",
			{
				func:'reportMovementsForm'
			},
			function()
			{
				$("#loading_container").hide();
				$("#form_container").show();
			}
		);
	});
}

function bind_reports_warehouse_click()
{
	$("#menu_reports_warehouse").unbind('click');
	$("#menu_reports_warehouse").bind("click",
	function()
	{
		tb_reports();
		$("#form_container").hide();
		$("#flexi_container").hide();
		$("#loading_container").show();
		$("#form_container").load
		(
			"include/reports.php",
			{
				func:'reportWareHouseForm'
			},
			function()
			{
				$("#loading_container").hide();
				$("#form_container").show();
			}
		);
	});
}

function bind_reports_fotografia_click()
{
	$("#menu_reports_fotografia").unbind('click');
	$("#menu_reports_fotografia").bind("click",
	function()
	{
		tb_reports();
		$("#form_container").hide();
		$("#flexi_container").hide();
		$("#loading_container").show();
		$("#form_container").load
		(
			"include/reports.php",
			{
				func:'reportFotografia'
			},
			function()
			{
				$("#loading_container").hide();
				$("#form_container").show();
//				update_fotografia_output();
			}
		);
	});
}
