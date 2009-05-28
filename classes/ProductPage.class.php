<?php
//Copyright (c) 2009, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

require_once("XML/RPC.php");

/**
 * @author mattmann
 * @version $Revision$
 * 
 * A Page of {@link Product}s returned from the <code>File Manager</code>.
 * 
 */
class ProductPage {

	/* the number of this page */
	private $pageNum = -1;

	/* the total number of pages in the set */
	private $totalPages = -1;

	/* the size of the number of products on this page */
	private $pageSize = -1;

	/* the list of produdcts associated with this page */
	private $pageProducts = null;

	/**
	 * <p>
	 * Default Constructor
	 * </p>.
	 */
	function __construct() {
		$this->pageProducts = array ();
	}

	/**
	 * @param pageNum
	 *            The number of this page.
	 * @param totalPages
	 *            The total number of pages in the set.
	 * @param pageSize
	 *            The size of this page.
	 * @param pageProducts
	 *            The products associated with this page.
	 */
	function __init($pageNum, $totalPages, $pageSize, $pageProducts) {
		$this->pageNum = pageNum;
		$this->totalPages = totalPages;
		$this->pageSize = pageSize;
		$this->pageProducts = pageProducts;
	}

    function __initXmlRpc($xmlRpcData){
		$this->pageNum = (isset($xmlRpcData['pageNum']))
			? intval($xmlRpcData['pageNum'])
			: -1;
		$this->totalPages = (isset($xmlRpcData['totalPages']))
			? intval($xmlRpcData['totalPages']) 
			: -1;
		$this->pageSize = (isset($xmlRpcData['pageSize']))
			? intval($xmlRpcData['pageSize']) 
			: -1;
		$this->pageProducts = (isset($xmlRpcData['pageProducts']))
			? $xmlRpcData['pageProducts'] 
			: array();    	
    }
    
    
	/**
	 * @return Returns the pageNum.
	 */
	public function getPageNum() {
		return $this->pageNum;
	}

	/**
	 * @param pageNum
	 *            The pageNum to set.
	 */
	public function setPageNum($pageNum) {
		$this->pageNum = $pageNum;
	}

	/**
	 * @return Returns the pageProducts.
	 */
	public function getPageProducts() {
		return $this->pageProducts;
	}

	/**
	 * @param pageProducts
	 *            The pageProducts to set.
	 */
	public function setPageProducts($pageProducts) {
		$this->pageProducts = $pageProducts;
	}

	/**
	 * @return Returns the pageSize.
	 */
	public function getPageSize() {
		return $this->pageSize;
	}

	/**
	 * @param pageSize
	 *            The pageSize to set.
	 */
	public function setPageSize($pageSize) {
		$this->pageSize = $pageSize;
	}

	/**
	 * @return Returns the totalPages.
	 */
	public function getTotalPages() {
		return $this->totalPages;
	}

	/**
	 * @param totalPages
	 *            The totalPages to set.
	 */
	public function setTotalPages($totalPages) {
		$this->totalPages = $totalPages;
	}

	/**
	 * 
	 * @return True if this is the last page in the set, false otherwise.
	 */
	public function isLastPage() {
		return $this->pageNum == $this->totalPages;
	}

	/**
	 * 
	 * @return True if this is the fist page of the set, false otherwise.
	 */
	public function isFirstPage() {
		return $this->pageNum == 1;
	}

	public function toXmlRpcStruct() {
	  return new XML_RPC_Value(array(
					 'pageNum' => new XML_RPC_Value($this->pageNum,'int'),
					 'pageSize' => new XML_RPC_Value($this->pageSize,'int'),
					 'totalPages' => new XML_RPC_Value($this->totalPages,'int'),
					 'pageProducts' => $this->toXmlRpcProductList($this->pageProducts)), 'struct');
	}

	/**
	 * 
	 * @return A blank, unpopulated {@link ProductPage}.
	 */
	public static function blankPage() {
		$blank = new ProductPage();
		$blank->setPageNum(0);
		$blank->setTotalPages(0);
		$blank->setPageSize(0);
		return $blank;
	}
	
	
	private function toXmlRpcProductList($prodList){
		$prodEncodedArr = array();
		
		foreach ($prodList as $product){
                  $productObj = new Product($product);
		  $prodEncodedArr[] = $productObj->toXmlRpcStruct();
		}
		
		return new XML_RPC_Value($prodEncodedArr, 'array');
	}

}
?>
