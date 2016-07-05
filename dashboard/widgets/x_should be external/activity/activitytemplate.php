<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Activity;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class ActivityTemplate extends WidgetTemplate implements IWidgetTemplate {
    private $maxStringLen;
    private $startStringLen;

    /**
     * @param array $data
     * @return string
     */
    function getContentHtml($data = array()) {
        $this->maxStringLen     = $data['maxStringLen'];
        $this->startStringLen   = $data['startStringLen'];

        // if problem exists
        if( isset($data['msg']) ) {
            return '<div class="msg">'.$this->p($data['msg']).'</div>';
        }

        $html = '';
        $html .= '<table>';
        foreach ($data['activities'] as $activity) {
            $prioritystyle='class="activity-section group"';
            if( isset($activity['priority']))
                $prioritystyle='class="activity-section group priority-'.$this->p($activity['priority']);
            $priority       = $this->p($activity['priority']);
            $link           = $this->p($activity['link']);
            $subject        = $this->p($activity['subject']);
            $sSub           = $this->getNiceSmallText($this->p($activity['subject']));
            $smallSubject   = \OC_Util::sanitizeHTML($sSub);
            $time           = $this->getRelativeTime($this->p($activity['date']));
            $html .= '<tr><td><div '.$prioritystyle.' subject"><a class="preview preview-dir-icon" href="'.$link.'" title="'.$subject.'">'.$smallSubject.'</a><br /><span class="hoverInfo" data-opacitynormal="0.5">'.$time.'</span></div></td></tr>';
        }
        $html .= '</table>';
        return $html;
    }

    function getSettingsArray()
    {
        return array(
            'maxStringLen'  => array(
                'type'      => 'text',
                'default'   => '40',
                'name'      => 'Max string length.'
            ),
            'startStringLen'  => array(
                'type'      => 'text',
                'default'   => '5',
                'name'      => 'Start string length.'
            )
        );
    }

    private function getNiceSmallText($string) {
        if(strlen($string) >= $this->maxStringLen) {
            $lastCharacter = -1 * ($this->maxStringLen-$this->startStringLen);
            $return = substr($string,0,$this->startStringLen);
            $return .= "...";
            $return .= substr($string,$lastCharacter);
        } else {
            $return = $string;
        }
        return $return;
    }

    private function getRelativeTime($timeString) {
        // ToDo what about relative time?
        $datetime   = new \DateTime($timeString);
        $time       = $datetime->getTimestamp()+$datetime->getOffset();
        if( function_exists('\OCP\relative_modified_date') ) {
            $time = \OCP\relative_modified_date($time);
        } else {
            $time = $this->l10n->l('datetime', $time);
        }
        return $time;
    }

    protected function getLicenseInfo() {
        return 'This widget uses the activity-app from owncloud.<br>For more details look at the license from the activity-app.';
    }

}
