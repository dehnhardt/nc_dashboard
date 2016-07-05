<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Tasks;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class TasksTemplate extends WidgetTemplate implements IWidgetTemplate {

    function getContentHtml($data = array()) {
        // if problem exists
        if( isset($data['msg']) ) {
            return '<div class="msg">'.$this->p($data['msg']).'</div>';
        }

        $html  = '<table>';
        foreach( $data['calendars'] as $calId => $cal) {
            // heading calendar
            $style = (isset($cal['color']) && $cal['color']!='') ? 'style="border-left: 5px solid '.$cal['color'].'; padding-left: 5px;" ': '';
            $html .= '<tr>
                        <td><div class="calendar" '.$style.'>'.$this->p($cal['name']).'</div></td>
                    </tr>';

            foreach ($data['tasks'] as $task) {
                if( $task['calendarid'] == $calId ) {
                    $html .= '<tr><td>';
                    $html .= '<div class="task">';
                    $html .= '&nbsp;<span class="icon-checkmark markAsDone" data-taskid="'.$task['id'].'" data-wiid="'.$this->wIId.'">&nbsp;&nbsp;&nbsp;</span>&nbsp;';
                    $html .= $task['name'];
                    if( $task['starred'] == true ) {
                        $html .= '&nbsp;&nbsp;<span class="icon-starred">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                    }
                    $html .= '</div></td></tr>';
                }
            }

        }
        $html .= '</table>';
        return $html;
    }

    function getSettingsArray()
    {
        return array(
            'settingOne'  => array(
                'type'      => 'text',
                'default'   => '',
                'name'      => 'valueOne'
            )
        );
    }

    protected function getLicenseInfo() {
        return 'This widget uses the calendar and tasks-app from owncloud.<br>For more details look at the license from the calendar and tasks-app.';
    }

}