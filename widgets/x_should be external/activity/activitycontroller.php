<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Activity;

use OCA\Activity\Api;
use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;
use OCP\App;

class ActivityController extends WidgetController implements IWidgetController {



    // interface needed methods ------------------------------------


    /**
     * see IWidgetController interface
     */
    public function setData() {
        $this->icon     =             'icons/87.png';
        $this->refresh  =                        120;
        $this->wId      =                 'activity';
        $this->name     = $this->l10n->t('Activity');
    }

    /**
     *
     * returns all the needed data as array
     * you can access them in the widgetTemplate->getContentHtml with $data['abc']
     *
     * @return array
     */
    public function getData() {
        if( $this->checkDependencies() ) {
            $data = array(
                'activities'    => $this->getActivities(),
                'maxStringLen'  => $this->getConfig('maxStringLen', '40'),
                'startStringLen'=> $this->getConfig('startStringLen', '5')
            );
        } else {
            $data = array(
                'msg'           => $this->l10n->t('Activity app must be enabled.')
            );
        }

        // do not remove the following line
        // it creates the status information
        $this->setHash($data);
        return $data;
    }


    // ajax call methods ---------------------------------------------


    // private services -------------------------------------------------

    private function checkDependencies() {
        return App::isEnabled('activity');
    }

    private function getActivities() {
        $activitiesApp = Api::get(null);
        $activities = $activitiesApp->getData();
        return $activities;
    }
} 