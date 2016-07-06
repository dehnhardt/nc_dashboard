<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 27.04.15
 * Time: 20:54
 */

namespace OCA\Dashboard\Services;

use OC\DateTimeFormatter;
use OCA\Dashboard\Db\WidgetsDAO;
use OCA\Dashboard\Utils\Helper;
use OCP\IL10N;
use OCP\IGroupManager;
use OCP\IUserManager;

class WidgetManagementService {
  
    private $widgetSettingsService;
    private $widgetsDAO;
    private $widgetHashService;
    private $L10N;
    private $user;
    private $dateTimeFormatter;
    private $groupManager;
    private $userManager;

    public function __construct($user, IL10N $l10n, WidgetSettingsService $widgetSettingsService, WidgetsDAO $widgetsDAO, DateTimeFormatter $dateTimeFormatter, WidgetHashService $widgetHashService, IGroupManager $groupManager, IUserManager $userManager){
        $this->user                     = $user;
        $this->L10N                     = $l10n;
        $this->widgetSettingsService    = $widgetSettingsService;
        $this->widgetsDAO               = $widgetsDAO;
        $this->dateTimeFormatter        = $dateTimeFormatter;
        $this->widgetHashService        = $widgetHashService;
        $this->groupManager             = $groupManager;
        $this->userManager              = $userManager;
    }

    public function getEnabled() {
        $enabledWidgets         = $this->widgetSettingsService->getEnabledWidgets();
        foreach ($enabledWidgets as $key => $wIId) {
            if( !$this->isWidgetForUserAllowedByWidget($this->user, Helper::wId($wIId)) ) {
                unset($enabledWidgets[$key]);
            }
        }
        $sortedEnabledWidgets   = $this->sortWidgets($enabledWidgets);
        return $sortedEnabledWidgets;
    }

    public function getAvailable($filterByUser=false) {
        $return = array();
        $allWidgets = $this->widgetsDAO->getAvailableWidgets();

        foreach ($allWidgets as $widget) {
            $enabledGroups = explode(':', $widget['enabled_groups']);
            //TODO eventually reenable filter by group
            /*if($filterByUser) {
                if($this->isWidgetForUserAllowed($this->user, $enabledGroups)) {
                    $return[] = $widget['wId'];
                }
            } else {*/
                $return[] = $widget['wId'];
            //}
        }
        return $return;
    }

    public function enable($wIdG) {
        return $this->widgetSettingsService->enableWidgetForGroup(Helper::wId($wIdG), Helper::gId($wIdG));
    }

    public function disable($wIdG) {
        return $this->widgetSettingsService->disableWidgetForGroup(Helper::wId($wIdG), Helper::gId($wIdG));
    }

    public function addNewInstance($wId) {
        $wIId = $wId.'-'.$this->widgetSettingsService->getNextWidgetInstanceNumber($wId);
        $this->widgetSettingsService->setConfig($wIId, 'enabled', '1');
        return $wIId;
    }

    public function removeInstance($wIId) {
        return ($this->widgetSettingsService->removeWidgetInstance($wIId) && $this->widgetHashService->removeHashes($wIId) );
    }

    /**
     *
     * returns an object instance for a wIId
     * for a controller or a template object
     *
     * @param $wIId
     * @param string $type {'controller', 'template'}
     * @return object {widget controller object, widget template object}
     */
    public function getInstance($wIId, $type='controller') {
        $appName        = $this->widgetsDAO->getAppName(Helper::wId($wIId));
        $serviceName    = null;
        $service        = null;

        if( $type === 'controller' ) {
            $serviceName = $this->widgetsDAO->getControllerServiceName(Helper::wId($wIId));
            if( $serviceName && $appName ) {
                /** @var $widgetControllerService \OCA\Dashboard\Widgets\WidgetController */
                if( $widgetControllerService = $this->getServiceFromAppContainer($appName, $serviceName) ) {
                    $widgetControllerService->secondInjection(Helper::wNo($wIId), $this->widgetSettingsService, $this->user, $this->L10N, $this->dateTimeFormatter);
                    return $widgetControllerService;
                }
            }
        } elseif( $type === 'template' ) {
            $serviceName = $this->widgetsDAO->getTemplateServiceName(Helper::wId($wIId));
            if( $serviceName && $appName ) {
                /** @var $widgetTemplateService \OCA\Dashboard\Widgets\WidgetTemplate */
                if( $widgetTemplateService = $this->getServiceFromAppContainer($appName, $serviceName) ) {
                    $widgetTemplateService->secondInjection($this->L10N, $this->widgetSettingsService, $this->dateTimeFormatter);
                    return $widgetTemplateService;
                }
            }
        }
        \OC_Log::write('dashboard',"instance ".$type." from ".$wIId." could not be loaded.", \OC_Log::WARN);
        return null;
    }

