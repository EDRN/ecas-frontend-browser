<?php
class ProductDownloadWidget 
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {
	
	public $client;
	public $product;
	public $dataDeliveryUrl;
	
	public function __construct($options = array()) {
		$this->dataDeliveryUrl = $options['dataDeliveryUrl'];
		
	}
	
	public function setClient(&$XmlRpcClient) {
		$this->client = $XmlRpcClient;
	}
	
	public function load($Product) {
		$this->product = $Product;
	}
	
	public function render($bEcho = true) {
	        $ctx = App::Get()->loadModule();
		$str = '';
		$references = $this->client->getProductReferences($this->product);
		if (isset($references['faultCode'])){
			$str .=  "<div class=\"error\">";
			$str .=  "Error encountered while attempting to retrieve product references.<br/>";
			$str .=  "FAULT CODE: $references[faultCode]<br/>";
			$str .=  "FAULT STRING: $references[faultString]<br/>";
			$str .=  "</div>";
			if ($bEcho) {
				echo $str;
			} else {
				return $str;
			}
		}
		
		$referenceCounter = 0;
		$str .=  "<table class=\"pdw_downloadTable\">";
		foreach ($references as $reference){
			$fileNameParts = split("/",$reference['dataStoreReference']);
			$fileName = $fileNameParts[sizeof($fileNameParts) -1];
			$fileSize = $reference['fileSize'];
			$fileSizeStr = "";
			($fileSize > (1024*1024)) 
				? $fileSizeStr = number_format(($fileSize/(1024*1024)),1) . " MB"
				: (($fileSize > (1024))
					? $fileSizeStr = number_format(($fileSize / 1024),1) . " KB"
					: $fileSizeStr = $fileSize . " bytes");
			$str .=  "<tr>";
			$str .=  "<td>";
			if ($reference['mimeType'] == 'image/jpeg') {
				$str .=  "<img class=\"tn\" src=\"".$this->dataDeliveryUrl."/data?refIndex=$referenceCounter&productID={$this->product->getID()}\">";	
			} else {
				$str .=  "<img class=\"tn\" src=\"".$ctx->moduleStatic."/img/download-icon.gif\"/>";
			}
			$str .=  "</td>";
			$str .=  "<td style=\"vertical-align:top;\">".urldecode($fileName)." <br/><span style=\"color:#555;font-size:0.9em;\">$fileSizeStr</span><br/>";
			$str .=  "Mime Type: $reference[mimeType]<br/>";
			if($reference['mimeType'] == 'image/jpeg') {
				$str .=  "&nbsp;<a href=\"".$this->dataDeliveryUrl."/data?refIndex=$referenceCounter&productID={$this->product->getID()}\" target=\"_new\">view</a> &nbsp;";
				$str .=  "<a href=\"getImage.php?productID={$this->product->getID()}&refNumber=$referenceCounter&fileName=$fileName\">save</a>&nbsp;";	
			}
			else{
				$str .=  "<a href=\"".$this->dataDeliveryUrl."/data?refIndex=$referenceCounter&productID={$this->product->getID()}\">save</a> &nbsp;";		
			}
				
			$str .=  "</td>";
			$str .=  "</tr>";
			$referenceCounter++;
		}
		$str .=  "</table>";
		
		if ($bEcho) {
			echo $str;
		} else {
			return $str;
		}	
	}
}