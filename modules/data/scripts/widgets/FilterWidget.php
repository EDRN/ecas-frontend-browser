<?php
class FilterWidget 
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {

	// The ProductType that the widget is filtering.  This must be set before calling
	// render() or renderScript().
	public $productType;
	
	// The id of the HTMLElement that will be modified by filter results.  This must be set before
	// calling renderScript().
	public $htmlID;
	
	// The url of the site base
	public $siteUrl;
	
	// Whether filtered results will be displayed all at once or in a paged format
	public $pagedResults;
	
	// Will results be returned in html or json
	public $resultFormat;

	public function __construct($options = array()){
		if(isset($options['productType'])){
			$this->productType = $options['productType'];
		}
		if(isset($options['htmlID'])){
			$this->htmlID = $options['htmlID'];
		}
		if(isset($options['siteUrl'])){
			$this->siteUrl = $options['siteUrl'];
		}
		if(isset($options['pagedResults'])){
			$this->pagedResults = $options['pagedResults'];
		}
		if(isset($options['resultFormat'])){
			$this->resultFormat = $options['resultFormat'];
		}
		
	}
	
	public function setProductType($productType){
		$this->productType = $productType;
	}
	
	public function setHtmlId($htmlID){
		$this->htmlID = $htmlID;
	}
	
	public function setSiteUrl($siteUrl){
		$this->siteUrl = $siteUrl;
	}
	
	public function setPagedResults($pagedResults){
		$this->pagedResults = $pagedResults;
	}
	
	public function setResultFormat($resultFormat){
		$this->resultFormat = resultFormat;
	}
	
	public function render($bEcho = true){
		$str = '';
		$str .= '<select id="filterKey">';
		$filterKeys = Utils::getMetadataElements(array($this->productType));
		natcasesort($filterKeys);
		foreach($filterKeys as $label){
			$str .= '<option value="' . $label . '">' . $label . '</option>';
		}
		$str .= '</select>&nbsp;=&nbsp;';
		$str .= '<input type="text" id="filterValue" size="18" alt="filterValue">&nbsp;';
		$str .= '<input type="button" value="Add" onclick="addFilter()" />';
		$str .= '<table id="filters"></table>';
		
		if ($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
	
	public function renderScript($bEcho = true){
		$str = '';
		$str .= '<script type="text/javascript">var htmlID = "' . $this->htmlID . '";</script>';
		$str .= '<script type="text/javascript">var ptName = "' . $this->productType->getName() . '";</script>';
		$str .= '<script type="text/javascript">var siteUrl = "' . $this->siteUrl . '";</script>';
		$str .= '<script type="text/javascript">var resultFormat = "' . $this->resultFormat . '";</script>';
		$str .= '<script type="text/javascript" src="' . MODULE_STATIC . '/js/querywidget.js"></script>';
		$str .= '<script type="text/javascript" src="' . MODULE_STATIC . '/js/filterwidget.js"></script>';
	
		if ($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
}
?>