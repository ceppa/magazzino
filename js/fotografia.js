function update_fotografia_output()
{
	var system=$("#snapshotSystemSelect").val();
	var subsystem=$("#snapshotSubsystemSelect").val();
	$("#fotografia_output").html("<img src='img/indicator.gif'>");
	$.post("include/fotografia.php", { func: "doReportFotografia"
		, system: system, subsystem: subsystem }, 
	function(data) 
	{
		$("#fotografia_output").html(data);
	});
}

function print_fotografia()
{
	var system=$("#snapshotSystemSelect").val();
	var subsystem=$("#snapshotSubsystemSelect").val();
	var pars="func=doPrintFotografia&system="+escape(system)+"&subsystem="+escape(subsystem);
	window.open("include/fotografia.php?"+pars);
}

function export_fotografia()
{
	var system=$("#snapshotSystemSelect").val();
	var subsystem=$("#snapshotSubsystemSelect").val();
	var pars="func=doExportFotografia&system="+escape(system)+"&subsystem="+escape(subsystem);
	window.open("include/fotografia.php?"+pars);
}
