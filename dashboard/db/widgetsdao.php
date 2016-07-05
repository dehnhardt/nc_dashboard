<?php

namespace OCA\Dashboard\Db;

use \OCP\IDb;

class WidgetsDAO {

    private $db;
    private $table;

    public function __construct(IDb $db) {
        $this->db       = $db;
        $this->table    = '*PREFIX*dashboard_widgets';
    }

    /**
     *
     * return a array with all available widgets
     * and enabled_groups for them
     *
     * @return array
     */
    public function getAvailableWidgets() {
        $sql = 'SELECT `wid`, `enabled_groups` FROM `'.$this->table.'`';

        $query = $this->db->prepareQuery($sql);
        $result = $query->execute();
        $return = array();
        while( $row = $result->fetchRow() ) {
            $return[]   = array('wId' => $row['wid'], 'enabled_groups' => $row['enabled_groups']);
        }
        return $return;
    }

    /**
     *
     * insert a new widget with its settings,
     * but only if it does not exist
     * (identified by the wId)
     *
     * @param $wId
     * @param $appName
     * @param $controllerServiceName
     * @param $templateServiceName
     * @param string $special
     * @param array $cssPaths
     * @param array $jsPaths
     * @return bool
     */
    public function insertIfNotExist($wId, $appName, $controllerServiceName, $templateServiceName, $special='', $cssPaths=array(), $jsPaths=array()) {
        $cssPaths   = implode(':', $cssPaths);
        $jsPaths    = implode(':', $jsPaths);
        $sql        = 'SELECT count(id) as counter FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( $row['counter'] == 0 ) {
            $sql = 'INSERT INTO `'.$this->table.'`(`wid`, `app_name`, `controller_service_name`, `template_service_name`, `enabled_groups`, `special`, `css_paths`, `js_paths`) VALUES(?,?,?,?,?,?,?,?)';
            $params = array($wId, $appName, $controllerServiceName, $templateServiceName, '', $special, $cssPaths, $jsPaths);
            $query = $this->db->prepareQuery($sql);
            $execute = $query->execute( $params );
            return $execute;
        } else {
            return false;
        }
    }

    /**
     *
     * returns the appName by a wId
     *
     * @param $wId
     * @return bool
     */
    public function getAppName($wId) {
        $sql        = 'SELECT `app_name` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['app_name']) ) {
            return $row['app_name'];
        } else {
            return false;
        }
    }

    /**
     *
     * returns the controllerServiceName by a wId
     *
     * @param $wId
     * @return bool
     */
    public function getControllerServiceName($wId) {
        $sql        = 'SELECT `controller_service_name` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['controller_service_name']) ) {
            return $row['controller_service_name'];
        } else {
            return false;
        }
    }

    /**
     *
     * returns the templateServiceName by a wId
     *
     * @param $wId
     * @return bool
     */
    public function getTemplateServiceName($wId) {
        $sql        = 'SELECT `template_service_name` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['template_service_name']) ) {
            return $row['template_service_name'];
        } else {
            return false;
        }
    }

    /**
     *
     * returns the cssPaths by a wId as array
     *
     * @param $wId
     * @return array
     */
    public function getCssPaths($wId) {
        $sql        = 'SELECT `css_paths` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['css_paths']) && $row['css_paths'] != '' ) {
            return explode(':', $row['css_paths']);
        } else {
            return array();
        }
    }

    /**
     *
     * returns the jsPaths by a wId as array
     *
     * @param $wId
     * @return array
     */
    public function getJsPaths($wId) {
        $sql        = 'SELECT `js_paths` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['js_paths']) && $row['js_paths'] != '' ) {
            return explode(':', $row['js_paths']);
        } else {
            return array();
        }
    }

    public function getEnabledGroups($wId) {
        $sql        = 'SELECT `enabled_groups` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['enabled_groups']) && $row['enabled_groups'] != '' ) {
            return explode(':', $row['enabled_groups']);
        } else {
            return array();
        }
    }

    public function updateEnabledGroups($wId, $gIds=array()) {
        $sql        = 'UPDATE `'.$this->table.'` SET `enabled_groups` = ? WHERE `wid` = ?';
        $wIds       = implode(':', $gIds);
        $query      = $this->db->prepareQuery($sql);
        return $query->execute(array($wIds, $wId));
    }
}