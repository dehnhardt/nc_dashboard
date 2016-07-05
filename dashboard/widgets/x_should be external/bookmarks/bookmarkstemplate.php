<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Bookmarks;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class BookmarksTemplate extends WidgetTemplate implements IWidgetTemplate {

    function getContentHtml($data = array()) {
        // if problem exists
        if( isset($data['msg']) ) {
            return '<div class="msg">'.$data['msg'].'</div>';
        }

        $html  = '';
        $html .= '<table>';
        foreach ($data['bookmarks'] as $bookmark) {
            $html .= '<tr><td><a target="_blank" href="'.$this->p($bookmark['url']).'" title="'.$this->p($bookmark['url']).'">'.$this->p($bookmark['title']).'</a></td></tr>';
        }
        $html .= '</table>';
        return $html;
    }

    function getSettingsArray()
    {
        return array(
            'tagKeyword'  => array(
                'type'      => 'text',
                'default'   => 'Dashboard',
                'name'      => 'Tag keyword',
                'info'      => 'Use this tag in the bookmarks app.'
            )
        );
    }

    protected function getLicenseInfo() {
        return 'This widget uses the bookmarks-app from owncloud.<br>For more details look at the license from the bookmarks-app.';
    }

}