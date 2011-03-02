<?php
class ProductTypeListWidget 
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {
	
	public $productTypes;
	public $urlBase;
	
	public function __construct($options = array()) {
		$this->productTypes = $options['productTypes'];
		$this->urlBase      = isset($options['urlBase']) 
			? $options['urlBase']
			: '';
	}
	
	public function setUrlBase($base) {
		$this->urlBase = $base;
	}
	
	public function render($bEcho = true) {
		$str = '';
		$str.= "<table id=\"productTypeSearch\" class=\"dataTable\">
			  <thead>
			    <tr>";
		// Display the Column Headers
		foreach (App::Get()->settings['browser_pt_search_met'] as $metKey) {
			$str .= "<th>".ucwords($metKey)."</th>";
		}
		if (isset(App::Get()->settings['browser_pt_hidden_search_met'])) {
			foreach (App::Get()->settings['browser_pt_hidden_search_met'] as $metKey) {
				$str .= "<th class=\"hidden\">{$metKey}</th>";
			}
		}
		$str .= "</tr></thead><tbody>";

		// Display the Data
		foreach ($this->productTypes as $ptKey => $ptMetadata) {
			if (isset(App::Get()->settings['browser_product_type_ignores']) && 
				in_array($ptKey,App::Get()->settings['browser_product_type_ignores'])) { continue; }
			$str .= "<tr>";
			foreach (App::Get()->settings['browser_pt_search_met'] as $metKey) {
				if ($metKey == App::Get()->settings['browser_pt_search_linkkey']) {
					$str .= "<td><a href=\"{$this->urlBase}/dataset/{$ptKey}\">{$ptMetadata[$metKey][0]}</a></td>";
				} else {
					if (count($ptMetadata[$metKey]) > 1) {
						$str .= "<td>" . implode(", ", $ptMetadata[$metKey]) . "</td>";
					} else {
						$str .= "<td>{$ptMetadata[$metKey][0]}</td>";	
					}
				}
			}
			if (isset(App::Get()->settings['browser_pt_hidden_search_met'])) {
				foreach (App::Get()->settings['browser_pt_hidden_search_met'] as $metKey) {
					if (count($ptMetadata[$metKey]) > 1) {
						$str .= "<td class=\"hidden\">" . implode(", ", $ptMetadata[$metKey]) . "</td>";
					} else {
						$str .= "<td class=\"hidden\">{$ptMetadata[$metKey][0]}</td>";	
					}
				}
			}
			$str .= "</tr>\r\n";
		}
		$str .= "</tbody></table>";	

		if ($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
}