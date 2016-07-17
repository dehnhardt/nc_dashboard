<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 27.04.15
 * Time: 20:29
 */

namespace OCA\Dashboard\Api;



use OCA\Dashboard\Services\WidgetManagementService;


class Api_1_0 {

    private $user;
    private $widgetManagementService;

    public function __construct($user, WidgetManagementService $widgetManagementService) {
        $this->user                     = $user;
        $this->widgetManagementService  = $widgetManagementService;
    }

    /**
     * @param $widget
     * @return bool
     */
    public function addWidget($widget) {
        $special    = (isset($widget['special']))   ? $widget['special']    : '';
        $css        = (isset($widget['css']))       ? $widget['css']        : array();
        $js         = (isset($widget['js']))        ? $widget['js']         : array();
        $version    = (isset($widget['version']))   ? $widget['version']	: 1;
        $link 		= (isset($widget['link']))	 	? $widget['link']		: null;
        $enableDefault  	= (isset($widget['enableDefault']) && $widget['enableDefault']) === true ? true : false; 
        return $this->widgetManagementService->addWidget(
            $widget['wId'],
            $widget['appName'],
        	$version,
        	$enableDefault,
            $widget['controllerServiceName'],
            $widget['templateServiceName'],
        	$link,
            $special,
            $css,
            $js
        );
    }
}
