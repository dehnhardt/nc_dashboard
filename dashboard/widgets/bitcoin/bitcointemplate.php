<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Bitcoin;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class BitcoinTemplate extends WidgetTemplate implements IWidgetTemplate
{

    function getContentHtml($data = array()) {
        // if problem exists
        if( isset($data['msg']) ) {
            return '<div class="msg">'.$data['msg'].'</div>';
        }

        return '
            <div class="last">'.$this->p($data['last']).' USD</div>
            <div class="high"><span class="icon-triangle-n">&nbsp;&nbsp;&nbsp;</span>&nbsp;'.$this->p($data['high']).' USD</div>
            <div class="low"><span class="icon-triangle-s">&nbsp;&nbsp;&nbsp;</span>&nbsp;'.$this->p($data['low']).' USD</div>
        ';
    }

    function getSettingsArray() {
        return array();
    }

    public function getLicenseInfo() {
        return 'Data are fetched from https://www.bitstamp.net, please look there for more information.';
    }

}
