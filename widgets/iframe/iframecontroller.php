<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Iframe;

use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;
use OCP\Util;

class IframeController extends WidgetController implements IWidgetController {



    // interface needed methods ------------------------------------

    public function setBasicValues() {
        $this->icon     = \OC::$server->getURLGenerator()->imagePath('dashboard', 'icons/71.png');
        $this->refresh  =                                            0;
        $this->wId      =                                     'iframe';
        $this->name     =                     $this->L10N->t('iFrame');
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
            'url'      => $this->getConfig('url')
        );
    }

    public function getName() {
        //return $this->L10N->t( $this->getConfig('title', 'iFrame') )->__toString();
	return '';
    }

    // ajax call methods ---------------------------------------------

    // private services -------------------------------------------------

} 
