<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Clock;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class ClockTemplate extends WidgetTemplate implements IWidgetTemplate {

    function getContentHtml($data = array()) {
        return '<canvas class="CoolClock:'.$this->p($data['clock']).':70" width="100" height="100" style="width: 100px; height: 100px; "></canvas>
                <script>CoolClock.findAndCreateClocks();</script><br><div class="date hoverInfo" data-opacitynormal="0.5">'.$this->p($data['date']).'</div>';
    }

    function getSettingsArray()
    {
        return array(
            'clock'     => array(
                'type'          => 'select',
                'options'       => array(
                    'swissRail'             => 'swiss rail',
                    'chunkySwiss'           => 'chunky swiss',
                    'fancy'                 => 'fancy',
                    'machine'               => 'machine',
                    'simonbaird_com'        => 'simonbaird.com',
                    'classic'               => 'classic',
                    'modern'                => 'modern',
                    'simple'                => 'simple',
                    'securephp'             => 'securephp',
                    'Tes2'                  => 'Tes2',
                    'Lev'                   => 'Lev',
                    'Sand'                  => 'Sand',
                    'Sun'                   => 'Sun',
                    'Tor'                   => 'Tor',
                    'Cold'                  => 'Cold',
                    'Babosa'                => 'Babosa',
                    'Tumb'                  => 'Tumb',
                    'Stone'                 => 'Stone',
                    'Disc'                  => 'Disc',
                    'watermelon'            => 'Watermelon'
                ),
                'name'          => 'Clock',
                'default'       => 'swissRail',
                'info'          => 'Refresh to apply changes.'
            )
        );
    }

    public function getLicenseInfo() {
        return 'This widget uses scripts from http://randomibis.com/coolclock/.<br>For more details look at the license from http://randomibis.com/coolclock/.';
    }

}