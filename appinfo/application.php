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

namespace OCA\Dashboard\AppInfo;



use OC;
use OCA\Dashboard\Api\Api_1_0;
use OCA\Dashboard\Controller\RoutePageController;
use OCA\Dashboard\Controller\RouteWidgetContentController;
use OCA\Dashboard\Controller\RouteWidgetManagementController;
use OCA\Dashboard\Controller\RouteWidgetSettingsController;
use OCA\Dashboard\Db\WidgetConfigDAO;
use OCA\Dashboard\Db\WidgetHashDAO;
use OCA\Dashboard\Db\WidgetsDAO;
use OCA\Dashboard\Services\WidgetCssAndJsService;
use OCA\Dashboard\Services\WidgetManagementService;
use OCA\Dashboard\Services\WidgetSettingsService;
use OCA\Dashboard\Services\WidgetContentService;
use OCA\Dashboard\Services\WidgetHashService;
use OCA\Dashboard\Widgets\Bitcoin\BitcoinController;
use OCA\Dashboard\Widgets\Bitcoin\BitcoinTemplate;
use OCA\Dashboard\Widgets\Clock\ClockController;
use OCA\Dashboard\Widgets\Clock\ClockTemplate;
use OCA\Dashboard\Widgets\Dummy\DummyController;
use OCA\Dashboard\Widgets\Dummy\DummyTemplate;
use OCA\Dashboard\Widgets\Iframe\IframeController;
use OCA\Dashboard\Widgets\Iframe\IframeTemplate;
use \OCP\AppFramework\App;
use \OCP\IContainer;


class Application extends App {


	public function __construct (array $urlParams=array()) {
		parent::__construct('dashboard', $urlParams);

		$container = $this->getContainer();


		/**
		 * Route Controllers
		 */
        $container->registerService('RoutePageController', function(IContainer $c) {
            return new RoutePageController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('UserId'),
                $c->query('WidgetCssAndJsService')
            );
        });

        $container->registerService('RouteWidgetManagementController', function(IContainer $c) {
            return new RouteWidgetManagementController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('UserId'),
                $c->query('WidgetManagementService'),
                $c->query('L10N')
            );
        });

        $container->registerService('RouteWidgetContentController', function(IContainer $c) {
            return new RouteWidgetContentController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('UserId'),
                $c->query('L10N'),
                $c->query('WidgetContentService')
            );
        });

        $container->registerService('RouteWidgetSettingsController', function(IContainer $c) {
            return new RouteWidgetSettingsController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('UserId'),
                $c->query('L10N'),
                $c->query('WidgetSettingsService')
            );
        });


        /**
         *  Services
         */
        $container->registerService('WidgetContentService', function(IContainer $c) {
            return new WidgetContentService(
                $c->query('UserId'),
                $c->query('L10N'),
                $c->query('WidgetManagementService'),
                $c->query('WidgetHashService')
            );
        });

        $container->registerService('WidgetManagementService', function(IContainer $c) {
            return new WidgetManagementService(
                $c->query('UserId'),
                $c->query('L10N'),
                $c->query('WidgetSettingsService'),
                $c->query('WidgetsDAO'),
                $c->query('dateTimeFormatter'),
                $c->query('WidgetHashService'),
                $c->query('GroupManager'),
                $c->query('UserManager')
            );
        });

        $container->registerService('WidgetHashService', function(IContainer $c) {
            return new WidgetHashService(
                $c->query('WidgetHashDAO'),
                $c->query('UserId')
            );
        });

        $container->registerService('WidgetSettingsService', function(IContainer $c) {
            return new WidgetSettingsService(
                $c->query('WidgetConfigDAO'),
                $c->query('UserId'),
                $c->query('WidgetsDAO')
            );
        });

        $container->registerService('WidgetCssAndJsService', function(IContainer $c) {
            return new WidgetCssAndJsService(
                $c->query('WidgetManagementService'),
                $c->query('WidgetsDAO')
            );
        });


        /**
         * DAO
         */
        $container->registerService('WidgetHashDAO', function(IContainer $c) {
            return new WidgetHashDAO(
                $c->query('ServerContainer')->getDb()
            );
        });

        $container->registerService('WidgetConfigDAO', function(IContainer $c) {
            return new WidgetConfigDAO(
                $c->query('ServerContainer')->getDb()
            );
        });

        $container->registerService('WidgetsDAO', function(IContainer $c) {
            return new WidgetsDAO(
                $c->query('ServerContainer')->getDb()
            );
        });


        /**
		 * Core
		 */
        $container->registerService('L10N', function(IContainer $c) {
            return $c->query('ServerContainer')->getL10N($c->query('AppName'));
        });

        $container->registerService('dateTimeFormatter', function() {
            /** @noinspection PhpUndefinedClassInspection */
            return OC::$server->query('DateTimeFormatter');
        });

        $container->registerService('UserManager', function($c) {
            return $c->query('ServerContainer')->getUserManager();
        });
        $container->registerService('GroupManager', function($c) {
            return $c->query('ServerContainer')->getGroupManager();
        });


        /**
         * API
         */
        $container->registerService('Api_1_0', function(IContainer $c) {
            return new Api_1_0(
                $c->query('UserId'),
                $c->query('WidgetManagementService')
            );
        });


        /**
         * included widgets
         */
        // dummy widget
        $container->registerService('widgetDummyController', function(IContainer $c) {
            return new dummyController(
                $c->query('AppName'),
                $c->query('Request')
            );
        });
        $container->registerService('widgetDummyTemplate', function(IContainer $c) {
            return new dummyTemplate(
                $c->query('AppName'),
                $c->query('Request')
            );
        });

        // clock widget
        $container->registerService('widgetClockController', function(IContainer $c) {
            return new clockController(
                $c->query('AppName'),
                $c->query('Request')
            );
        });
        $container->registerService('widgetClockTemplate', function(IContainer $c) {
            return new clockTemplate(
                $c->query('AppName'),
                $c->query('Request')
            );
        });

        // bitcoin widget
        $container->registerService('widgetBitcoinController', function(IContainer $c) {
            return new bitcoinController(
                $c->query('AppName'),
                $c->query('Request')
            );
        });
        $container->registerService('widgetBitcoinTemplate', function(IContainer $c) {
            return new bitcoinTemplate(
                $c->query('AppName'),
                $c->query('Request')
            );
        });

        // iframe widget
        $container->registerService('widgetIframeController', function(IContainer $c) {
            return new iframeController(
                $c->query('AppName'),
                $c->query('Request')
            );
        });
        $container->registerService('widgetIframeTemplate', function(IContainer $c) {
            return new iframeTemplate(
                $c->query('AppName'),
                $c->query('Request')
            );
        });
	}


}