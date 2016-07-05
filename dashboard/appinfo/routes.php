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

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
$application = new Application();

$application->registerRoutes(
    $this,
    array(
        'routes' => array(
            // index
            array('name' => 'route_page#index',                              'url' => '/',                                      'verb' => 'GET'   ),

            // widget content
            array('name' => 'route_widget_content#get_complete',            'url' => '/widget/content/getComplete/{wiid}',      'verb' => 'GET'   ),
            array('name' => 'route_widget_content#get_content',             'url' => '/widget/content/getContent/{wiid}',       'verb' => 'GET'   ),
            array('name' => 'route_widget_content#call_method',             'url' => '/widget/content/callMethod',              'verb' => 'POST'  ),

            // widget settings
            array('name' => 'route_widget_settings#set_config',             'url' => '/widget/settings/setConfig',              'verb' => 'POST'  ),
            array('name' => 'route_widget_settings#get_config',             'url' => '/widget/settings/getConfig/{wIId}/{key}', 'verb' => 'GET'   ),

            // widget management
            array('name' => 'route_widget_management#get_enabled_widgets',  'url' => '/widget/management/enabled',              'verb' => 'GET'   ),
            array('name' => 'route_widget_management#get_available_widgets','url' => '/widget/management/available',            'verb' => 'GET'   ),
            array('name' => 'route_widget_management#get_basic_conf',       'url' => '/widget/management/basicConf/{wId}',      'verb' => 'GET'   ),
            array('name' => 'route_widget_management#add_new_instance',     'url' => '/widget/management/add/{wid}',            'verb' => 'PUT'   ),
            array('name' => 'route_widget_management#remove_instance',      'url' => '/widget/management/remove/{wiid}',        'verb' => 'DELETE'),
            array('name' => 'route_widget_management#enable_widget_group',  'url' => '/widget/management/enable/{wIdG}',        'verb' => 'PUT'   ),
            array('name' => 'route_widget_management#disable_widget_group', 'url' => '/widget/management/disable/{wIdG}',       'verb' => 'PUT'   ),

            // settings
            array('name' => 'route_settings#get_config',                    'url' => '/settings/{key}',                         'verb' => 'GET'   ),
            array('name' => 'route_settings#set_config',                    'url' => '/settings',                               'verb' => 'POST'  ),
        )
    )
);
