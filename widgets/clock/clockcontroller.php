<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Clock;

use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;
use OCP\Util;

class ClockController extends WidgetController implements IWidgetController {



    // interface needed methods ------------------------------------

    public function setBasicValues() {
        $this->icon     =  \OC::$server->getURLGenerator()->imagePath('dashboard', 'icons/5.png');
        $this->refresh  =                                            0;
        $this->wId      =                                      'clock';
        $this->name     =                      $this->L10N->t('Clock');
    }

    /**
     *
     * returns all the needed data as array
     * you can access them in the widgetTemplate->getContentHtml with $data['abc']
     *
     * @return array
     */
    public function getData() {
        return array(
            'date'  => $this->L10N->l('date', ''.time()),
            'clock' => $this->getConfig('clock', 'swissRail')
        );
    }


    // ajax call methods ---------------------------------------------

    // private services -------------------------------------------------

} 
