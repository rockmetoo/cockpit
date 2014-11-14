<?php

	/* File Name: Form.php
	 * Description: This project's namespace structure is leveraged to autoload
	 * requested classes at runtime
	 * Updated By: rockmetoo
	 * */

	function FORM_SPL_LOAD($class){
		$file = dirname(__FILE__) . "/" . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php";
		if(is_file($file))
			include_once $file;
	}
	spl_autoload_register("FORM_SPL_LOAD");

	class Form extends Base{
		private $elements = array();
		private $prefix = "http";
		private $values = array();
		private $widthSuffix = "px";

		protected $ajax;
		protected $ajaxCallback;
		protected $attributes;
		protected $error;
		protected $resourcesPath;
		/*
		 * Prevents various automated from being automatically applied.
		 * Current options for this array included focus, and style
		 * */
		protected $prevent = array();
		protected $view;
		protected $width;

		public function __construct($id = "pfbc", $width = ""){
			$this->configure(array(
				"width" => $width,
				"action" => basename($_SERVER["SCRIPT_NAME"]),
				"id" => preg_replace("/\W/", "-", $id),
				"method" => "post",
				"name" => $id
			));
			if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $this->prefix = "https";

			/*The Standard view class is applied by default and will be used unless a different view is
			specified in the form's configure method*/
			if(empty($this->view)) $this->view = new View_Standard;

			if(empty($this->error)) $this->error = new Error_Standard;

			$path = dirname(__FILE__) . "/Resources";
			if(strpos($path, $_SERVER["DOCUMENT_ROOT"]) !== false)
				$this->resourcesPath = substr($path, strlen($_SERVER["DOCUMENT_ROOT"]));
			else
				$this->resourcesPath = "/PFBC/Resources";
		}

		/*
		 * When a form is serialized and stored in the session,
		 * this function prevents any non-essential information from being included
		 * */
		public function __sleep(){
			return array("attributes", "elements", "error");
		}

		public function addElement(Element $element){
			$element->setForm($this);
			//If the element doesn't have a specified id, a generic identifier is applied.
			$id = $element->getID();
			if(empty($id)) $element->setID($this->attributes["id"] . "-element-" . sizeof($this->elements));
			$this->elements[] = $element;
			/* For ease-of-use, the form tag's encytype attribute is automatically set if the File element */
			if($element instanceof Element_File) $this->attributes["enctype"] = "multipart/form-data";
	    }

		/*
		 * Values that have been set through the setValues method,
		 * either manually by the developer or after validation errors,
		 * are applied to elements within this method
		 * */
	    private function applyValues(){
	        foreach($this->elements as $element){
	            $name = $element->getName();
	            if(isset($this->values[$name]))
	                $element->setValue($this->values[$name]);
	            elseif(substr($name, -2) == "[]" && isset($this->values[substr($name, 0, -2)]))
	                $element->setValue($this->values[substr($name, 0, -2)]);
	        }
	    }

		/*This method parses the form's width property into a numeric width value and a width suffix - either px or %.
		These values are used by the form's concrete view class.*/
		public function formatWidthProperties(){
			if(!empty($this->width)){
				if(substr($this->width, -1) == "%"){
					$this->width = substr($this->width, 0, -1);
					$this->widthSuffix = "%";
				}
				elseif(substr($this->width, -2) == "px")
					$this->width = substr($this->width, 0, -2);
			}
			else{
				/*If the form's width property is empty, 100% will be assumed.*/
				$this->width = 100;
				$this->widthSuffix = "%";
			}
		}

	    public function getAjax(){
	        return $this->ajax;
	    }

	    public function getElements(){
	        return $this->elements;
	    }

		public function getError(){
			return $this->error;
		}

	    public function getId(){
	        return $this->attributes["id"];
	    }

		public function getPrevent(){
	        return $this->prevent;
	    }

	    public function getResourcesPath(){
	        return $this->resourcesPath;
	    }

		public function getErrors(){
			$errors = array();
			$id = $this->attributes["id"];
			if(!empty($COCKPIT_SYSTEM_DEF["pfbc"][$id]["errors"])) $errors = $COCKPIT_SYSTEM_DEF["pfbc"][$id]["errors"];
			return $errors;
		}

		public function getWidth(){
			return $this->width;
		}

		public function getWidthSuffix(){
			return $this->widthSuffix;
		}

		/*
		 * This method restores the serialized form instance
		 * */
		private static function recover($id){
			if(!empty($COCKPIT_SYSTEM_DEF["pfbc"][$id]["form"]))
				return unserialize($COCKPIT_SYSTEM_DEF["pfbc"][$id]["form"]);
		}

		public function render($returnHTML = false){
			$this->view->setForm($this);
			$this->error->setForm($this);
			$this->formatWidthProperties();
			if($returnHTML) ob_start();
			$this->view->render();
			if($returnHTML){
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
			}
		}

		/*When ajax is used to submit the form's data, validation errors need to be manually sent back to the
		form using json.*/
		public static function renderAjaxErrorResponse($id = "pfbc"){
			$form = self::recover($id);
			$form->error->renderAjaxErrorResponse();
		}

		/*
		 * An associative array is used to pre-populate form elements.
		 * The keys of this array correspond with the element names
		 * */
		public function setValues(array $values){
	        $this->values = array_merge($this->values, $values);
	    }
	}
