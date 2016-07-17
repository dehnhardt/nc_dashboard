<?php


namespace OCA\[YourApp]\Widget;

use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;

class [YourApp]Controller extends WidgetController implements IWidgetController{
	
	public function setBasicValues(){
		$this->refresh = 30;
		$this->wId = '[yourapp]';
		$this->name = '[YourApp] Widget';
	}
		
	public function getData() {
		return array();
	}
}