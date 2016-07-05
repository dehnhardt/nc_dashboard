<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 27.04.15
 * Time: 20:28
 */

namespace OCA\Dashboard\Services;


use OCA\Dashboard\Db\WidgetConfigDAO;
use OCA\Dashboard\Db\WidgetsDAO;
use OCA\Dashboard\Utils\Helper;

class WidgetSettingsService {

    private $widgetConfigDAO;
    private $widgetsDAO;
    private $user;


    // this key will be stored encrypted in the db
    protected $encryptAttributes = array(
        'password',
        'private',
        'secret'
    );

    function __construct(WidgetConfigDAO $widgetConfigDAO, $user, WidgetsDAO $widgetsDAO) {
        $this->widgetConfigDAO  = $widgetConfigDAO;
        $this->user             = $user;
        $this->widgetsDAO       = $widgetsDAO;
    }

    /**
     *
     * set config to db
     * return true if successful
     *
     * @param $wIId
     * @param $key
     * @param $value
     * @return bool
     */
    public function setConfig($wIId, $key, $value) {
        if( isset($value) && in_array($key, $this->encryptAttributes) ) {
            $value = Helper::encrypt($value);
        }
        return $this->widgetConfigDAO->insertOrUpdateConfig(Helper::wId($wIId), Helper::wNo($wIId), $this->user, $key, $value);
    }

    /**
     *
     * get config from db
     * return null, if none exists
     *
     * @param $wId
     * @param $wNo
     * @param $key
     * @param $default
     * @return null
     */
    public function getConfig($wId, $wNo, $key, $default='') {
        $value = $this->widgetConfigDAO->getConfig($wId, $wNo, $this->user, $key, false);
        if( isset($value) && in_array($key, $this->encryptAttributes) ) {
            $value = Helper::decrypt($value);
        }

        if( !isset($value) ) {
            return $default;
        } else {
            return $value;
        }
    }

    public function getNextWidgetInstanceNumber($wId) {
        $max = intval( $this->widgetConfigDAO->getHighestNo($wId, $this->user) );
        return $max + 1;
    }

    public function getEnabledWidgets() {
        return $this->widgetConfigDAO->findEnabledWidgets($this->user);
    }

    public function removeWidgetInstance ($wIId) {
        return $this->widgetConfigDAO->removeWidgetConfigs(Helper::wId($wIId), Helper::wNo($wIId), $this->user);
    }

    public function enableWidgetForGroup($wId, $gId) {
        $enabledGroupIds = $this->widgetsDAO->getEnabledGroups($wId);
        if( !in_array($gId, $enabledGroupIds)) {
            $enabledGroupIds[] = $gId;
            return $this->widgetsDAO->updateEnabledGroups($wId, $enabledGroupIds);
        }
        return true;
    }

    public function disableWidgetForGroup($wId, $gId) {
        $enabledGroupIds = $this->widgetsDAO->getEnabledGroups($wId);
        if( in_array($gId, $enabledGroupIds) ) {
            $key = array_search($gId, $enabledGroupIds);
            unset($enabledGroupIds[$key]);
            return $this->widgetsDAO->updateEnabledGroups($wId, $enabledGroupIds);
        }
        return true;
    }
}
