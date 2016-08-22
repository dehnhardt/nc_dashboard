<?php

/**
 * ownCloud - dashboard
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Florian Steffens <webmaster@freans.de>
 * @author Holger Dehnhardt <holger@dehnhardt.org>
 * @copyright Florian Steffens 2014, Holger Dehnhardt 2016
 */
namespace OCA\Dashboard\AppInfo;

use OCP\AppFramework\App;

\OCP\Util::writeLog( 'Dashboard', 'Dashboard: app.php', \OCP\Util::DEBUG );
$app = new App( 'dashboard' );
$container = $app->getContainer();

\OCP\Util::writeLog( 'Dashboard', 'Dashboard: app.php #1', \OCP\Util::DEBUG );

$container->query( 'OCP\INavigationManager' )->add( function () use ($container) {
	$urlGenerator = $container->query( 'OCP\IURLGenerator' );
	$l10n = $container->query( 'OCP\IL10N' );
	return [
			// the string under which your app will be referenced in Nextcloud
			'id' => 'dashboard',
			
			// sorting weight for the navigation. The higher the number, the higher
			// will it be listed in the navigation
			'order' => 10,
			
			// the route that will be shown on startup
			'href' => $urlGenerator->linkToRoute( 'dashboard.route_page.index' ),
			
			// the icon that will be shown in the navigation
			// this file needs to exist in img/
			'icon' => $urlGenerator->imagePath( 'dashboard', 'dashboard.svg' ),
			
			// the title of your application. This will be used in the
			// navigation or on the settings page of your app
			'name' => $l10n->t( 'Dashboard' ) 
	];
} );

\OCP\Util::writeLog( 'Dashboard', 'Dashboard: app.php #2', \OCP\Util::DEBUG );

// register admin settings in the owncloud settings menu
\OCP\APP::registerAdmin( 'dashboard', 'admin-settings' );

\OCP\Util::writeLog( 'Dashboard', 'Dashboard: app.php #3', \OCP\Util::DEBUG );

// \OCA\Dashboard\utils\Helper::registerHooks ();

\OCP\Util::writeLog( 'Dashboard', 'Dashboard: app.php #4', \OCP\Util::DEBUG );

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

/*
 * $widgets = array();
 *
 * // add dummy widget
 * $widget = array();
 * $widget['wId'] = 'dummy';
 * $widget['appName'] = 'dashboard';
 * $widget['controllerServiceName'] = 'widgetDummyController';
 * $widget['templateServiceName'] = 'widgetDummyTemplate';
 * $widget['css'] = array(
 * 'widgets'.DIRECTORY_SEPARATOR.'dummy'.DIRECTORY_SEPARATOR.'style'
 * );
 * $widget['js'] = array(
 * 'widgets'.DIRECTORY_SEPARATOR.'dummy'.DIRECTORY_SEPARATOR.'script'
 * );
 * $widget['enableDefault'] = true;
 * $widget['version'] = 3;
 * $widgets[] = $widget;
 *
 * // add clock widget
 * $widget = array();
 * $widget['wId'] = 'clock';
 * $widget['appName'] = 'dashboard';
 * $widget['controllerServiceName'] = 'widgetClockController';
 * $widget['templateServiceName'] = 'widgetClockTemplate';
 * $widget['css'] = array(
 * 'widgets'.DIRECTORY_SEPARATOR.'clock'.DIRECTORY_SEPARATOR.'style'
 * );
 * $widget['js'] = array(
 * 'widgets'.DIRECTORY_SEPARATOR.'clock'.DIRECTORY_SEPARATOR.'script'
 * );
 * $widget['enableDefault'] = true;
 * $widget['version'] = 3;
 * $widgets[] = $widget;
 *
 * // add bitcoin widget
 * $widget = array();
 * $widget['wId'] = 'bitcoin';
 * $widget['appName'] = 'dashboard';
 * $widget['version'] = 2;
 * $widget['controllerServiceName'] = 'widgetBitcoinController';
 * $widget['templateServiceName'] = 'widgetBitcoinTemplate';
 * $widget['link'] = 'https://www.bitstamp.net';
 * $widget['css'] = array(
 * 'widgets'.DIRECTORY_SEPARATOR.'bitcoin'.DIRECTORY_SEPARATOR.'style'
 * );
 * $widget['js'] = array(
 * 'widgets'.DIRECTORY_SEPARATOR.'bitcoin'.DIRECTORY_SEPARATOR.'script'
 * );
 * $widget['enableDefault'] = true;
 * $widget['version'] = 3;
 * $widgets[] = $widget;
 *
 * // add iframe widget
 * $widget = array();
 * $widget['wId'] = 'iframe';
 * $widget['appName'] = 'dashboard';
 * $widget['controllerServiceName'] = 'widgetIframeController';
 * $widget['templateServiceName'] = 'widgetIframeTemplate';
 * $widget['css'] = array(
 * 'widgets'.DIRECTORY_SEPARATOR.'iframe'.DIRECTORY_SEPARATOR.'style'
 * );
 * $widget['js'] = array(
 * 'widgets'.DIRECTORY_SEPARATOR.'iframe'.DIRECTORY_SEPARATOR.'script'
 * );
 * $widget['enableDefault'] = true;
 * $widget['version'] = 3;
 * $widgets[] = $widget;
 *
 */

\OCP\Util::writeLog( 'Dashboard', 'Dashboard: app.php #5', \OCP\Util::DEBUG );

// ============================================
