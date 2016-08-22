<?php

namespace OCA\Dashboard\Services;

class WidgetService {
	static public function addWidget( $widgetName ) {
        	\OCP\Util::writeLog('Dashboard', 'addWidget called from '.print_r($widgetName, true), \OCP\Util::DEBUG);;
	}
}