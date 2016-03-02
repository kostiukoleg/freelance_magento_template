<?php

// validate request store add

function validate_request_add_store(){

global $lang;
global $fields;
global $images;
global $db;
global $errors;

	$fields = array(
		'name'=>array(
			'rule'=>'/.+/',
			'message'=>$lang['ADMIN_STORE_NAME_VALIDATE'],
			'value'=>'',
			'required'=>TRUE
		),
		'address'=>array(
			'rule'=>'/.+/',
			'message'=>$lang['ADMIN_STORE_ADDRESS_VALIDATE'],
			'value'=>'',
			'required'=>TRUE
		),
		'telephone'=>array(
			'rule'=>'/[0-9 +]/',
			'message'=>$lang['ADMIN_STORE_TELEPHONE_VALIDATE'],
			'value'=>'',
			'required'=>FALSE
		),
		'email'=>array(
			'rule'=>"/^([a-z0-9\+_\-']+)(\.[a-z0-9\+_\-']+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix",
			'message'=>$lang['ADMIN_STORE_EMAIL_VALIDATE'],
			'value'=>'',
			'required'=>FALSE
		),
		'website'=>array(
			'rule'=>'/.+/',
			'message'=>$lang['ADMIN_STORE_WEBSITE_VALIDATE'],
			'value'=>'',
			'required'=>FALSE
		),
		'embed_video'=>array(
			'rule'=>'/.+/',
			'message'=>'Please enter a valid embed code',
			'value'=>'',
			'required'=>FALSE
		),
		'default_media'=>array(
			'rule'=>'/.+/',
			'message'=>'Please select a default media',
			'value'=>'',
			'required'=>FALSE
		),
		'description'=>array(
			'rule'=>'/.+/',
			'message'=>$lang['ADMIN_STORE_DESCRIPTION_VALIDATE'],
			'value'=>'',
			'required'=>FALSE
		),
		'latitude'=>array(
			'rule'=>'/[0-9.\-]/',
			'message'=>$lang['ADMIN_STORE_LATITUDE_VALIDATE'],
			'value'=>'',
			'required'=>TRUE
		),
		'longitude'=>array(
			'rule'=>'/[0-9.\-]/',
			'message'=>$lang['ADMIN_STORE_LONGITUDE_VALIDATE'],
			'value'=>'',
			'required'=>TRUE
		)
	);



	$session_id = session_id();

	$tmp_upload_folder = ROOT.'temp_upload/'.$session_id.'/';

	$resize_image_width = 100;



	if(isset($_POST['delete_image'])) {
		
		$delete = array_keys($_POST['delete_image']);
		$image = $delete[0];
		
		if(file_exists($tmp_upload_folder.$image)) {
			
			if(!@unlink($tmp_upload_folder.$image)) {
				$errors = $lang['ADMIN_STORE_IMAGE_DELETE_FAILED'].$v;
			}
		}
	}


	if($_POST) {

		$errors = array();
		foreach($fields as $k=>$v) {
		
			if(isset($_POST[$k])) {
			
				$required = (isset($v['required'])) ? (!empty($_POST[$k])) ? TRUE : $v['required']  : TRUE ;
				
				if(isset($v['rule']) && $required && !preg_match($v['rule'], $_POST[$k]) ) {
				
					if(isset($v['rule']) && !preg_match($v['rule'], $_POST[$k]) ) {
					
						if(isset($v['message']) && !empty($v['message'])) {
							$errors[] = $v['message'];
						}
					}
				}
			$fields[$k]['value'] = $_POST[$k];
			}
		}
		
		
		if($_FILES && $_FILES['file']['error'] != 4) {
			
			$allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');

			
			if(!in_array($_FILES['file']['type'],$allowed_mimetypes)) {
				$errors[] = $lang['ADMIN_STORE_ALLOWED_IMAGE'];
			} else {
				
				create_dir($tmp_upload_folder);

			
				$img  = new Image(array('filename'=>$_FILES['file']['tmp_name']));
				
				if($img !== FALSE) {
					
					if($img->resize_to_width($resize_image_width)) {
						
						$safe_name = strtolower(str_replace(' ','_',preg_replace('/[^a-zA-Z0-9\-_. ]/','',$_FILES['file']['name'])));

						
						if(!$img->save($tmp_upload_folder.$safe_name)) {
							$errors[] = $lang['ADMIN_STORE_THUMB_FAILED'];
						}
					} else {
						$errors[] = $lang['ADMIN_STORE_THUMB_FAILED'];
					}
				} else {
					$errors[] = $lang['ADMIN_STORE_IMAGE_FAILED'];
				}
			}
		}
		
		if(empty($errors)) {
			mysql_query("SET NAMES utf8"); 
			if (!get_magic_quotes_gpc()) { 
			 $_POST['name'] = addslashes($_POST['name']);
			 $_POST['address'] = addslashes($_POST['address']);
			 $_POST['description'] = addslashes($_POST['description']);
			}
		
			if(!$db->insert('stores',$_POST)) {
				$errors[] = $lang['ADMIN_STORE_SAVE_FAILED'];
			} else {
				
				$insert_id = $db->get_insert_id();
				
				
				if(is_dir($tmp_upload_folder)) {
					$files = get_files($tmp_upload_folder);
					if(!empty($files)) {
						
						if(create_dir(ROOT.'admin/imgs/stores/'.$insert_id)) {
							
							foreach($files as $k=>$v) {
								if(@copy(ROOT.'temp_upload/'.$session_id.'/'.$v,ROOT.'admin/imgs/stores/'.$insert_id.'/'.$v)) {
									@unlink(ROOT.'temp_upload/'.$session_id.'/'.$v);
								}
							}
						}
						
						@unlink(ROOT.'temp_upload/'.$session_id.'/');
					}
				}

				
				mail(ADMINISTRATOR_EMAIL,$lang['ADD_STORE_REQUEST_EMAIL_TITLE'],$lang['ADD_STORE_REQUEST_EMAIL_BODY'],"From: no-reply@gmail.com");
				
				
				$_SESSION['notification'] = array('type'=>'good','msg'=>$lang['REQUEST_STORE_SAVED']);
				//redirect(ROOT_URL.'index.php');
			}
		}
	}
	
	$images = array();
	if(is_dir($tmp_upload_folder)) {
		$images = get_files($tmp_upload_folder);
		foreach($images as $k=>$v) {
			$images[$k] = ROOT_URL.'temp_upload/'.$session_id.'/'.$v;
		}
	}
}

?>