<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 20.07.15
 * Time: 08:10
 */

\OC_Util::checkAdminUser();

/*
\OCP\Util::addStyle('templateeditor', 'settings-admin');
\OCP\Util::addScript('templateeditor', 'settings-admin');

$themes = \OCA\TemplateEditor\MailTemplate::getEditableThemes();
$editableTemplates = \OCA\TemplateEditor\MailTemplate::getEditableTemplates();
*/

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
$app                    = new \OCA\Dashboard\AppInfo\Application();
$container              = $app->getContainer();
/** @var $widgetManagementService \OCA\Dashboard\Services\WidgetManagementService */
$widgetManagementService= $container->query('WidgetManagementService');
$L10N = $container->query('L10N');

$groups = \OC_Group::getGroups();

$template = new \OCP\Template('dashboard', 'admin-settings');
$template->assign('availableWidgets', $widgetManagementService->getAvailable());
$template->assign('enabledWidgetGroups', $widgetManagementService->getEnabledWidgetGroups());
$template->assign('groups', $groups);

return $template->fetchPage();