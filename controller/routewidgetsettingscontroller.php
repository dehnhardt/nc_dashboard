<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 27.04.15
 * Time: 20:56
 */

namespace OCA\Dashboard\Controller;


use OCA\Dashboard\Services\WidgetSettingsService;
use OCP\AppFramework\Controller;
use OCP\IL10N;
use OCP\IRequest;

class RouteWidgetSettingsController extends Controller {

    private $widgetSettingsService;
    private $L10N;

    public function __construct($appName, IRequest $request, $user, IL10N $l10n, WidgetSettingsService $widgetSettingsService){
        parent::__construct($appName, $request);
        $this->user                     = $user;
        $this->L10N                     = $l10n;
        $this->widgetSettingsService    = $widgetSettingsService;
    }

    /**
     *
     * returns the config from the db
     * if not found, default will be returned
     *
     * @param $wIId
     * @param $key
     * @return string
     */
    public function getConfig($wIId, $key, $default = null) {
        // TODO
        return 'one';
    }

    /**
     *
     * write a setting value in the db
     * returns true if successful
     *
     * @param $wIId
     * @param $key
     * @param $value
     * @return bool
     */
    public function setConfig($wIId, $key, $value) {
        $this->widgetSettingsService->setConfig($wIId, $key, $value);
    }

}