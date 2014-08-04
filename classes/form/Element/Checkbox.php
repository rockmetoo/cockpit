<?php
	class Element_Checkbox extends OptionElement {
		protected $attributes = array("type" => "checkbox");
		protected $maxheight;

		public function render() {
			if(isset($this->attributes["value"])) {
				if(!is_array($this->attributes["value"]))
					$this->attributes["value"] = array($this->attributes["value"]);
			}
			else
				$this->attributes["value"] = array();

			if(substr($this->attributes["name"], -2) != "[]")
				$this->attributes["name"] .= "[]";

			$count = 0;
			foreach($this->options as $value => $text){
				$value = $this->getOptionValue($value);
				echo '<table cellpadding="0" cellspacing="0"><tr><td valign="top"><input id="', $this->attributes["id"], '"', $this->getAttributes(array("id", "value", "checked")), ' value="', $this->filter($value), '"';
				if(in_array($value, $this->attributes["value"]))
					echo ' checked="checked"';
				echo '/></td><td><label for="', $this->attributes["id"], '">', $text, '</label></td></tr></table>';
				++$count;
			}
			if(!empty($this->inline))
				echo '<div style="clear: both;"></div>';
		}
	}
