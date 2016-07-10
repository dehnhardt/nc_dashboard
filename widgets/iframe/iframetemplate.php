<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Iframe;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class IframeTemplate extends WidgetTemplate implements IWidgetTemplate {

    function getContentHtml($data = array()) {
        return '<iframe src="'.$this->p($data['url']).'" class="iframe"></iframe>';
    }

    function getSettingsArray()
    {
        return array(
            'url'  => array(
                'type'      => 'text',
                'default'   => '',
                'name'      => 'URL'
            ),
            'title'  => array(
                'type'      => 'text',
                'default'   => 'iFrame',
                'name'      => 'Widget title',
                'info'      => 'Refresh to apply changes.'
            )
        );
    }

    public function getLicenseInfo() {
        return '';
    }
}