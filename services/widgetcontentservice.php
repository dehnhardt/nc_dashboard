<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 27.04.15
 * Time: 20:29
 */

namespace OCA\Dashboard\Services;

use OCA\Dashboard\Widgets\Status;
use OCP\IL10N;


class WidgetContentService {

    private $user;
    private $l10n;
    private $widgetManagementService;
    private $widgetHashService;

    public function __construct($user, IL10N $l10n, WidgetManagementService $widgetManagementService, WidgetHashService $widgetHashService) {
        $this->user                     = $user;
        $this->l10n                     = $l10n;
        $this->widgetManagementService  = $widgetManagementService;
        $this->widgetHashService        = $widgetHashService;
    }

    /**
     *
     * returns the complete html code for the wIId
     *
     * @param String $wIId
     * @return String html
     */
    public function getComplete($wIId) {
        /** @var $widgetController \OCA\Dashboard\Widgets\IWidgetController */
        $widgetController   = $this->widgetManagementService->getInstance($wIId, 'controller');

        if( $widgetController ){
        	\OCP\Util::writeLog('Dashboard', 'instance of '.$wIId.' controller created', \OCP\Util::DEBUG);
        } else{
        	\OCP\Util::writeLog('Dashboard', 'can not get instance of '.$wIId.' controller', \OCP\Util::ERROR);
        }
        
        /** @var $widgetTemplate  \OCA\Dashboard\Widgets\IWidgetTemplate */
        $widgetTemplate     = $this->widgetManagementService->getInstance($wIId, 'template');

        $contentData        = $widgetController->getData();
        $basicValues        = $widgetController->getBasicValues();
        $data               = array_merge($contentData, $basicValues);
        $data['status']     = $this->calculateStatus($wIId, $data);
        $data['widgetHtml'] = $widgetTemplate->getCompleteHtml( $data );
        return $data;
    }

    /**
     *
     * return the html for the content part of the wIId
     *
     * @param String $wIId
     * @return string html
     */
    public function getContent($wIId) {
        /** @var $widgetController \OCA\Dashboard\Widgets\IWidgetController */
        $widgetController   = $this->widgetManagementService->getInstance($wIId, 'controller');
        
        if( $widgetController ){
        	\OCP\Util::writeLog('Dashboard', 'instance of '.$wIId.' controller created', \OCP\Util::DEBUG);
        } else{
        	\OCP\Util::writeLog('Dashboard', 'can not get instance of '.$wIId.' controller', \OCP\Util::ERROR);
        }
        
        /** @var $widgetTemplate  \OCA\Dashboard\Widgets\IWidgetTemplate */
        $widgetTemplate     = $this->widgetManagementService->getInstance($wIId, 'template');

        $contentData        = $widgetController->getData();
        $basicValues        = $widgetController->getBasicValues();
        $data               = array_merge($contentData, $basicValues);
        $data['status']     = $this->calculateStatus($wIId, $data);
        $data['widgetHtml'] = $widgetTemplate->getContentHtml( $data );
        return $data;
    }

    /**
     *
     * call a widget-method
     * you can define a key and value as strings
     *
     * @param $wIId
     * @param String $method
     * @param String $value
     * @return bool if execution success
     */
    public function callMethod($wIId, $method, $value)
    {
        $widgetController   = $this->widgetManagementService->getInstance($wIId, 'controller');
        if( method_exists($widgetController, $method) ) {
            return array(
                'success'   => $widgetController->$method($value)
            );
        } else {
            \OCP\Util::writeLog('dashboard', 'method \'$method\' for widget \'$wIId\' not found', \OCP\Util::WARN);
            return array();
        }
    }

    /**
     *
     * returns the status for the wIId
     *
     * @param $wIId
     * @param null $data
     * @return int
     */
    private function calculateStatus($wIId, $data=null) {
        $status = (isset($data['status'])) ? $data['status']: Status::STATUS_OKAY;
        if( $status === Status::STATUS_OKAY && $this->widgetHashService->isNew($wIId, $data) ) {
            return Status::STATUS_NEW;
        }
        return $status;
    }


}
