<?php

	require_once('CDBQuery.php');
	require_once('CDBUser.php');

	class CDBUserAcl extends CDBQuery{

		public static function getSpecificUserACL($userId){
			
			global $db;
			global $EC_CONTROL_PANEL;
			global $EC_CONTROL_PANEL_SUB;
			 
			$MY_AVAILABLE_SERVICE = CDBUser::getUserServiceACL($userId);
			ob_start();
?>			
			
			<ul id="list1">
<?php
			$quick_link_data = array();
			reset($EC_CONTROL_PANEL);
			reset($EC_CONTROL_PANEL_SUB);
			$service_group = 0;
			
			foreach($EC_CONTROL_PANEL as $section => $section_text){
				
				$service_function = 0;
?>
				<li>
					<div class="curved_230_top"></div>
					<div class="curved_230" style="overflow:auto">
						<div class="leftmenu">
							<h3><?php echo $section_text; ?></h3>
<?php
							foreach($EC_CONTROL_PANEL_SUB[$section] as $key => $data){
								
								$value = $service_group . '|' . $service_function;
								if(in_array($key, $MY_AVAILABLE_SERVICE[$section])){
									$checked = "checked='yes'";
								}else{
									$checked = "";
								}
								
								printf(
									'<table cellpadding="0" cellspacing="0">
									<tr><td><input type="checkbox" name="acl_check" id="%s" value="%s" style="" %s /></td>
									<td>%s</td></tr></table>'
									, $key, $value, $checked, $data
								);
								$service_function++;
							}
?>
						</div>
						<div class="clear10"></div>
						<div class="clear10"></div>
					</div>
					<div class="curved_230_bottom"></div>
				</li>
<?php
				$service_group++;
			}
?>
			</ul>
			<div class="clear20"></div>
			<div style="text-align:center">
				<button name="save_acl" id="save_acl" type="button" class="blue" style="width: 90px;">   Save ACL  </button>
			</div>
			<div class="clear10"></div>
			
			<script type="text/javascript">
				$(document).ready(function(){
					
					$("#save_acl").click(function(){
						
						var acl_val = ""; 
						$("input:checkbox[name=acl_check]").each(
							function(){
								if(this.value != ""){

									if(this.checked){
										acl_val += this.value + "|1,";
									}else{
										acl_val += this.value + "|0,";
									}
								}
							}
						);
						
						var url = "opcode=save_user_acl&userid=" + $("select[name=user]").find(":selected").val() + "&acl_val=" + acl_val;
						
						addLoadEvent();
						$.ajax({
							type : "POST",
							url : "ajax/ec_cockpit_set_user_acl.php",
							data : url,
							success : function(response){
								removeLoadEvent();
								$("#fetch_acl_container").html(response);
							}
						});
						return false;
					});
				});
			</script>
<?php
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
		
		public static function setSpecificUserACL($userId, $acl_val){
			
			global $db;
			global $SMITH_SYSTEM_DEF;
			
			foreach($acl_val as $key => $service){
				
				$service_group_function = explode('|', $service);
				
				$db->duplicateOther(
					'smith', 'userServiceAcl',
					array(
						'userId' => $userId, 'serviceGroup' => $service_group_function[0],
						'serviceFunction' => $service_group_function[1], 'active' => $service_group_function[2],
						'createdBy' => $SMITH_SYSTEM_DEF['userId'], 'updatedBy' => $SMITH_SYSTEM_DEF['userId'],
						'dateCreated' => date("Y-m-d H:i:s"), 'dateUpdated' => date("Y-m-d H:i:s")
					),
					array(
						'active' => $service_group_function[2], 'updatedBy' => $SMITH_SYSTEM_DEF['userId'],
						'dateUpdated' => date("Y-m-d H:i:s")
					)
				);
			}
		}
	}