<?php

	class CUserMenu
	{
		public function __construct(){}

		public static function topMenuPanel($lang, $selected = 1)
		{
			ob_start();
?>
			<ul class="top_menupanel_menu">
				<li><a title="" href="index.php" class="borders<?php echo ($selected==1) ? " selected": ""; ?>">Home</a></li>
				<li><a href="controlPanel.php"
					class="borders<?php echo ($selected==2) ? " selected": ""; ?>"><?php echo "SPAM DASHBOARD"; ?></a>
				</li>
				<li>
					<a href="machineDataControlPanel.php" class="borders<?php echo ($selected==3) ? " selected": ""; ?>"><?php echo "MACHINE DATA"; ?>
					<br/><font color="red" size="1" style="text-transform:lowercase">(Seeking Investment)</font></a>
				</li>
				<li>
					<a href="logDataControlPanel.php" class="marginright<?php echo ($selected==4) ? " selected": ""; ?>"><?php echo "LOG DATA"; ?>
					<br/><font color="red" size="1" style="text-transform:lowercase">(Seeking Investment)</font></a>
				</li>
			</ul>
<?php
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
