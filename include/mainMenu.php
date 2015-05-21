<ul id="jd_menu" class="jd_menu">
	<li id="jd_parts"><a>Part</a>
		<ul>
			<li id="menu_part_list"><a>List</a></li>
		<?
		if($_SESSION["user_type"]!=3)
		{?>
			<li id="menu_part_new" style="display:none"><a>New</a></li>
			<li id="menu_part_edit" style="display:none"><a>Edit</a></li>
		<?}?>
			<li id="menu_part_search"><a>Search</a></li>
		</ul>
	</li>
	<li id="jd_items"><a>Inventory</a>
		<ul>
			<li id="menu_inventory_list"><a>List</a></li>
			<li id="menu_inventory_search"><a>Search</a></li>
		<?
		if($_SESSION["user_type"]!=3)
		{?>
			<li id="menu_inventory_move" style="display:none"><a>Move</a></li>
			<li id="menu_inventory_edit" style="display:none"><a>Edit</a></li>
		<?}?>
		</ul>
	</li>
	<li id="jd_movements"><a>Movements</a>
		<ul>
			<li id="menu_movements_list"><a>List</a></li>
		<?
		if($_SESSION["user_type"]!=3)
		{?>
			<li id="menu_movements_new"><a>New</a></li>
			<li id="menu_movements_edit" style="display:none"><a>Edit</a></li>
		<?}?>
			<li id="menu_movements_search"><a>Search</a></li>
		</ul>
	</li>
<?
if($_SESSION["user_type"]==1)
{?>
	<li id="jd_users"><a>Users</a>
		<ul>
			<li id="menu_users_list"><a>List</a></li>
			<li id="menu_users_new"><a>New</a></li>
			<li id="menu_users_edit" style="display:none"><a>Edit</a></li>
			<li id="menu_users_search"><a>Search</a></li>
		</ul>
	</li>
	<li id="jd_admin"><a>Admin</a>
		<ul>
			<li id="menu_compatibles"><a>Compatible parts</a></li>
			<li id="menu_merge_parts"><a>Merge parts</a></li>
		</ul>
	</li>
<?}
if($_SESSION["user_type"]<=2)
{?>
	<li id="jd_places"><a>Places</a>
		<ul>
			<li id="menu_places_list"><a>List</a></li>
			<li id="menu_places_new"><a>New</a></li>
			<li id="menu_places_edit" style="display:none"><a>Edit</a></li>
			<li id="menu_places_search"><a>Search</a></li>
		</ul>
	</li>
<?}?>
	<li id="jd_reports"><a>Reports</a>
		<ul>
			<li id="menu_reports_warehouse"><a>warehouse</a></li>
			<li id="menu_reports_movements"><a>movements</a></li>
			<li id="menu_reports_fotografia"><a>fotografia</a></li>
		</ul>
	</li>
	<li id="logoutButton" style="float:right">
		<a href="index.php?func=doLogout">
			<img src="img/stock_exit.png" alt="exit" />
		</a>
	</li>
	<li style="float:right">
		<a href="index.php?func=doLogout">
			<?=$_SESSION["name"]?>
		</a>
	</li>
</ul>
