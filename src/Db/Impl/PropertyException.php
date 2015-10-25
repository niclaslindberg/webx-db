<?php 
namespace WebX\Db\Impl;

class PropertyException extends \Exception {

	private $property;

	public function __construct($property, $message=null) {
		parent::__construct(sprintf("Missing property %s (%m)",$property,$message));
		$this->property = $property;
	}

	public function getProperty() {
		return $this->property;
	}

}