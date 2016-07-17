<?php

namespace OCA\Dashboard\Db;

use \OCP\IDb;

class WidgetsDAO {

    private $db;
    private $table;
    
    private $settings;

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
     * @param $version
     * @param $enableDefault
     * @param $controllerServiceName
     * @param $templateServiceName
     * @param $link
     * @param string $special
     * @param array $cssPaths
     * @param array $jsPaths
     * @return bool
     */
    public function insertIfNotExist($wId, $appName, $version, $enableDefault, $controllerServiceName, $templateServiceName, $link, $special='', $cssPaths=array(), $jsPaths=array()) {
        $groups		= $enableDefault ? 'all' : '';
    	$cssPaths   = implode(':', $cssPaths);
        $jsPaths    = implode(':', $jsPaths);
        $sql        = 'SELECT version FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( ! $row ) {
            $sql = 'INSERT INTO `'.$this->table.'`(`wid`, `version`, `app_name`, `controller_service_name`, `template_service_name`, `link`, `enabled_groups`, `special`, `css_paths`, `js_paths`) VALUES(?,?,?,?,?,?,?,?,?,?)';
            $params = array($wId, $version, $appName, $controllerServiceName, $templateServiceName, $link, $groups, $special, $cssPaths, $jsPaths);
            $query = $this->db->prepareQuery($sql);
            $execute = $query->execute( $params );
            return $execute;
        } else if( intval($row['version']) !== intval($version) ) {
        	\OCP\Util::writeLog('dashboard','version XML: '.$version.' SQL: '. $row['version'], \OCP\Util::DEBUG);
        	$sql = 'UPDATE '.$this->table.
            	' set `version` = ?, `app_name` = ?, `controller_service_name` = ?, `template_service_name` = ?, `link` = ?, `special` = ?, `css_paths` = ?, `js_paths` = ? where `wid` = ?'; 
            $params = array( $version, $appName, $controllerServiceName, $templateServiceName, $link, $special, $cssPaths, $jsPaths, $wId);
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
    	
    	$this->getSettings ($wId);
    	
    	return $this->settings['app_name'];
    	
    	/*
        $sql        = 'SELECT `app_name` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['app_name']) ) {
            return $row['app_name'];
        } else {
            return false;
        }
       */
    }
    
    /**
     *
     * returns the controllerServiceName by a wId
     *
     * @param $wId
     * @return bool
     */
    public function getControllerServiceName($wId) {
    	
    	$this->getSettings($wId);
    	
    	return $this->settings['controller_service_name'];
    	
    	/*
        $sql        = 'SELECT `controller_service_name` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['controller_service_name']) ) {
            return $row['controller_service_name'];
        } else {
            return false;
        }*/
    	
    }

    /**
     *
     * returns the templateServiceName by a wId
     *
     * @param $wId
     * @return bool
     */
    public function getTemplateServiceName($wId) {
    	$this->getSettings($wId);
    	
    	return $this->settings['template_service_name'];
    	
    	/*
    	$sql        = 'SELECT `template_service_name` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['template_service_name']) ) {
            return $row['template_service_name'];
        } else {
            return false;
        }*/
    }

    /**
     *
     * returns the cssPaths by a wId as array
     *
     * @param $wId
     * @return array
     */
    public function getCssPaths($wId) {
    	$this->getSettings($wId);
   	
    	//return $this->settings['css_paths'];
    	
    	
    	$sql        = 'SELECT `css_paths` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['css_paths']) && $row['css_paths'] !== '' ) {
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
    	$this->getSettings($wId);
   	
    	//return $this->settings['js_paths'];
    	
    	$sql        = 'SELECT `js_paths` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
		\OCP\Util::writeLog('Dashboard', 'js-row settings of '.$wId.' - '.print_r(explode(':', $row['js_paths']), true), \OCP\Util::DEBUG);
        if( isset($row['js_paths']) && $row['js_paths'] !== '' ) {
            return explode(':', $row['js_paths']);
        } else {
            return array();
        }
    }
    

    /**
     * returns the url which is shown in the widget header
     * 
     * @param string $wId
     * @return string
     */
    
