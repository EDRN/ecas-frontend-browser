<?php
//Copyright (c) 2007, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

require_once("XML/RPC.php");

class XmlRpcManager {
	private $client;		// The XML/RPC client
	public $serverURL;		// The base URL of the server (default is localhost:9000)
	public $serverPath;		// The path to the XMLRPC handler (/path/to/xmlrpc)
	
	function __construct($serverURL = 'localhost:9000',$serverPath = '/'){
		$this->serverURL = $serverURL;
		$this->serverPath = $serverPath;
		try {
			$this->client = new XML_RPC_Client($this->serverPath,$this->serverURL);
		} catch (Exception $e) {
			echo "<h4>Error creating XMLRPC client: " . $e.getMessage() . "</h4>";
			exit();
		}
		if (!$this->client->server) {
			echo "<h4>Error connecting to XMLRPC server. A connection could not be established.</h4>";
			exit();
		}
	}
	
	function __destruct(){

	}


	function getProductById($productID){
		$params = array(new XML_RPC_Value($productID,'string'));
		$message = new XML_RPC_Message('filemgr.getProductById',$params);
		$response = $this->client->send($message);
		
		if (!$response){
			echo "<h4>Communication Error: {$this->client->errstr}</h4>";
			exit(); 
		}
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}
	}
	function getProductReferences($product){
		$params = array($product->toXmlRpcStruct());
		$message = new XML_RPC_Message('filemgr.getProductReferences',$params);
		$response = $this->client->send($message);
		
		if (!$response){
			echo "<h4>Communication Error: {$this->client->errstr}</h4>";
			exit(); 
		}
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}
	}
	
	function getMetadata($product){	
		$params = array($product->toXmlRpcStruct());
		$message = new XML_RPC_Message('filemgr.getMetadata',$params);
		$response = $this->client->send($message);
		
		if (!$response){
			echo "<h4>Communication Error: {$this->client->errstr}</h4>";
			exit(); 
		}
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}
	}
	
	function getProductTypeById($typeID){
		$params = array(new XML_RPC_Value($typeID,'string'));
		$message = new XML_RPC_Message("filemgr.getProductTypeById", $params);
		$response = $this->client->send($message);
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}		
		
	}
	
	function getProductsByProductType($type){
		$params = array($type->toXmlRpcStruct());
		$message = new XML_RPC_Message("filemgr.getProductsByProductType", $params);
		$response = $this->client->send($message);
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}		
		
	}

	function getProductTypes(){
		$params = array();
		$message = new XML_RPC_Message("filemgr.getProductTypes", $params);
		$response = $this->client->send($message);
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}		
		
	}
		
	
	
    function getNumProducts($type){
		$params = array($type->toXmlRpcStruct());
		$message = new XML_RPC_Message("filemgr.getNumProducts", $params);
		$response = $this->client->send($message);
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}		
		
	}
	
	/* Pagination API */
	
	function getFirstPage($type){
		$params = array($type->toXmlRpcStruct());
		$message = new XML_RPC_Message("filemgr.getFirstPage", $params);
		$response = $this->client->send($message);
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}			
	}
	
	function getNextPage($type, $page){
		$params = array($type->toXmlRpcStruct(), $page->toXmlRpcStruct());
		$message = new XML_RPC_Message("filemgr.getNextPage", $params);
		$response = $this->client->send($message);
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}			
	}
	
	function getPrevPage($type, $page){
		$params = array($type->toXmlRpcStruct(), $page->toXmlRpcStruct());
		$message = new XML_RPC_Message("filemgr.getPrevPage", $params);
		$response = $this->client->send($message);
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}			
	}
	
	function getLastPage($type){
		$params = array($type->toXmlRpcStruct());
		$message = new XML_RPC_Message("filemgr.getLastPage", $params);
		$response = $this->client->send($message);
		
		if (!$response->faultCode()) {
			$value = $response->value();
			$data = XML_RPC_decode($value);
			return $data;
		} else {
			echo "<h4>Fault Code Returned: (" . $response->faultCode . ") </h4>";
			exit();
		}		
	}
}
?>