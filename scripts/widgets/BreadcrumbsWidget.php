<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * BREADCRUMB DISPLAY
 * 
 * Widget to display basic information about the current page. It allows 
 * users to keep track of their locations within the website. 
 * 
 * These breadcrumbs will allow the user to return to parent pages when a 
 * a link is provided.
 * 
 * Initialize BreadcrumbsWidget:
 * 		$bcw = new BreadcrumbsWidget();
 * 
 * Add breadcrumb label:
 * 		$bcw->add('Home');
 * 
 * When breadcrumb links are included:
 * 	 	$bcw->add('Home',SITE_ROOT . '/');
 * 
 * 
 * @author ahart
 * @author s.khudikyan 
 * 
 */

class BreadcrumbsWidget 
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {
		

	public function __construct($options = array()) {}
	
	public function add( $label = null, $link = null) {
		$breadcrumbs = App::Get()->response->data('breadcrumbs');
		if ( $label != null && $link != null ) {
			
			// will show up as a linked label
			$breadcrumbs[] = array($label,$link);
		} elseif ( $label != null ) {
			
			// will show up as just text
			$breadcrumbs[] = $label;
		} else
			return 0;
			
		App::Get()->response->data("breadcrumbs",$breadcrumbs);
	}
		
	public function render($bEcho = true) {
		
		$str 	= '';
		$data = App::Get()->response->data('breadcrumbs');
		if ( !empty($data) ) {
			foreach ($data as $bc) {
				if (is_array($bc)) {
					$str 	.= '<a href="' . $bc[1] . '">' . $bc[0] . '</a>&nbsp;&rarr;&nbsp;';
				} else {
					$str 	.= $bc;
				}
			}
		}
		
		if($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
	
}