<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 27.04.15
 * Time: 20:54
 */

namespace OCA\Dashboard\Controller;

use OCA\Dashboard\Services\WidgetManagementService;
use OCP\AppFramework\Controller;
use OCP\IL10N;
use OCP\IRequest;
use OCP\Util;

class RouteWidgetManagementController extends Controller {

    private $widgetManagementService;
    private $L10N;

    public function __construct($appName, IRequest $request, $user, WidgetManagementService $widgetManagementService, IL10N $l10n){
    parent::__construct($appName, $request);
        $this->user                     = $user;
        $this->widgetManagementService  = $widgetManagementService;
        $this->L10N                     = $l10n;
    }

    /**
    *
    * return a array of widgets for this user
    *
    * @NoAdminRequired
    * @return array
    */
    public function getEnabledWidgets() {
        return $this->widgetManagementService->getEnabled();
    }

    /**
     *
     * return a array of widgets that are available
     *
     * @NoAdminRequired
     * @return array
     */
    public function getAvailableWidgets() {
        return $this->widgetManagementService->getAvailable(true);
    }

    /**
     *
     * returns the basic conf as array
     * includes wId, name, refresh, icon
     *
     * @NoAdminRequired
     * @param $wId
     * @return array
     */
    public function getBasicConf( $wId ) {
        /** @var $controllerClass \OCA\Dashboard\Widgets\IWidgetController */
        if( $controllerClass = $this->widgetManagementService->getInstance($wId.'-0') ) {
            return $controllerClass->getBasicValues();
        }
        return false;
    }

    public function enableWidgetGroup($wIdG) {
        if( !isset($wIdG) ) {
            return array('success' => 0);
        }
        return $this->widgetManagementService->enable($wIdG);
    }

    public function disableWidgetGroup($wIdG) {
        if( !isset($wIdG) ) {
            return array('success' => 0);
        }
        return $this->widgetManagementService->disable($wIdG);
    }


    /**
     * @NoAdminRequired
     * @param $wId
     * @return array
     */
    public function addNewInstance($wId) {
        return array(
            'wIId' => $this->widgetManagementService->addNewInstance($wId)
        );
    }

    public function removeInstance($wIId) {
        $this->widgetManagementService->removeInstance($wIId);
    }

}
