<?php
    require_once('bootstrap.php');
	header('HTTP/1.1 404 Not Found', true, 404);
	require_once('CDBSession.php');
	$SMITH_SYSTEM_DEF['section'] = "home";
	require_once('CHelperFunctions.php');
	require_once('CLocalization.php');
	$lang = new CLocalization($SMITH_SYSTEM_DEF['lang'], '404.php');
	require_once('includes/form_values_'.$SMITH_SYSTEM_DEF['lang'].'.php');

	$title = "404 Page Not Found Error";
	global $mobile_agent;
	if($mobile_agent->isNonMobile()){
		$js_files = array('ui.dropdownchecklist.js', 'jquery.cookie.js', 'jquery.currency.js', 'AC_RunActiveContent.js');
		$js_string =
			'$(document).ready(function(){
			$("#category_search").dropdownchecklist("destroy");
			$("#category_search").dropdownchecklist({ firstItemChecksAll: true, width: 132, textFormatFunction: function(options){
				var selectedOptions = options.filter(":selected");
				var countOfSelected = selectedOptions.size();
				switch(countOfSelected) {
					case 0: return "Select Project";
					case 1: return selectedOptions.text();
					case options.size(): return "(Select All)";
					default: return countOfSelected + " Categories";
				}
			}});
			$("#project_groups").dropdownchecklist("destroy");
			$("#project_groups").dropdownchecklist({ firstItemChecksAll: true, width: 132, textFormatFunction: function(options){
				var selectedOptions = options.filter(":selected");
				var countOfSelected = selectedOptions.size();
				switch(countOfSelected) {
					case 0: return "Select Winner Skills";
					case 1: return selectedOptions.text();
					case options.size(): return "(Select All)";
					default: return countOfSelected + " Categories";
				}
			}});
		});
		';
		$css_files = array('style.css' => 'all', 'ui.dropdownchecklist.css' => 'all', 'currency.css' => 'all');
		require_once('header.php');
		include('dcontents/indexHeader.php');
?>
			<div class="top_menupanel">
				<div id="menupanel">
					<ul class="top_menupanel_project_search">
						<li>
				      		<form action="project_search.php" method="post" name="project_search" id="project_search" target="_self">
								<div style="float:left;margin-top:2px">
									<input type="text" name="search_text" id="search_text" size="27" maxlength="2048" style="margin:1px;" />
								</div>
								<div style="float:right;margin-top:4px">
									<select name="category_search" id="category_search" multiple="multiple">
										<option value=""><?php echo $lang->get('any project group'); ?></option>
						                <?php
							                reset($project_groups);
							            	unset($project_groups[1800], $project_groups[2000], $project_groups[2400]);
							                echo CHelperFunctions::populateSelect($project_groups, $_REQUEST['category_search']);
						                ?>
									</select>
						    		<button name="submit" id="submit" type="submit" class="black">
						    			Project
						    		</button>
								</div>
				      		</form>
			      		</li>
					</ul>
					<ul class="top_menupanel_menu">
						<li><a href="index.php" class="borders">Home</a></li>
						<li>
						<?php
							echo '<a href="controlPanel.php" class="borders">My Dashboard</a>';
						?>
						</li>
						<li><a href="my_winner_network.php" class="borders">Winners</a></li>
						<li><a href="my_project_search.php" class="borders">Projects</a></li>
						<li><a href="my_fame.php" class="borders">Fame</a></li>
						<li><a href="tech_research_trends.php" class="marginright">Trends</a></li>
					</ul>
					<div class="clear"></div>
					<div class="top_menupanel_adv_search">
						<a title="ocrm project search" href="adv_project_search.php">
							Advanced Search
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="clear10"></div>
		<div class="form_full_content">
			<div class="content_holder">
				<div class="form">
		    		<h2 class="h2_title"><?php echo $lang->get('404 head title'); ?></h2>
		    		<h2><?php echo $lang->get('404 not found'); ?></h2>
		    	</div>
			</div>
		</div>
	<?php
		require_once('footer.php');
	}else{ //show mobile error
		require_once('mobile_header.php');
	?>
		<h1><?php echo $lang->get('[mobile]page not found'); ?></h1>
	<?php
		require_once('mobile_footer.php');
	}