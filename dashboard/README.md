# Dashboard
This app lets you organize widgets. Each widget present some basic information about one topic.
You can create as much widgets as you want to. Each widgets have its own settings area.

    WANTED
    I'm looking for help in JS.
    If you know how js works and like to help,
    please contact me.

## depedencies
* PHP 5.4 (tested with 5.5)
* Database (sqlite, postgresql and mysql tested)
* owncloud >= 7.0.4
* owncloud <= 8.0.2
* **no** Internet Explorer Support (maybe one day)

## translations
The dashboard app is connected to the transiflex translation system.
If you want to help, go there and add some translations.

## support
If you have an idea for this app, new widgets or a question, please create an issue at github.
https://github.com/fjies/dashboard

## new widget
Everybody is welcome to develop some new widgets. Please send them via mail or pull-message. I will check them and add if they look good.
If you plan to develop a new widget, please read the 'planed function -> integrated widgets' part first.

## maintainers
* [Florian Steffens](https://github.com/fjies)

# install or update
To install place this app in **owncloud/apps/** and activate it in the app menu.
If you want to update from a version < 1.5 please deactivate the old one and remove it from the apps folder.
Additionally remove the database table *dbprefix*ocDashboard_usedHashs or *dbprefix*ocDashboard_used_hashs.

### update from 1.6.2
If you like to update from version 1.6.2 to a higher one, you have to rename a table:
* rename table '*dbprefix*dashboard_usedHashs' to '*dbprefix*dashboard_used_hashs'
* rename column 'usedHash' to 'used_hash' in *dbprefix*dashboard_used_hashs
* rename column 'wIId' to 'wiid' in *dbprefix*dashboard_used_hashs
* rename colum 'wId' to 'wid' in *dbprefix*dashboard_config
* rename colum 'wNo' to 'wno' in *dbprefix*dashboard_config
  
# known bugs
* tasks can not be set as done (technical problem)

# planed functions

## integrated widgets
It is planed, that every owncloud app can register an widget at the dashboard app.
The dashboard will call the registered classes directly from the app and include the widget.
This means the app-developers can support their widget on their own.

    // -------------------------------------------
    // add the weather widget to the dashboard app
    // ===========================================
    //
    // $wId is the identifier of the widget and must be unique
    // $controllerClass is the full class name for the widget controller
    // $templateClass is the full class name for the widget template
    // $special can be some special information about that widget
    //
    
    $wId                    = 'weather';
    $appName                = 'weather';
    $controllerServiceName  = 'WidgetWeatherController';
    $templateServiceName    = 'WidgetWeatherTemplate';
    $special                = '';
    $app                    = new \OCA\Dashboard\AppInfo\Application();
    $container              = $app->getContainer();
    $api                    = $container->query('Api_1_0');
    $api->addWidget($wId, $appName, $controllerServiceName, $templateServiceName, $special);

# developer information

## IDs
    wIId = wId + '-' + wNo
    for example tasks-2

* widget instance id (wIId)
* widget id (wId)
* number of instance of a widget (wNo)
* spoken name of a widget (wName)


    wIdG = wId + '-' + gId
    for example dummy-0 or weather-all
    
* group id (gId)
* group id 'all' is for all groups

## folder structure
Each widget has its own folder inside **owncloud/apps/dashboard/widgets/**. 
All files that belongs to this widget are inside this folder.
The widget folder must have the following files:

* script.js
* style.css
* [widget name]controller.php
* [widget name]template.php

## javascript
Each widget has its own javascript file 'script.js'. This will be always loaded.

### callback for widget refresh
If your widget has a refresh interval, it will automatically refreshed (only the content part).
You can register a callback function for each wId. This will be called at the end of the refresh process.

    dashboard.widgetCallback['[wId]'] = function() {
                alert('callback news');
            };

### call widget controller method
You can call method inside the widget controller method like this:

    var call = dashboard.callWidgetMethod(wIId, '[method name]', '[value]');
    call.success(function (response) {
        // response.success is the value that is returned by the method you called
        alert(response.success);
    });

## css
You can define your special styles in this file. Please start always with '#widgets .widget.[wId]'.

## widget controller
The widget controller is a typical controller that extends WidgetController and has to implement the IWidgetController interface.
You have to set the namespace for your widget.
Methods, that have to be implemented are getData() and setData().

The setData()-method sets the path to a icon, the interval to refresh widget information (set 0 for no refreshing), the wId and the spoken name of the widget. The name ist mostly the headline of the widget.
The headline can be linked, therefore set a link-address in the setData()-method like $this->link = '[link-address]';.
If you want to specify the headline, you can implement the method getName() that returns a string. This method will be automatically called in the widget template object.

The getData()-method collects all the data that are needed in the template as an array and returns it. Please insert always the $this->setHash($data); line at the end to enable the hash management for that widget.

### encrypted config values
Some values (for example passwords) should be encrypted stored in the database. All values with the key 'password' will be automatically encrypted. You can add your own config-key for encryption. You just have to add your key like this (inside the widget controller object) $this->encryptAttributes[] = [key].
 
 
## widget template
This class is a template generation-tool for widgets. It extends the WidgetTemplate class and implements the IWidgetTemplate. There are two methods that have to be implemented (getContentHtml and getSettingsArray).

### content
The getContentHtml($data = array())-method create the html-code for the content part of a widget. You can use the data-array, that has been specified in the getData()-method in the widgetController object.

To avoid XSS and other security stuff you have to pass ALL strings and values through the p([string])-method. This is like you are using p() inside a owncloud-app-template.
    
    $this->p([string]);

If you have already html code, you can print it with the following, but try to avoid that.

    $this->print_unescaped([string]);

### settings
The getSettingsArray()-method returns an array with all the widget-specific settings. The template object will parse this automatically.
You can choose one of the following types for a settings item:

* text (simple input field)
* password (password field, will be encrypted in the database)
* select (select field with specified options)

For text and password types the array entry should look like this ('info' is optional):
    
    '[id]' => array(
       'type'    => '[text or password]',
       'default' => '[some text]',
       'name'    => '[text that will display]',
       'info'    => '[some info text]'
    ),
    
For type select use this array:

    '[id]' => array(
       'type'   => 'select',
       'options'=> array(
          '[option value]'  => '[option name]',
          ...
       ),
       'default' => '[some text]',
       'name'    => '[text that will display]',
       'info'    => '[some info text]'
    ),
    
### license info
You can implement the getLicenseInfo()-method to return a string with some additional information that will be displayed below the settings.
    
### hoverInfo
You can set the 'hoverInfo' css class to items, each item will be hidden normally and displayed if the mouse hovers the widget.
Additionally you can specify both or just one data-attribute. The normal opacity can be set with data-opacitynormal="[0<x<1]" and the opacity while the mouse hovers the widget can set with data-opacityhover="[0<x<1]". 

### configs and basic values
$data or values from a widget are all content and config values together. The basicConf are just a few static values. If you add some dynamic values, you get the basicValues.