<?php

//Copyright (c) 2007, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

require_once ("XML/RPC.php");

/**
 * A PHP representation of the CAS metadata data structure, which
 * is a structure of key=>List of String values.
 */
class Metadata {

	public $elemMap;

	function __construct($xmlRpcData = array ()) {
		//$this->elemMap = array();
		$this->elemMap = $xmlRpcData;
		/*foreach ($xmlRpcData as $key => $value){
		    $this->elemMap[$key] = array();		
			foreach($value as $v){
				array_push($this->elemMap[$key], $v);
			}
		}*/
	}

	function __destruct() {

	}

	function toXmlRpcStruct() {
		$xmlRpcStruct = array ();
		foreach ($this->elemMap as $key => $val) {
			$valList = array ();
			foreach ($val as $v) {
				array_push($valList, new XML_RPC_VALUE($v, 'string'));
			}
			$xmlRpcStruct[$key] = new XML_RPC_VALUE($valList, 'array');
		}

		return new XML_RPC_VALUE($xmlRpcStruct, 'struct');
	}

	function toAssocArray() {
		return $this->elemMap;
	}

	function addMetadata($key, $value) {
		array_push($this->elemMap[$key], $value);
	}

	function replaceMetadata($key, $value) {
		$this->elemMap[$key] = $value;
	}

	function removeMetadata($key) {
		unset ($this->elemMap[$key]);
	}

	function getAllMetadata($key) {
		return $this->elemMap[$key];
	}

	function getMetadata($key) {
		return $this->elemMap[$key][0];
	}

	function containsKey($key) {
		return array_key_exists($key, $this->elemMap);
	}

	function isMultiValued($key) {
		return array_count_values($this->elemMap[$key]) > 1;
	}
}
?>