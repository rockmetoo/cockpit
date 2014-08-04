<?php
abstract class OptionElement extends Element{
	protected $options;

	public function __construct(
		$label, $name, $position = "left", array $options, array $properties = null, $errorul = ''
	){
		$this->options = $options;
		if(!is_array($this->options)) $this->options = array($this->options);

		/*if(array_values($this->options) === $this->options)
			$this->options = array_combine($this->options, $this->options);
		*/
		parent::__construct($label, $name, $position, $properties, $errorul);
	}

	protected function getOptionValue($value){
        $position = strpos($value, ":pfbc");
        if($position !== false){
            if($position == 0)
                $value = "";
            else
                $value = substr($value, 0, $position);
        }
        return $value;
    }
}
