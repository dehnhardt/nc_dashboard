<?php
/**
* Created by PhpStorm.
* User: flost
* Date: 27.04.15
* Time: 20:29
*/

namespace OCA\Dashboard\Services;


use OC_Util;
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
            $this->loadWidgetJs($wId);
            $this->loadWidgetCss($wId);
        }
    }

    public function loadWidgetCss($wId) {
        $stylePaths = $this->widgetsDAO->getCssPaths($wId);

        foreach ($stylePaths as $stylePath) {
            // load only once
            if( !in_array($stylePath, $this->loadedStyles) ) {
                OC_Util::$styles[]      = $stylePath;
                $this->loadedStyles[]   = $stylePath;
            }
        }
    }

    public function loadWidgetJs($wId) {
        $scriptPaths = $this->widgetsDAO->getJsPaths($wId);

        foreach ($scriptPaths as $scriptPath) {
            // load only once
            if( !in_array($scriptPath, $this->loadedStyles) ) {
                OC_Util::$scripts[]      = $scriptPath;
                $this->loadedScripts[]   = $scriptPath;
            }
        }
    }


    // ---- private methods --------------------------------------

}
