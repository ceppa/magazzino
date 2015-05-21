<?php
if($isLogged == false)
{
?>
<div	id="header"
		class="grid_12"
		style="text-align:center;">
	<img src="img/header_efa.png" alt="efa" />
</div>
<?php
}
elseif(		$isLogged == true
	&&	$_SESSION['user_type']=='1'	)
{
?>
<div	id="header"
		class="grid_3"
		style="text-align:center;">
	<img src="img/header_efa.png" alt="efa" />
</div>
<div	class="grid_1"
		style="text-align:center;height:103px;">
	<img	id="btn_stats"
			src="img/btn_stats.gif"
			alt="Reports"
			style="	position:relative;
					top:10px;
					padding-bottom:0px;"/>
	<span style="	font-size:9px;
					color:#FFF;
					position:relative;
					top:15px;">
		REPORTS
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
		USERS
	</span>
</div>
<div	class="grid_1"
		style="text-align:center;height:103px;">
	<img 	id="btn_parts"
			src="img/btn_parts.gif"
			alt="Parts"
			style="	position:relative;
					top:10px;"/>
	<span style="	font-size:9px;
					color:#FFF;
					position:relative;
					top:15px;">
		PARTS
	</span>
</div>
<div	class="grid_1"
		style="text-align:center;height:103px;">
	<img 	id="btn_items"
			src="img/btn_items.gif"
			alt="Items"
			style="	position:relative;
					top:10px;"/>
	<span style="	font-size:9px;
					color:#FFF;
					position:relative;
					top:15px;">
		ITEMS
	</span>
</div>
<div	class="grid_1"
		style="text-align:center;height:103px;">
	<img 	id="btn_movimenti"
			src="img/btn_movimenti.gif"
			alt="Movements"
			style="	position:relative;
					top:10px;"/>
	<span style="	font-size:9px;
					color:#FFF;
					position:relative;
					top:15px;">
		MOVEMENTS
	</span>
</div>
<div	class="grid_1"
		style="text-align:center;height:103px;">
	<img 	id="btn_magazzini"
			src="img/btn_magazzini.gif"
			alt="Warehouses"
			style="	position:relative;
					top:10px;"/>
	<span style="	font-size:9px;
					color:#FFF;
					position:relative;
					top:15px;">
		WAREHOUSES
	</span>
</div>
<div	id="search_box"
		class="grid_3" 
		style="height:103px">
	<img	src="img/search_box3.gif"
			style="	position:relative;
					top:23px;"
			alt="search"/>
	<input	type="text"
			size="15"
			style="	position:relative;
					left:30px;
					top:-25px;
					border:0px #FFF solid;"/>
	<img	id="btn_logout"
			src="img/btn_logout.gif"
			style="	position:relative;
					left:50px;
					top:-15px;"/>
</div>
<?php
}?>
