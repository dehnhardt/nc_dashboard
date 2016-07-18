# nc_dashboard
Dashboard for Nextcloud - based on Work from Florian Steffens


## Install from git

Go to the apps folder of your Nextcloud instance and run the following command:

    git clone https://github.com/dehnhardt/nc_dashboard.git dashboard

## Install from zip
Download the zip. Unzip to the apps folder an then rename the folder to 'dashboard'

## API HowTo

currently we use the class Api_1_0.

The following things has to be done to contribute an widget to the dashboard from another app:

- Create a folder in your app root directory and name it 'widget'
- add two class files to this folder:
  * [yourapp]controller.php
  * [yourapp]template.php
  * you can find templates for the two files in the example_files folder
    - replace [yourapp] in the files with the technical name of your app (all lowercase)
    - replace [YourApp] in the files with the technical name of your app (mixed case if needed)
    - replace [yourappname] in the files with the descriptive name of your app (all lowercase)
- Modify your appinfo/application.php to register the controller and template
```php

/* don't forget the use statements */
use \OCA\[YourApp]\Widget\[YourApp]Controller;
use \OCA\[YourApp]\Widget\[YourApp]Template;
....
		$container->registerService('widget[YourApp]Controller', function(IContainer $c){
			return new OwnNoteController(
					$c->query('AppName'),
					$c->query('Request')
			);
		});'
		
		'$container->registerService('widget[YourApp]Template', function(IContainer $c){
			return new OwnNoteTemplate(
					$c->query('AppName'),
					$c->query('Request')
					);
		});
```
- Modify your appinfo/app.php to specify your widget parameters and call the api function
```php
/* instantiate an array */
$widget = array();  

/* 
*required* 
use the app-name if your app provides only one widget
use any other describing name if your app provides more than one widget*/
$widget['wId']                      = '[yourapp]'; 

/* 
*required* 
again the app name 
*/
$widget['appName']                  = '[yourapp]'; 

/* 
*required*
the registered name of the widget controller 
*/
$widget['controllerServiceName']    = 'widget[YourApp]Controller'; 

/* 
*required*
the registered name of the widget template 
*/
$widget['templateServiceName']      = 'widget[YourApp]Template'; 

/* 
*optional*, default: nothing
A "Joker" for later use... maybe...
*/
$widget['special']                  = '';

/*
*required*, default: 1
The version of YOUR widget, don't forget increment it if you change any of these parameters
In incremented version triggern an update of the stored values
*/
$widget['version']					= 1; 

/* 
*optional*, default: empty
this is the link which is behind the headline of your widget, you can either link to your app (use the full path: /index.php/apps/[yourapp]/) or to any other internal or external page 
No link is created if this parameter is omitted
*/
$widget['link']						= '/index.php/apps/ownnote/';

/*
*optional*, default: false
In the admin settings you can toggle the availability for each widget and each group. If this parameter is set to true, the widget is available for all groups, otherwise the admin has to enable the widget for each group manually
*/
$widget['enableDefault']				= true;

/*
*optional* default: empty
provide the pathes to aditional cascading style sheets as an array. 
*/
$widget['css']                      = array('one file');

/*
*optional* default: empty
provide the pathes to your javascript files as an array. . 
*/
$widget['js']                       = array('one file', 'another file');

/*end of parameters*/

$app = new \OCA\Dashboard\AppInfo\Application();
$container = $app->getContainer();

$api = $container->query('Api_1_0');
$api->addWidget($widget);
```
- If neither my nor you made any mistake, your widget should appear in the list of available widgets in the dashboard.
- otherwise open an issue at https://github.com/dehnhardt/nc_dashboard/issues
