<?php
	class Element_Radio extends OptionElement {
		protected $attributes = array("type" => "radio");
		protected $inline;
		protected $maxheight;

		public function render(){
			$count = 0;
			$checked = false;
			echo '<div class="form_radio_block">';
			foreach($this->options as $value => $text){
				$value = $this->getOptionValue($value);
				echo '<div class="form_each_radio">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<input id="', $this->attributes["id"], "-", $count, '"', $this->getAttributes(array("id", "value", "checked")), ' value="', $this->filter($value), '"';
				if(isset($this->attributes["value"]) && $this->attributes["value"] == $value)
					echo ' checked="checked"';
				echo '/>
								</td>
								<td>
									<label for="', $this->attributes["id"], "-", $count, '">', $text, '</label>
								</td>
							</tr>
						</table>
					</div>';
				++$count;
			}
			echo '</div>';
			if(!empty($this->inline))
				echo '<div style="clear: both;"></div>';
		}
	}
