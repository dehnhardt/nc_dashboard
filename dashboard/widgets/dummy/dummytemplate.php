<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Dummy;

use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;


class DummyTemplate extends WidgetTemplate implements IWidgetTemplate {

    function getContentHtml($data = array()) {
        $html = '<table>
                    <tr>
                        <td><div class="time hoverInfo" data-opacitynormal="0.7">' . $this->L10N->t('Time') . ': ' . $this->L10N->l('datetime', $data['time']) . '</div></td>
                    </tr>
                    <tr>
                        <td><div class="generateNormal" data-wiid="' . $data['wIId'] . '">' . $this->L10N->t('click to generate normal status') . '</div></td>
                    </tr>
                    <tr>
                        <td><div class="generateNew" data-wiid="' . $data['wIId'] . '">' . $this->L10N->t('click to generate new status') . '</div></td>
                    </tr>
                    <tr>
                        <td><div class="generateProblem" data-wiid="' . $data['wIId'] . '">' . $this->L10N->t('click to generate problem status') . '</div></td>
                    </tr>
                    <tr>
                        <td><div class="generateError" data-wiid="' . $data['wIId'] . '">' . $this->L10N->t('click to generate error status') . '</div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="' . $data['wIId'] . ' countUp">
                                <div class="counter"></div>
                                <div class="counterTime">'.$this->L10N->t('last tick:').'<span></span></div>
                                <button class="counterButton" data-wiid="' . $data['wIId'] . '" data-counter="0">'.$this->L10N->t('click me').'</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>';
        foreach ($data['values'] as $key => $value) {
            $html .=  '<tr>
                        <td><i>' . $key . ': ' . $value . '</i></td>
                    </tr>';
        }

        $html .= '</table>';

        return $html;
    }

    function getSettingsArray() {
        return array(
            'settingOne' => array(
                'type' => 'text',
                'default' => '',
                'name' => 'valueOne'
            ),
            'settingTwo' => array(
                'type'   => 'select',
                'options'=> array(
                    '1'  => 'one',
                    '2'  => 'two',
                    '3'  => 'three'
               ),
               'default' => 'one',
               'name'    => 'value two',
               'info'    => 'just an info text'
            ),
        );
    }

    public function getLicenseInfo() {
        return 'Feel free to copy und use this dummy to develop new widgets.';
    }

}
