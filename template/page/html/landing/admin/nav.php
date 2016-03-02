<div style="float:right; padding:10px;"><?php echo $lang['ADMIN_LOGGED_IN_AS']; ?>: 
<div class="btn-group">
                <button class="btn" onclick="document.location.href='stores.php'"><?php echo $_SESSION['User']['username']; ?></button>
				<button class="btn" onclick="document.location.href='settings.php'"><i class="icon-cog"></i></button>
				<button class="btn btn-info" onclick="document.location.href='change_password.php'"><?php echo $lang['ADMIN_CHANGE_PASSWORD']; ?></button>
                <button class="btn btn-danger" onclick="document.location.href='logout.php'"><?php echo $lang['ADMIN_LOGOUT']; ?></button>
		  </div>
</div>

<div id="logo"></div>
<div class="clearfix"></div>
<div class="navbar">
<div class="navbar-inner">

<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
	  
<a class="brand" href="../index.php"><?php echo $lang['MENU_STORE_FINDER']; ?></a>
 
<div class="nav-collapse collapse">   
<ul class="nav">

	<li class="divider-vertical"></li>
	<li id="store-list" <?php if (strpos($_SERVER['PHP_SELF'],'stores.php') !== false) { ?>class="active"<?php } ?>><a href="./stores.php"><?php echo $lang['ADMIN_STORE_LIST']; ?></a></li>
	<li class="divider-vertical"></li>
	<li id="add-store" <?php if (strpos($_SERVER['PHP_SELF'],'stores_add.php') !== false) { ?>class="active"<?php } ?>><a href="./stores_add.php"><?php echo $lang['ADMIN_ADD_STORE']; ?></a></li>
	<li class="divider-vertical"></li>
	<li id="import" <?php if (strpos($_SERVER['PHP_SELF'],'import.php') !== false) { ?>class="active"<?php } ?>><a href="./import.php">Import/Export Stores</a></li>
	<li class="divider-vertical"></li>
	<li id="add-store" <?php if (strpos($_SERVER['PHP_SELF'],'categories.php') !== false) { ?>class="active"<?php } ?>><a href="./categories.php"><?php echo $lang['SSF_CATEGORY_LIST']; ?></a></li>
	<li class="divider-vertical"></li>
	<li id="add-store" <?php if (strpos($_SERVER['PHP_SELF'],'category_add.php') !== false) { ?>class="active"<?php } ?>><a href="./category_add.php"><?php echo $lang['SSF_ADD_CATEGORY']; ?></a></li>
	<li class="divider-vertical"></li>
	<li id="user-list" <?php if (strpos($_SERVER['PHP_SELF'],'users.php') !== false) { ?>class="active"<?php } ?>><a href="./users.php"><?php echo $lang['ADMIN_USER_LIST']; ?></a></li>
	<li class="divider-vertical"></li>
	<li id="add-user" <?php if (strpos($_SERVER['PHP_SELF'],'users_add.php') !== false) { ?>class="active"<?php } ?>><a href="./users_add.php"><?php echo $lang['ADMIN_ADD_USER']; ?></a></li>
	
	<li class="divider-vertical"></li>
</ul>
  </div>
  </div>
</div>	