    /**
     *
     * add a widget to the available list
     *
     * @param $wId
     * @param $appName
     * @param $controllerServiceName
     * @param $templateServiceName
     * @param $special
     * @param $cssPaths
     * @param $jsPaths
     * @return bool|int|\OC_DB_StatementWrapper
     */
    public function addWidget($wId, $appName, $controllerServiceName, $templateServiceName, $special, $cssPaths, $jsPaths) {
        $result = $this->widgetsDAO->insertIfNotExist($wId, $appName, $controllerServiceName, $templateServiceName, $special, $cssPaths, $jsPaths);
        //\OC_Log::write('dashboard',"widget could not be added.", \OC_Log::WARN);
        return $result;
    }

    /**
     * returns a array with widgets and its enabled groups
     */
    public function getEnabledWidgetGroups() {
        $return = array();
        $availableWidgets = $this->widgetsDAO->getAvailableWidgets();
        foreach ($availableWidgets as $availableWidget) {
            $availableWidget = $availableWidget['wId'];
            $gIds = $this->widgetsDAO->getEnabledGroups($availableWidget);
            foreach ($gIds as $gId) {
                $return[]   = $availableWidget.'-'.$gId;
            }
        }
        return $return;
    }


    // ----- PRIVATE METHODS -------------------------------------

    /**
     *
     * return the object from requested service from DI-container
     *
     * @param $appName
     * @param $serviceName
     * @return mixed|null
     */
    private function getServiceFromAppContainer($appName, $serviceName) {
        $appNamespace   = '\OCA\\'.strtolower($appName).'\AppInfo\Application';
        if( !class_exists($appNamespace) ) {
            \OC_Log::write('dashboard',"unknown app: ".$appNamespace, \OC_Log::WARN);
            return null;
        }
        /** @var $app \OCP\AppFramework\App */
        $app            = new $appNamespace();
        $container      = $app->getContainer();
        return $container->query($serviceName);
    }

    /**
     *
     * input is a array with unsorted wIIds
     * output are sorted wIIds
     *
     * @param $wIIdsArray
     * @return array
     */
    private function sortWidgets($wIIdsArray) {
        $widgets = array();
        foreach ($wIIdsArray as $wIId) {
            $widgets[] = array(
                'wIId'   => $wIId,
                'order' => $this->widgetSettingsService->getConfig(Helper::wId($wIId), Helper::wNo($wIId), 'order', 10)
            );
        }
        usort($widgets, function ($a, $b) {
            if ($a['order'] == $b['order']) {
                return 0;
            }
            return ($a['order'] < $b['order']) ? -1 : 1;
        });
        $wIIds = array();
        foreach ($widgets as $widget) {
            $wIIds[] = $widget['wIId'];
        }
        return $wIIds;
    }

    /**
     *
     * check if a user is in one or more of the groups
     *
     * @param $user
     * @param $enabledGroups
     * @return bool
     */
    private function isWidgetForUserAllowed($user, $enabledGroups) {
        if( in_array('all', $enabledGroups) ) {
            return true;
        }
        $user = $this->userManager->get($user);
        $userGroupIds = $this->groupManager->getUserGroupIds($user);
        $userGroupIds = array_flip($userGroupIds);
        $enabledForUser = array_intersect($userGroupIds, $enabledGroups);
        if( count($enabledForUser) > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    private function isWidgetForUserAllowedByWidget($user, $wId) {
        $enabledGroups = $this->widgetsDAO->getEnabledGroups($wId);
        return $this->isWidgetForUserAllowed($user, $enabledGroups);
    }

}
