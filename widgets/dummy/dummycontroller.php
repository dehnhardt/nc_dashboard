<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Dummy;

use OC;
use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\status;
use OCA\Dashboard\Widgets\WidgetController;

class DummyController extends WidgetController implements IWidgetController {


    // interface needed methods ------------------------------------

    public function setBasicValues() {    	
        $this->icon     =  \OC::$server->getURLGenerator()->imagePath('dashboard', 'icons/9.png');
        $this->refresh  =                                           30;
        $this->wId      =                                      'dummy';
        $this->name     =                      $this->L10N->t('Dummy');
    }

    /**
     *
     * return values as array as parameter for the template
     * always return
     *
     * @return array
     */
    public function getData() {
        return array(
            'wIId'      => $this->getConfig('wIId'),
            'values'    => array(
                'valueOne'  =>  $this->getConfig('settingOne', 'test'),
                'valueTwo'  =>  $this->getConfig('settingTwo')
            ),
            'time'      => time()
        );
    }


    // ajax call methods ---------------------------------------------

    /**
     *
     * ajax example
     *
     * @param $status
     * @return mixed
     */
    public function generateStatus( $status ) {
        return $status;
    }

    public function countUp( $oldValue ) {
        return array(
            'counter'   => (intval($oldValue) + 1),
            'time'      => $this->dateTimeFormatter->formatTime(time())
        );
    }


    // private services -------------------------------------------------

} 
