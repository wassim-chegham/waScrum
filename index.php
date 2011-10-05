<?php
	
	ob_start('ob_gzhandler');
	
	require_once('config/config.php');
	require_once('config/lang.php');
	require_once('includes/global.fn.php');
  	require_once('includes/Session.class.php');
	require_once('do.i8ln.php');
	
	if( $session->ok() == false )
	{
		redirect('do.login.php');
	}

	
?>
<!DOCTYPE html> 
<html lang="en" > 
	<head> 
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
		<title><?php echo $config['title']; ?> v1-alpha</title> 
		<link rel="stylesheet" type="text/css" href="assets/css/wascrum.css" media="screen" />
		
		<script>
		<?php 
			
		$js = array(
		  	'MSG_1'=>$lang['js_msg_1'],
		  	'MSG_2'=>$lang['js_msg_2'],
		  	'MSG_3'=>$lang['js_msg_3'],
		  	'MSG_4'=>$lang['js_msg_4'],
		  	'MSG_5'=>$lang['js_msg_5'],
			'MSG_6'=>$lang['js_msg_6'],
			'MSG_7'=>$lang['js_msg_7'],
			'MSG_8'=>$lang['js_msg_8'],
			'MSG_9'=>$lang['js_msg_9'],
      		'MSG_10'=>$lang['js_msg_10'],
      		'MSG_11'=>$lang['js_msg_11'],
      		'MSG_12'=>$lang['js_msg_12'],
      		'MSG_13'=>$lang['js_msg_13']
		);
		
		echo "// i8ln\n";
		echo "var LANG = {";
		foreach( $js as $k=>$v)
		{
		  echo '"'.$k.'":"'.$v.'",';
		}
		echo "'null':null};"
		?>
		</script>
	</head> 
	<body>
	
		<header>
			<h1 id="big-title"><?php echo sprintf($lang['site_welcome'], $GLOBALS['config']['title']); ?></h1>
			<div id="profile">
				<h4><span></span></h4>
				<h3>
				  <?php echo $lang['profile_welcome']; ?>, <b><?php echo ucwords($session->get('login')); ?></b> 
					<?php 
						$a = $session->get('last_access');
						if( $a = false )
							echo sprintf($lang['profile_last_login'], $session->get('last_access')); 
					?>
					( <a href="do.logout.php"><?php echo $lang['profile_logout']; ?></a> )
			    <div id="i8ln">
			     <span><?php echo $lang['site_select_lang']; ?> &#187;</span>
			     <?php foreach($GLOBALS['config']['available_lang'] as $k=>$lbl) echo "<a title='".ucfirst($lbl)."' href='?".$k."'>".ucfirst($lbl)."</a>&nbsp;"; ?>
			    </div>
			  </h3>
			</div>
			<div id="menu">
				<ul>
					<li><input id="btn-new-story" type="button" name="btn-new-story" value="<?php echo $lang['form_story_new']; ?>"></li>
					<li><input id="btn-edit-story" type="button" disabled="disabled" name="btn-edit-story" value="<?php echo $lang['form_story_edit']; ?>"></li>
					<li><input id="btn-delete-story" type="button" disabled="disabled" name="btn-delete-story" value="<?php echo $lang['form_story_delete']; ?>"></li>
					<li>&nbsp;&nbsp;</li>
					<li><input id="btn-new-task" type="button" disabled="disabled" name="btn-new-task" value="<?php echo $lang['form_task_new']; ?>" /></li>
					<li><input id="btn-edit-task" type="button" disabled="disabled" name="btn-edit-task" value="<?php echo $lang['form_task_edit']; ?>" /></li>
					<li><input id="btn-delete-task" type="button" disabled="disabled" name="btn-delete-task" value="<?php echo $lang['form_task_delete']; ?>" /></li>
					<li>&nbsp;&nbsp;</li>
					<li><select id="btn-toggle-users" name"btn-toggle-users">
						<option value="all"><?php echo $lang['form_select_users_all']; ?></option>
						<option value="<?php echo strtolower($session->get('login')); ?>"><?php echo $lang['form_select_users_me']; ?></option>
					</select></li>
					<li><input id="btn-save-board" type="button" name="btn-save-board" value="<?php echo $lang['form_board_save']; ?>"></li>
					<li><input id="btn-clear-board" type="button" name="btn-clear-board" value="<?php echo $lang['form_board_clear']; ?>"></li>
					<li>&nbsp;&nbsp;</li>
					<li><input id="btn-about" type="button" name="btn-about" value="<?php echo $lang['form_board_about']; ?>"></li>
				</ul>
			</div>
		</header>
		<br />
		<div id="main" >

			<div id="board" >
				<ul>
					<li>
						<h2><?php echo $lang['form_board_column_story']; ?></h2>
						<br />
					</li>
					<li>
						<h2><?php echo $lang['form_board_column_todo']; ?></h2>
						<br />
					</li>
					<li>
						<h2><?php echo $lang['form_board_column_inprogress']; ?></h2>
						<br />
					</li>
					<li>
						<h2><?php echo $lang['form_board_column_toverify']; ?></h2>
						<br />
					</li>
					<li>
						<h2><?php echo $lang['form_board_column_done']; ?></h2>
						<br />
					</li>
				</ul>
				<br style="clear:both"/>
			</div>
			
		</div>
		
		</footer>
			<p id="credits"><a href="http://cheghamwassim.com"><?php echo sprintf($lang['site_copyrights'], date('Y')); ?></a></p>
		</footer>
		
		<script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="assets/js/wascrum.js"></script>
		
		<div id="modal" class="display-none"></div>
		<!-- stories -->
		<!-- create -->
		<div class="dialog display-none" id="new-story-dialog">
			<form>
				<h1><?php echo $lang['dialog_story_new']; ?></h1>
				<p>
					<label for="story-description"><?php echo $lang['dialog_story_description']; ?></label>
					<textarea cols="30" rows="10" name="story-description" id="story-description" ><?php echo $lang['dialog_story_textarea_value']; ?></textarea>
				</p>
				<input type="hidden" id="story-author" name="story-author" value="<?php echo $session->get('login'); ?>"/>
				<input id="create-story" type="button" name="create-story" value="<?php echo $lang['dialog_btn_create']; ?>"/>
				<input class="close-dialog" type="button" name="close-dialog" value="<?php echo $lang['dialog_btn_close']; ?>"/>
			</form>
		</div>
		<!-- edit -->
		<div class="dialog display-none" id="edit-story-dialog">
			<form>
				<h1><?php echo $lang['dialog_story_edit']; ?></h1>
				<p>
					<label for="story-description"><?php echo $lang['dialog_story_description']; ?></label>
					<textarea cols="30" rows="10" name="story-description" id="story-description" ></textarea>
				</p>
				<input id="edit-story" type="button" name="edit-story" value="<?php echo $lang['dialog_btn_update']; ?>"/>
				<input class="close-dialog" type="button" name="close-dialog" value="<?php echo $lang['dialog_btn_close']; ?>"/>
			</form>
		</div>
		<!-- delete -->
		<div class="dialog display-none" id="delete-story-dialog">
			<form>
				<h1><?php echo $lang['dialog_story_delete']; ?></h1>
				<p>
					<?php echo $lang['dialog_story_delete_text']; ?>
				</p>
				<input id="delete-story" type="button" name="delete-story" value="<?php echo $lang['form_value_yes']; ?>"/>
				<input class="close-dialog" type="button" name="close-dialog" value="<?php echo $lang['form_value_no']; ?>"/>
			</form>
		</div>
		<!-- tasks -->
		<!-- create -->
		<div class="dialog display-none" id="new-task-dialog">
			<form>
				<h1><?php echo $lang['dialog_task_new']; ?></h1>
				<p>
					<label for="task-description"><?php echo $lang['dialog_task_description']; ?></label>
					<textarea cols="30" rows="10" name="task-description" id="task-description" ><?php echo $lang['dialog_task_textarea_value']; ?></textarea>
				</p>
				<p>
					<label for="estimated-points"><?php echo $lang['dialog_task_estimated']; ?></label>
					<select name="estimated-points" id="estimated-points">
					<?php $f=fibonacci(); foreach($f as $i): ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php endforeach; ?>
					</select>
				</p>
				<input type="hidden" id="task-author" name="task-author" value="<?php echo $session->get('login'); ?>" />
				<input id="add-task" type="button" name="add-task" value="<?php echo $lang['dialog_btn_add']; ?>" />
				<input class="close-dialog" type="button" name="close-dialog" value="<?php echo $lang['dialog_btn_close']; ?>" />
			</form>
		</div>
		<!-- edit -->
		<div class="dialog display-none" id="edit-task-dialog">
			<form>
				<h1><?php echo $lang['dialog_task_edit']; ?></h1>
				<p>
					<label for="task-description"><?php echo $lang['dialog_task_description']; ?></label>
					<textarea cols="30" rows="10" name="task-description" id="task-description" ></textarea>
				</p>
				<p>
					<label for="estimated-points"><?php echo $lang['dialog_task_estimated']; ?></label>
					<select name="estimated-points" id="estimated-points">
						<?php foreach($f as $i): ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<input id="edit-task" type="button" name="edit-task" value="<?php echo $lang['dialog_btn_update']; ?>"/>
				<input class="close-dialog" type="button" name="close-dialog" value="<?php echo $lang['dialog_btn_close']; ?>"/>
			</form>
		</div>
		<!-- delete -->
		<div class="dialog display-none" id="delete-task-dialog">
			<form>
				<h1><?php echo $lang['dialog_task_delete']; ?></h1>
				<p>
					<?php echo $lang['dialog_task_delete_text']; ?>
				</p>
				<input id="delete-task" type="button" name="delete-task" value="<?php echo $lang['form_value_yes']; ?>"/>
				<input class="close-dialog" type="button" name="close-dialog" value="<?php echo $lang['form_value_no']; ?>"/>
			</form>
		</div>
		<!-- -->
		<!-- Clear board -->
		<div class="dialog display-none" id="clear-board-dialog">
			<form>
				<h1><?php echo $lang['dialog_board_clear']; ?></h1>
				<p>
					<?php echo $lang['dialog_board_clear_text']; ?>
				</p>
				<input id="clear-board" type="button" name="clear-board" value="<?php echo $lang['form_value_yes']; ?>"/>
				<input class="close-dialog" type="button" name="close-dialog" value="<?php echo $lang['form_value_no']; ?>"/>
			</form>
		</div>
		<!-- -->
		<div class="dialog display-none" id="about-dialog">
			<form>
				<p>
					<?php echo $lang['dialog_about']; ?>
				</p>
				<hr/>
				<input type="button" name="close-dialog" id="close-dialog" class="close-dialog" value="<?php echo $lang['dialog_btn_close']; ?>">
			</form>
		</div>
		<div id="b"></div>
	</body>
</html>
