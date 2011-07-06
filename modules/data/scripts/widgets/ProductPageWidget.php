<?php
/**
 * Display a page of product results.
 * @author ahart
 *
 */
class ProductPageWidget 
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {
	
	public $page;
	public $productTypeId;
	public $returnPage;
	
	public function __construct($options = array() ) {
		$this->page = false;
		$this->productTypeId = $options['productTypeId']; 
		$this->returnPage    = (isset($options['returnPage']))
			? $options['returnPage']
			: 1;
	}
	
	public function load($productPage) {
		$this->page = $productPage;
	}
	
	public function render($bEcho = true) {
	        $ctx = App::Get()->loadModule();
		$str = '';
		if ($this->page) {
			$str .= "<ul class=\"pp_productList\">";
			$products = $this->page->getPageProducts();
			foreach ($products as $product) {
				$str .= "<li><a href=\"".$ctx->moduleRoot."/product/{$product->getId()}/{$this->returnPage}\">" 
				  . urlDecode(basename($product->getName())) . "</a></li>";
			}
			$str .= "</ul>\r\n";
		} 
		if ($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
	
	public function renderPageDetails($bEcho = true) {
		$displayCountStart = $this->page->getPageSize() * ($this->page->getPageNum() - 1) + 1;
		$displayCountEnd   = $displayCountStart + count($this->page->getPageProducts()) - 1;
		$displayCountStart = ($displayCountStart == 0) ? 1 : $displayCountStart;
		
		// Product range displayed and total count
		

		// 'Previous' and 'Next' page links
		$linkBase    = MODULE_ROOT . "/products/{$this->productTypeId}/page";
		$prevPageNum = $this->page->getPageNum() -1;
		$nextPageNum = $this->page->getPageNum() +1;
		 
		$prevPageLink = ($prevPageNum >= 1) 
			? "<a href=\"{$linkBase}/{$prevPageNum}\">&lt;&lt;&nbsp;Previous Page</a>"
			: '';
		$nextPageLink = ($nextPageNum <= $this->page->getTotalPages()) 
			? "<a href=\"{$linkBase}/{$nextPageNum}\">Next Page&nbsp;&gt;&gt;</a>"
			: '';
	
		$rangeInfo = "<span class=\"pp_detail\">Page {$this->page->getPageNum()} of {$this->page->getTotalPages()} "
			."(products {$displayCountStart} - {$displayCountEnd})</span>";

		
		$str = "<div class=\"pp_pageLinks\">{$rangeInfo}&nbsp;&nbsp;{$prevPageLink}&nbsp;&nbsp;{$nextPageLink}</div>\r\n";	
	
		if ($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
}