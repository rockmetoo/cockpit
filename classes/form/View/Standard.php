<?php
	class View_Standard extends View{
		public function render(){
			echo '<form', $this->form->getAttributes(), '>';
			$this->form->getError()->render();

			$elements = $this->form->getElements();
			$elementSize = sizeof($elements);
			for($e = 0; $e < $elementSize; ++$e){
				$element = $elements[$e];
				if($element instanceof Element_Hidden || $element instanceof Element_HTMLExternal)
	                $element->render();
	            elseif($element instanceof Element_Button){
	                if($e == 0 || !$elements[($e - 1)] instanceof Element_Button)
	                    echo '<div class="form_row_' . $element->getElemPos() . '">';
	                $element->render();
	                if(($e + 1) == $elementSize || !$elements[($e + 1)] instanceof Element_Button){
	                    echo '</label>';
	                    $errorul = $element->getErrorUl();
						if(!empty($errorul)){
							echo $errorul;
						}
						echo '</div>';
	                }
	            }else{
					echo '<div class="form_row_' . $element->getElemPos() . '">';
					$this->renderLabel($element);
					$element->render();
					//View.php line 28
					$label = $element->getLabel();
					if(!empty($label)){
						echo '</label>';
					}
					$errorul = $element->getErrorUl();
					if(!empty($errorul)){
						echo $errorul;
					}
					echo '</div>';
				}
			}
			echo '</form>';
	    }
	}
