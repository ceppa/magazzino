<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>DHTML Tests</title>

<!-- JAVASCRIPT LIBRARIES -->
<script	type="text/javascript"
		src="js/jquery.ui-1.5b4/jquery-1.2.4b.js"></script>

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
	height:103px;
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
		<div	id="header"
				class="grid_1"
				style="text-align:center;">
			<img	id="btn1"
					src="img/btn_stats.gif"
					alt="Statistiche"
					style="	position:relative;
							top:10px;"/>
		</div>
		<div	id="header"
				class="grid_1"
				style="text-align:center;">
			<img src="img/btn_stats.gif" alt="Statistiche" />
		</div>
		<div	id="header"
				class="grid_1"
				style="text-align:center;">
			<img src="img/btn_stats.gif" alt="Statistiche" />
		</div>
		<div	id="header"
				class="grid_1"
				style="text-align:center;">
			<img src="img/btn_stats.gif" alt="Statistiche" />
		</div>
		<div	id="header"
				class="grid_1"
				style="text-align:center;">
			<img src="img/btn_stats.gif" alt="Statistiche" />
		</div>
		<div	id="header"
				class="grid_1"
				style="text-align:center;">
			<img src="img/btn_stats.gif" alt="Statistiche" />
		</div>
		<div	id="header"
				class="grid_1"
				style="text-align:center;">
			<img src="img/btn_stats.gif" alt="Statistiche" />
		</div>
		<div	id="header"
				class="grid_1"
				style="text-align:center;">
			<img src="img/btn_stats.gif" alt="Statistiche" />
		</div>
	</div>
	<div	class="container_12">
		<div	id="login_div"
				class="grid_4 prefix_4">
			<div	class="window_title">Login</div>
			<div	class="window_body">
				<p>
				<span	style="float:left;">Username</span>
				<input	type="text"
						size="30"
						style="float:right;"/>
				<br/><br/>
				<span	style="float:left;">Password</span>
				<input	type="text"
						size="30"
						style="float:right;"/>
				</p>
				<br/>
				<div	style="text-align:center;">
					<span	class="button">Submit</span>
					<span	class="button">Reset</span>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
