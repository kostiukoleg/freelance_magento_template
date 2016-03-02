<?php
// include common file
include_once "./includes/config.inc.php";

/* To create a new user in the database, add a username example called newuser, if you want to use 123456 as password all you need to do is run this php script

echo md5( '123456'.SALT );

The output should be f0971de887e9150eb74dbd4786d0f80d

Now you can login with 

Username: newuser
Password: 123456

*/

// form has been submitted
if($_POST) {
	// check user is valid
	if(!check_user($_POST['username'],$_POST['password'])) {
		$error = 'Invalid authentication details';
	} else {
		
		redirect(ROOT_URL.'stores.php');
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title><?php echo $lang['ADMIN_TITLE']; ?></title>
	<?php include 'header.php'; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body id="login">
	<div id="wrapper">
	

	<form method="post" action="" id="form_login">
	<div class="hero-unit">
              <h1><?php echo $lang['STORE_FINDER']; ?></h1>
              <p><?php echo $lang['ADMIN_LOGIN_TAGLINE']; ?></p>

            </div>
          
	<?php echo notification(); ?>
	<?php if(isset($error)): ?>
	<p class="alert alert-block alert-error fade in"><?php echo $error; ?></p>
	<?php endif; ?>
		  
        <div id="main">
		<fieldset>
			<legend><?php echo $lang['ADMIN_LOGIN']; ?></legend>

				<label><?php echo $lang['ADMIN_USERNAME']; ?>: <span class='required'>*</span></label>
				<input type="text" name='username'id='username' value='admin' />

				<label><?php echo $lang['ADMIN_PASSWORD']; ?>: <span class='required'>*</span></label>
				<input type="password" name='password'id='password' value='password' />

			<div class='input buttons'>
				<button type="button" onclick="document.location.href='../index.php'" name='btn_login' class="btn btn-primary" id='btn_login'>&laquo; <?php echo $lang['ADMIN_BACK_TO_FRONTEND']; ?></button> <button type="submit" name='btn_login' class="btn btn-primary" id='btn_login'><?php echo $lang['ADMIN_LOGIN']; ?></button>
			</div>
		</fieldset>
		</div>
	</form>
	
          
	</div>
<?php include '../themes/footer.inc.php'; ?>	
</body>
</html>