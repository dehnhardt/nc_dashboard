<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Bitcoin;

use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\Status;
use OCA\Dashboard\Widgets\WidgetController;
use OCP\Util;

class BitcoinController extends WidgetController implements IWidgetController {

    private $json;
    private $url = "https://www.bitstamp.net/api/ticker/";

    // interface needed methods ------------------------------------

    public function setBasicValues() {    	
    	$this->icon     = \OC::$server->getURLGenerator()->imagePath('dashboard', 'icons/201.png');
        $this->refresh  =                                          360;
        $this->wId      =                                    'bitcoin';
        $this->name     =                    $this->L10N->t('Bitcoin');
    }

    /**
     *
     * returns all the needed data as array
     * you can access them in the widgetTemplate->getContentHtml with $data['abc']
     *
     * @return array
     */
    public function getData() {
        $data = array();
        if($this->getJSON()) {
            $data = array(
                'last'      => $this->json->last,
                'low'       => $this->json->low,
                'high'      => $this->json->high,
            );
            $this->status = Status::STATUS_OKAY;
        } else {
            $this->status = Status::STATUS_PROBLEM;
            $data['msg'] = $this->L10N->t('The API response was wrong.');
        }
        return $data;
    }


    // ajax call methods ---------------------------------------------

    // private services -------------------------------------------------

    /**
	 * loads the json data from Bitstamp
     *
     * @return bool
	 */
    private function getJSON() {
        $con = @file_get_contents($this->url);
        if($con != '') {
            $this->json = json_decode($con);
            return true;
        } else {
            \OCP\Util::writeLog('dashboard',"Bitcoin price could not be loaded.", \OCP\Util::WARN);
            return false;
        }
    }
} 
