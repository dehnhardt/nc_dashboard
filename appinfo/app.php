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


use OCP\App;

\OC::$server->getNavigationManager()->add(array(
    // the string under which your app will be referenced in owncloud
    'id' => 'dashboard',

    // sorting weight for the navigation. The higher the number, the higher
    // will it be listed in the navigation
    'order' => 10,

    // the route that will be shown on startup
    'href' => \OC::$server->getURLGenerator()->linkToRoute('dashboard.route_page.index'),

    // the icon that will be shown in the navigation
    // this file needs to exist in img/
    'icon' => \OC::$server->getURLGenerator()->imagePath('dashboard', 'dashboard.svg'),

    // the title of your application. This will be used in the
    // navigation or on the settings page of your app
    'name' => \OC::$server->getL10N('dashboard')->t('Dashboard') 
));


// register admin settings in the owncloud settings menu
App::registerAdmin('dashboard', 'admin-settings');




// -------------------------------------------
// add a widget to the dashboard app
// ===========================================
//
// $wId is the identifier of the widget and must be unique
// $controllerClass is the full class name for the widget controller
// $templateClass is the full class name for the widget template
// $special can be some special information about that widget
// $css add any path to css file without suffix to the array
// $js add any path to js file without suffix to the array
//
$widgets = array();

// add dummy widget
$widget = array();
$widget['wId']                      = 'dummy';
$widget['appName']                  = 'dashboard';
$widget['controllerServiceName']    = 'widgetDummyController';
$widget['templateServiceName']      = 'widgetDummyTemplate';
$widget['css']                      = array(
    'widgets'.DIRECTORY_SEPARATOR.'dummy'.DIRECTORY_SEPARATOR.'style'
);
$widget['js']                       = array(
    'widgets'.DIRECTORY_SEPARATOR.'dummy'.DIRECTORY_SEPARATOR.'script'
);
$widget['enableDefault']			= true;
$widget['version']					= 3;
$widgets[] = $widget;

// add clock widget
$widget = array();
$widget['wId']                      = 'clock';
$widget['appName']                  = 'dashboard';
$widget['controllerServiceName']    = 'widgetClockController';
$widget['templateServiceName']      = 'widgetClockTemplate';
$widget['css']                      = array(
    'widgets'.DIRECTORY_SEPARATOR.'clock'.DIRECTORY_SEPARATOR.'style'
);
$widget['js']                       = array(
    'widgets'.DIRECTORY_SEPARATOR.'clock'.DIRECTORY_SEPARATOR.'script'
);
$widget['enableDefault']			= true;
$widget['version']					= 3;
$widgets[] = $widget;

// add bitcoin widget
$widget = array();
$widget['wId']                      = 'bitcoin';
$widget['appName']                  = 'dashboard';
$widget['version']					= 2;
$widget['controllerServiceName']    = 'widgetBitcoinController';
$widget['templateServiceName']      = 'widgetBitcoinTemplate';
$widget['link']						= 'https://www.bitstamp.net';
$widget['css']                      = array(
    'widgets'.DIRECTORY_SEPARATOR.'bitcoin'.DIRECTORY_SEPARATOR.'style'
);
$widget['js']                       = array(
    'widgets'.DIRECTORY_SEPARATOR.'bitcoin'.DIRECTORY_SEPARATOR.'script'
);
$widget['enableDefault']			= true;
$widget['version']					= 3;
$widgets[] = $widget;

// add iframe widget
$widget = array();
$widget['wId']                      = 'iframe';
$widget['appName']                  = 'dashboard';
$widget['controllerServiceName']    = 'widgetIframeController';
$widget['templateServiceName']      = 'widgetIframeTemplate';
$widget['css']                      = array(
    'widgets'.DIRECTORY_SEPARATOR.'iframe'.DIRECTORY_SEPARATOR.'style'
);
$widget['js']                       = array(
    'widgets'.DIRECTORY_SEPARATOR.'iframe'.DIRECTORY_SEPARATOR.'script'
);
$widget['enableDefault']			= true;
$widget['version']					= 3;
$widgets[] = $widget;

// ----- register widgets to dashboard
/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
$app                    = new \OCA\Dashboard\AppInfo\Application();
$container              = $app->getContainer();
/** @var $api \OCA\Dashboard\Api\Api_1_0 */
$api                    = $container->query('Api_1_0');
foreach ($widgets as $widget) {
    $api->addWidget($widget);
}
// ============================================
