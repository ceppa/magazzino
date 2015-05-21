<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Magazzino</title>

<!-- JAVASCRIPT LIBRARIES -->
<script	type="text/javascript"
		src="js/jquery-1.2.4b.js"></script>
<script	type="text/javascript"
		src="js/config.js"></script>
<script	type="text/javascript"
		src="js/utilities.js"></script>
<script	type="text/javascript"
		src="js/formChecks.js"></script>
<script	type="text/javascript"
		src="js/jquery.form.js"></script>
<script	type="text/javascript"
		src="js/main.js"></script>
<script	type="text/javascript"
		src="js/flexigrid/flexigrid.js"></script>
<script	type="text/javascript"
		src="js/flexigrid/flexigrid.pack.js"></script>
<script	type="text/javascript">
$(document).ready(function(){
	initAjax();
	initTopButtons();
});
</script>
	
<!-- CSS -->
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/reset.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/text.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/960.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/my.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="js/flexigrid/css/flexigrid/flexigrid.css" />

<style type="text/css" media="all">
body {
	padding: 0px 0 40px;
	font-family:Helvetica, sans-serif;
	background:#EEE url(img/header_bg.png) repeat-x;
}

p {
	overflow: hidden;
}

#fragment-1 > p {
	border: none;
}

.grid_1{width:60px}
.grid_2{width:140px}
.grid_3{width:220px}
.grid_4{width:300px}
.grid_5{width:380px}
.grid_6{width:460px}
.grid_7{width:540px}
.grid_8{width:620px}
.grid_9{width:700px}
.grid_10{width:780px}
.grid_11{width:860px}
.grid_12{width:940px}

.grid_1,
.grid_2,
.grid_3,
.grid_4,
.grid_5,
.grid_6,
.grid_7,
.grid_8,
.grid_9,
.grid_10,
.grid_11,
.grid_12 {
	display:inline;
	float:left;
	margin-left:10px;
	margin-right:10px;
}

html body * span.clear,
html body * div.clear,
html body * li.clear,
html body * dd.clear
{
	background:none;
	border:0;
	clear:both;
	display:block;
	float:none;
	font-size:0;
	list-style:none;
	margin:0;
	padding:0;
	overflow:hidden;
	visibility:hidden;
	width:0;
	height:0
}
.clearfix:after
{
	clear:both;
	content:'.';
	display:block;
	visibility:hidden;
	height:0
}
.clearfix
{
	display:inline-block
}
* html .clearfix{height:1%}
.clearfix{display:block}

#header {
	margin-top:0px;
	margin-bottom:20px;

}

</style>
</head>
<body>
	<div	class="container_12">
		<div	id="header"
				class="grid_3"
				style="text-align:center;">
			<img src="img/header_efa.png" alt="efa" />
		</div>
		<div	class="grid_1"
				style="text-align:center;height:103px;">
			<img	id="btn_stats"
					src="img/btn_stats.gif"
					alt="Statistiche"
					style="	position:relative;
							top:10px;
							padding-bottom:0px;"/>
			<span style="	font-size:9px;
							color:#FFF;
							position:relative;
							top:15px;">
				STATISTICHE
			</span>
		</div>
		<div	class="grid_1"
				style="text-align:center;">
			<img	id="btn_users"
					src="img/btn_users.gif"
					alt="Utenti"
					style="	position:relative;
							top:10px;"/>
			<span style="	font-size:9px;
							color:#FFF;
							position:relative;
							top:15px;">
				UTENTI
			</span>
		</div>
		<div	class="grid_1"
				style="text-align:center;height:103px;">
			<img 	id="btn_parts"
					src="img/btn_parts.gif"
					alt="Parti"
					style="	position:relative;
							top:10px;"/>
			<span style="	font-size:9px;
							color:#FFF;
							position:relative;
							top:15px;">
				PARTI
			</span>
		</div>
		<div	class="grid_1"
				style="text-align:center;height:103px;">
			<img 	id="btn_movimenti"
					src="img/btn_movimenti.gif"
					alt="Movimenti"
					style="	position:relative;
							top:10px;"/>
			<span style="	font-size:9px;
							color:#FFF;
							position:relative;
							top:15px;">
				MOVIMENTI
			</span>
		</div>
		<div	class="grid_1"
				style="text-align:center;height:103px;">
			<img 	id="btn_magazzini"
					src="img/btn_magazzini.gif"
					alt="Magazzini"
					style="	position:relative;
							top:10px;"/>
			<span style="	font-size:9px;
							color:#FFF;
							position:relative;
							top:15px;">
				MAGAZZINI
			</span>
		</div>
		<div	id="search_box"
				class="grid_4" 
				style="height:103px">
			<img	src="img/search_box.gif"
					style="	position:relative;
							top:23px;"
					alt="search"/>
			<input	type="text"
					size="38"
					style="	position:relative;
							left:30px;
							top:-8px;
							border:0px #FFF solid;"/>
		</div>
		<div	class="clear">&nbsp;</div>
		<div	id="left_frame_1"
				class="grid_3">
			<div	class="frame_title">
				&nbsp;
			</div>
			<div	class="frame_body">
			</div>
		</div>

		<div	id="right_frame_1"
				class="grid_9">
			<div	class="frame_title">
				&nbsp;
			</div>
			<div	class="frame_body">
				&nbsp;
			</div>
		</div>

		<div	id="flexigrid_frame"
				>
		</div>

		<div	id="left_frame_2"
				class="grid_3">
			<div	class="frame_title">
				&nbsp;
			</div>
			<div	class="frame_body">
			</div>
		</div>
	</div>
</body>
</html>
