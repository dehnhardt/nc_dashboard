<?php

namespace OCA\[YourApp]\Widget;

use \OCA\Dashboard\Widgets\IWidgetTemplate;
use \OCA\Dashboard\Widgets\WidgetTemplate;


class OwnNoteTemplate extends WidgetTemplate implements IWidgetTemplate{

	function getContentHtml( $data = array()){
		$html = '<h3>[YourAppName]</h3>';
		return $html;
	}
	
	function getSettingsArray(){
		return array();
	}
	
	function getLicenseInfo(){
		return 'License Info';
	}
}