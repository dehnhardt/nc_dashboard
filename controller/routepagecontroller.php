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
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoutePageController extends Controller {

    private $user;
    private $widgetCssAndJsService;
    private $eventDispatcher;

    public function __construct($appName, IRequest $request, $user, WidgetCssAndJsService $widgetCssAndJsService, EventDispatcherInterface $eventDispatcher){
        parent::__construct($appName, $request);
        $this->user                     = $user;
        $this->widgetCssAndJsService    = $widgetCssAndJsService;
        $this->eventDispatcher			= $eventDispatcher;
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
    	/*
    	\OCP\Util::writeLog( 'Dashboard', 'Dashboard: Before dispatch', \OCP\Util::DEBUG );
    	$this->eventDispatcher->dispatch('dashboard.callwidgets');
    	\OCP\Util::writeLog( 'Dashboard', 'Dashboard: After dispatch', \OCP\Util::DEBUG );
    	*/
    	$this->widgetCssAndJsService->loadAll();

        $params = array(
            'user'  => $this->user,
        );
        return new TemplateResponse('dashboard', 'main', $params);
    }

}