<?php
/**
 * ownCloud - dashboard
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Florian Steffens <webmaster@freans.de>
 * @copyright Florian Steffens 2014
 */

namespace OCA\Dashboard\Controller;

use OCA\Dashboard\Services\WidgetCssAndJsService;
use \OCP\IRequest;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCP\AppFramework\Controller;


class RoutePageController extends Controller {

    private $user;
    private $widgetCssAndJsService;

    public function __construct($appName, IRequest $request, $user, WidgetCssAndJsService $widgetCssAndJsService){
        parent::__construct($appName, $request);
        $this->user                     = $user;
        $this->widgetCssAndJsService    = $widgetCssAndJsService;
    }

    /**
     *
     * main index
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
    	\OCP\Util::writeLog('dashboard', '**** call: index', \OCP\Util::DEBUG);
        $this->widgetCssAndJsService->loadAll();

        $params = array(
            'user'  => $this->user,
        );
        return new TemplateResponse('dashboard', 'main', $params);
    }

}