<?php
/**
* Created by PhpStorm.
* User: flost
* Date: 27.04.15
* Time: 20:29
*/

namespace OCA\Dashboard\Services;


use OCA\Dashboard\Db\WidgetsDAO;

class WidgetCssAndJsService {

    private $widgetManagementService;
    private $widgetsDAO;

    private $loadedStyles = array();
    private $loadedScripts= array();

    function __construct(WidgetManagementService $widgetManagementService, WidgetsDAO $widgetsDAO) {
        $this->widgetManagementService  = $widgetManagementService;
        $this->widgetsDAO               = $widgetsDAO;
    }

    public function loadAll() {
        /** @var $wIdsToLoadCss \OCA\Dashboard\Services\WidgetManagementService */
        $wIdsToLoad = $this->widgetManagementService->getAvailable();

        foreach ($wIdsToLoad as $wId) {
        	$appName = $this->widgetsDAO->getAppName($wId);
        	\OCP\Util::writeLog('dashboard', 'AppName: '.$appName." WID: ".$wId, \OCP\Util::DEBUG);
            $this->loadWidgetJs($wId, $appName);
            $this->loadWidgetCss($wId, $appName);
        }
    }

    public function loadWidgetCss($wId, $app) {
        $stylePaths = $this->widgetsDAO->getCssPaths($wId);

        foreach ($stylePaths as $stylePath) {
            // load only once
            if( !in_array($stylePath, $this->loadedStyles) ) {
                \OCP\Util::addStyle($app, '../'.$stylePath);
                $this->loadedStyles[]   = $stylePath;
            }
        }
    }

    public function loadWidgetJs($wId, $app) {
        $scriptPaths = $this->widgetsDAO->getJsPaths($wId);
                
        foreach ($scriptPaths as $scriptPath) {
            // load only once
            if( !in_array($scriptPath, $this->loadedStyles) ) {
            	\OCP\Util::addScript($app, '../'.$scriptPath);
            	$this->loadedScripts[]   = $scriptPath;
            }
        }
    }

    // ---- private methods --------------------------------------

}
