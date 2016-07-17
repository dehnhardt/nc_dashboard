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

1. Create a folder in your app root directory and name it 'widget'
2. add two class files to this folder:
  * [yourapp]controller.php
  * [yourapp]template.php
  * you can find templates for the two files in the example_files folder
    - replace [yourapp] in the files with the technical name of your app (all lowercase)
    - replace [YourApp] in the files with the technical name of your app (mixed case if needed)
    - replace [yourappname] in the files with the descriptive name of your app (all lowercase)

3. Modify your appinfo/application.php to register the controller and template

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

4. Modify your appinfo/app.php to specify your widget parameters and call the api function

```
/* instantiate an array */
$widget = array();  

/* *required* use the app-name if you provide only one widget */
$widget['wId']                      = '[yourapp]'; 
$widget['appName']                  = '[yourapp]'; /* again the app name */
$widget['controllerServiceName']    = 'widget[YourApp]Controller'; /* the registered name of the widget controller */ 
$widget['templateServiceName']      = 'widget[YourApp]Template'; /* the registered name of the widget template */
$widget['special']                  = ''; /* A "Joker"vfor later use... */
$widget['version']					= 2; /* The version of YOUR widget, increment it if you change any of this parameters */
$widget['link']						= '/index.php/apps/ownnote/'; /* this is the link which is behind the headline of your widget, you can either link to your app (use the full path: /index.php/apps/[yourapp]/) or to any other internal or external page */
$widget['enableDefault']			= true;
$widget['css']                      = array();
$widget['js']                       = array(\OC::$server->getURLGenerator()->linkTo('ownnote', 'widgets/script'));

$app = new \OCA\Dashboard\AppInfo\Application();
$container = $app->getContainer();

$api = $container->query('Api_1_0');
$api->addWidget($widget);
```
