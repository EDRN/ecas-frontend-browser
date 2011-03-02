<?php
class MetadataDisplayWidget
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {
	
	public $metadata;
	
	public function __construct($options = array()) {
		
	}
	
	public function loadMetadata($metadata) {
		$this->metadata = $metadata;
	}
	
	
	public function render($bEcho = true) {
		$str  = "<table class=\"metwidget\"><tbody>";
		$str .= $this->renderHelper($this->metadata);
		$str .= "</tbody></table>";
		if ($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
	
	protected function renderHelper($metadata) {
		foreach ($metadata as $key => $values) {
			// Build nested metadata tables recursively
			$r .= "<tr><th>{$key}</th>";
			// Associative array means met contains subkeys
			if (is_array($values) && self::is_assoc($values)) {
				$r .= "<td>";
				$r .= "<table class=\"metwidget multivalue\"><tbody>";
				$r .= $this->renderHelper($values);
				$r .= "</tbody></table>";
			} 
			// Numeric array means met has multiple values
			else if (is_array($values)) {
				$r .= "<td>";
				$r .= "<table class=\"metwidget\"><tbody>";
				foreach ($values as $val) {
					if (is_array($val) && self::is_assoc($val)) {
						$r .= "<tr class=\"multivalue\"><td>";
						$r .= "<table class=\"metwidget\">";
						$r .= $this->renderHelper($val);
						$r .= "</table>";
					} else {
						$r .= "<tr><td class=\"value\">";
						$r .= "<div>{$val}</div>";
					}
					$r .= "</td></tr>";
				}
				$r .= "</tbody></table>";
			} 
			// Scalar value means met has one value 
			else {
				$r .= "<td class=\"value\">";
				$r .= "<div>{$values}</div>";
			}
			$r .= "</td></tr>";
		}
		return $r;
	}
	
	protected static function is_assoc($array) {
    	return (is_array($array) && 
    		(count($array)==0 || 
    			0 !== count(array_diff_key($array, array_keys(array_keys($array))) )));
	}
}