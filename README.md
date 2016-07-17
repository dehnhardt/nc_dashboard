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
-- [yourapp]controller.php
-- [yourapp]template.php
- you can find templates for the two files in the example_files folder
-- replace [yourapp] in the files with the technical name of your app (all lowercase)
-- replace [YourApp] in the files with the technical name of your app (mixed case if needed)
-- replace [yourappname] in the files with the descriptive name of your app (all lowercase)