    public function getLink($wId) {
    	$this->getSettings($wId);
    	
    	$value = $this->settings['link'];
    	\OCP\Util::writeLog('Dashboard', 'wdgetsdao: '.$wId.' link '.$value, \OCP\Util::DEBUG);
    	 
    	return $value;
    	
    	/*
    	$sql        = 'SELECT `link` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
    	$query      = $this->db->prepareQuery($sql);
    	$result     = $query->execute(array($wId));
    	$row        = $result->fetchRow();
    	if( isset($row['link']) && $row['link'] !== '' ) {
    		return $row['link'];
    	} else {
    		return '';
    	}*/
    }
    
	public function getAllWidgetSettings($wId){
		$sql        = 'SELECT `id`, `app_name`, `version`, `controller_service_name`, `template_service_name`, `link`, `enabled_groups`, `special`, `css_paths`, `js_paths` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
		$sql        = 'SELECT * FROM `'.$this->table.'` WHERE `wid` LIKE ?';
		$query      = $this->db->prepareQuery($sql);
		$result     = $query->execute(array($wId));
		$row        = $result->fetchRow();
		//\OCP\Util::writeLog('Dashboard', 'row settings of '.$wId.' - '.print_r($row, true), \OCP\Util::DEBUG);
		$settings = array();
		if( $row ) {
			$settings['id'] = $row['id'];
			$settings['app_name'] = $row['app_name'];
			$settings['version'] = $this->getValueOrDefault($row,'version',1);
			$settings['controller_service_name'] = $this->getValueOrDefault($row,'controller_service_name','');
			$settings['template_service_name'] = $this->getValueOrDefault($row, 'template_service_name','');
			$settings['link'] = $this->getValueOrDefault( $row, 'link', '');
			$settings['enabled_groups'] = (isset($row['enabled_groups']) && $row['enabled_groups'] !== '') ? explode(':', $row['enabled_groups']) : array();
			$settings['special'] = $this->getValueOrDefault( $row, 'special', '');
			$settings['css_paths'] = $this->getValueOrDefault( $row, 'css_paths',array());
			$settings['js_paths'] = $this->getValueOrDefault( $row, 'js_paths',array());
			$settings['css_paths'] = (isset($row['css_paths']) && $row['css_paths'] !== '' )?explode(':', $row['css_paths']):array();
			$settings['js_paths'] = (isset($row['js_paths']) && $row['js_paths'] !== '' )?explode(':', $row['js_paths']):array();
		} 
		return  $settings;
	}
    
    /**
     * returns an array with enabled groups
     * 
     * @param string $wId
     * @return array
     */
    
    public function getEnabledGroups($wId) {
        $sql        = 'SELECT `enabled_groups` FROM `'.$this->table.'` WHERE `wid` LIKE ?';
        $query      = $this->db->prepareQuery($sql);
        $result     = $query->execute(array($wId));
        $row        = $result->fetchRow();
        if( isset($row['enabled_groups']) && $row['enabled_groups'] !== '' ) {
            return explode(':', $row['enabled_groups']);
        } else {
            return array();
        }
    }
    
    /**
     * 
     * @param string $wId
     * @param array $gIds
     * 
     * @return bool
     */

    public function updateEnabledGroups($wId, $gIds=array()) {
        $sql        = 'UPDATE `'.$this->table.'` SET `enabled_groups` = ? WHERE `wid` = ?';
        $wIds       = implode(':', $gIds);
        $query      = $this->db->prepareQuery($sql);
        return $query->execute(array($wIds, $wId));
    }
    
    /**
     * stores all settings for the widget in private member $settings
     * @param string $wId
     */
    
    private function getSettings($wId) {
    	if (!isset($this->settings)){
    		$this->settings = $this->getAllWidgetSettings($wId);
    		\OCP\Util::writeLog('Dashboard', 'query settings of '.$wId.' - '.print_r($this->settings, true), \OCP\Util::DEBUG);
    	}
    }
    
    
    
    /**
     * this function checks if the value is set and is not empty
     * 
     * @param array $row
     * @param string $key
     * @param mixed $default
     * 
     * returns the value of the index if exists otherwise $default
     */
    
    private function getValueOrDefault( $row, $key, $default ){
    	return(isset($row[$key]) && $row[$key] !== '')?$row[$key]:$default;
    }
}