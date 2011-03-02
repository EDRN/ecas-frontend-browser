<?php
class BasicSearchWidget 
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {
	
	public function __construct($options = array()){}
	
	public function render($bEcho = true){
		$str = '';
		$str .= '<form action="' . MODULE_ROOT . '/queryScript.do" method="POST">';
		$str .= '<input type="hidden" name="Types[0]" value="*"/>';
		$str .= '<input type="hidden" name="Criteria[0][CriteriaType]" value="Term"/>';
		$str .= '<input type="hidden" name="Criteria[0][ElementName]" value="*"/>';
		$str .= '<input type="text" name ="Criteria[0][Value]"/>';
		$str .= '<input type="submit" value="Search"/>';
		$str .= '</form>';
		
		if ($bEcho) {
			echo $str;
		} else {
			return $st;
		}
	}
}
?>