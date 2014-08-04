<?php
	abstract class View extends Base{
		protected $form;

		public function __construct(array $properties = null){
			$this->configure($properties);
		}

		/* This method encapsulates the various pieces that are included in an element's label */

		/* <div class="form_row_left">
			<label>
				<span><?php echo $lang->get('first_name');?>:</span>
				<input type="text" name="user" />
			</label>
		</div>
		*/
		protected function renderLabel($element){
	        $label = $element->getLabel();
	        $id = $element->getID();
	        $description = $element->getDescription();
	        if(!empty($label) || !empty($description)){
	            if(!empty($label)){
	                echo '<label for="', $id, '">';
	                if($element->isRequired())
	                    echo '<strong>*</strong> ';
	                echo '<span>' , $label, '</span>';
	            }
	            if(!empty($description)) echo '<em>', $description, '</em>';
	        }
	    }

		public function setForm(Form $form){
			$this->form = $form;
		}

		/*jQuery is used to apply css entries to the last element.*/
		public function jQueryDocumentReady(){
			echo 'jQuery("#', $this->form->getId(), ' .pfbc-element:last").css({ "margin-bottom": "0", "padding-bottom": "0", "border-bottom": "none" });';
		}

		public function render() {}

		public function renderJS() {}
	}
