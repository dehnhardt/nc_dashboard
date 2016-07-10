<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\News;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class NewsTemplate extends WidgetTemplate implements IWidgetTemplate {

    public function getContentHtml($data = array()) {
        // if problem exists
        if( isset($data['msg']) ) {
            return '<div class="msg">'.$this->p($data['msg']).'</div>';
        }

        $html  = '<table>';
        if( $data['showDetail'] ) {
            $html .= $this->getDetailView($data);
        }
        if( $data['showList'] ) {
            $html .= $this->getListView($data);
        }
        $html .= '</table>';
        return $html;
    }

    public function getSettingsArray()
    {
        return array(
            'maxItemAge'  => array(
                'type'      => 'text',
                'default'   => '1',
                'name'      => 'Max age for news',
                'info'      => 'News will be filtered by age.'
            ),
            'showDetail' => array(
                'type'   => 'select',
                'options'=> array(
                    '1'  => 'show',
                    '0'  => 'hide'
                ),
                'default' => '1',
                'name'    => 'Detail view',
                'info'    => 'Uncertain content may load by uncertain source'
            ),
            'showList' => array(
                'type'   => 'select',
                'options'=> array(
                    '1'  => 'show',
                    '0'  => 'hide'
                ),
                'default' => '1',
                'name'    => 'List view',
            ),
        );
    }

    protected function getLicenseInfo() {
        return 'This widget uses the news-app from owncloud.<br>For more details look at the license from the news-app.';
    }

    // private services ------------------------------------------------------

    private function getDetailView($data) {
        $item = $data['news'][$data['detailViewKey']];
        $html = '';
        if( count($data['news']) > 0 ) {
            $html .= '<tr>';
            $html .= '<td>';
            $html .= '<div class="newsitem">';
            $html .= '<div class="newsTitle">&nbsp;<span class="icon-checkmark markAsRead" data-newsid="'.$item['id'].'" data-wiid="'.$this->wIId.'">&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;';
            $html .= '<a href="'.$item['url'].'" target="_blank">'.$this->p($item['title']).'</a></div>';
            $html .= '<div class="newsPubDate">'.$this->l10n->l('datetime', $item['pubDate']).'</div>';
            $html .= '<div class="newsBody">'.$this->print_unescaped($item['body']).'</div>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '&nbsp;';
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    }

    private function getListView($data) {
        $html = '';
        foreach ( $data['news'] as $item ) {
            $html .= '<tr>';
            $html .= '<td><div class="newsitem">';
            $html .= '&nbsp;<span class="icon-checkmark markAsRead" data-newsid="'.$item['id'].'" data-wiid="'.$this->wIId.'">&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;';
            $html .= '<a href="'.$this->p($item['url']).'" target="_blank">'.$this->p($item['title']).'</a>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }
        return $html;
    }
}