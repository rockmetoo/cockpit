<?php
	include_once 'bootstrap.php';
	ob_start();
?>

<div id="main_container">
	<div id="content">
		<div id="header">
	<?php
		if(!$SMITH_SYSTEM_DEF['userId']){ ?>
			<form action="signin.php" method="post" name="index" id="index" target="_self">
		    	<ul class="top_login">
					<div class="right">
						<label for="username"><?php echo $lang->get('username'); ?></label>
				    	<input name="username" type="text" id="username" maxlength="64" />
				    	<label for="password"><?php echo $lang->get('password'); ?></label>
				    	<input name="password" type="password" id="password" maxlength="32" />
			    	</div>
			    	<div class="right">
			    		<div style="float:left; margin-right:60px">
							<label for="keep_sign_in" class="label_checkbox">
								<a href="forgotten_passwd.php"
					        	 title="<?php echo $lang->get('forgotten password'); ?>"><?php echo $lang->get('forgotten password'); ?>
					        	</a>
							</label>
						</div>
						<div class="left">
							<label for="keep_sign_in" class="label_checkbox">
								<input type="checkbox" name="keep_sign_in" id="keep_sign_in" value="1" />
			           			<?php echo $lang->get('keep me signed in info'); ?>
							</label>
						</div>
						<div style="float: right; margin-left: 20px;">
				    		<button name="submit" id="submit" type="submit" class="black">
				    			<?php echo $lang->get('sign in'); ?>
				    		</button>
			    		</div>
					</div>
					<div id="caps_lock" class="top_absolute"></div>
				</ul>
			</form>
	<?php } ?>
			<ul class="header_list1">
				<?php
				if(!$SMITH_SYSTEM_DEF['userId']){ ?>
					<li class="first"><?php echo $lang->get('sign up'); ?>:</li>
					<li><a href="signup.php"><?php echo $lang->get('employer'); ?></a></li>
				<?php }else{ ?>
					<li><?php echo $lang->get('sign in as'); ?>: </li>
					<li>
						<a href="
							<?php
								if($SMITH_SYSTEM_DEF['userId']) echo 'controlPanel.php';
								else echo 'index.php';
							?>"><?php echo $SMITH_SYSTEM_DEF['username']; ?>
						</a>
					</li>
					<li><a href="signout.php"><?php echo $lang->get('sign out'); ?></a></li>
				<?php } ?>
				<li><a href="#"><?php echo $lang->get('support'); ?></a></li>
		    </ul>
		    <div class="clear"></div>
<?php
	$content = ob_get_contents();
	ob_end_clean();
	echo $content;
?>