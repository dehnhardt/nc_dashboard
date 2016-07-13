<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 16:25
 */

namespace OCA\Dashboard\Widgets;


use OC\DateTimeFormatter;
use OCA\Dashboard\Services\WidgetSettingsService;
use OCA\Dashboard\Utils\Helper;
use OCP\IL10N;

abstract class WidgetTemplate implements IWidgetTemplate {

    protected $L10N;
    protected $widgetSettingsService;
    protected $dateTimeFormatter;


    // ----- abstract and magic methods ----------------------------------------------

    public function secondInjection (IL10N $l10n, WidgetSettingsService $widgetSettingsService, DateTimeFormatter $dateTimeFormatter) {
        $this->L10N                     = $l10n;
        $this->widgetSettingsService    = $widgetSettingsService;
        $this->dateTimeFormatter        = $dateTimeFormatter;
    }


    public abstract function getContentHtml( $data = array() );

    public abstract function getSettingsArray();

    /**
     *
     * return the complete html for a widget
     * included
     *  - header
     *  - content
     *  - settings
     *
     * @param $data
     * @return string
     */
    public function getCompleteHtml( $data ) {
        $html = '';
        $html .= '<div class="heading">';
        $html .= $this->getHeadHtml( $data );
        $html .= '</div>';
        $html .= '<div class="content">';
        $html .= $this->getContentHtml( $data );
        $html .= '</div>';
        $html .= '<div class="settings">';
        $html .= $this->getSettingsHtml( $data['wIId'] );
        $html .= '</div>';
        return $html;
    }


    // ----- protected methods --------------------------------------------

    /**
     *
     * all output strings and values should be passed by this
     * to avoid XSS and other security things
     *
     * @param $string
     * @return array|string
     */
    protected function p( $string ) {
        return \OCP\Util::sanitizeHTML($string);
    }

    /**
     *
     * this is not a safe method
     * but if you have to print html use this
     *
     * @param $string
     * @return array|string
     */
    protected function print_unescaped( $string ) {
        return $string;
    }


    // ----- private methods ----------------------------------------------

    /**
     *
     * return the settings html
     *
     * @param $wIId
     * @return string
     */
    private function getSettingsHtml( $wIId ) {
        // this settings are available for every widget instance
        $defaultSettings        = $this->getDefaultSettings();

        // settings specially for this widget type
        $specialSettingsArray   = $this->getSettingsArray();

        // all settings combined
        $settingsArray          = array_merge($defaultSettings, $specialSettingsArray);

        $return = '<table>';
        foreach( $settingsArray as $key => $setting) {
            $return .= '<tr><td>'.$this->L10N->t($setting['name']).'</td><td>'.$this->getSettingsField($setting, $key, $wIId).'</td></tr>';
            if( isset($setting['info']) ) {
                $return .= '<tr><td colspan="2"><div class="settingsInfo">'.$this->L10N->t($setting['info']).'</div></td></tr>';
            }
        }
        $return .= '<tr><td>'.$this->L10N->t('Remove widget').'</td><td><input class="removeWidget" data-wiid="'.$wIId.'" type="button" value="'.$this->L10N->t('remove now').'" /></td></tr>';
        if( method_exists($this, 'getLicenseInfo') ) {
            $return .= '<tr><td class="key">'.$this->L10N->t('License').'</td><td><div class="value">'.$this->getLicenseInfo().'</div></td></tr>';
        }
        $return .= '</table>';
        return $return;
    }

    /**
     *
     * return the html for one field
     * depending on its type
     *
     * @param $setting
     * @param $key
     * @param $wIId
     * @return string
     */
    private function getSettingsField($setting, $key, $wIId) {
        $type   = (isset($setting['type'])) ? $setting['type']: '';
        $value  = $this->getValueForField($key, $setting['default'], $wIId);
        $html   = '&nbsp;';
        switch($type) {
            case 'select':
                $html .= '<select name="'.$key.'" data-wiid="'.$wIId.'" class="setting">';
                if(is_array($setting['options'])) {
                    foreach( $setting['options'] as $key => $option) {
                        $html .= '<option value="'.$key.'"';
                        if( $value === $key ) {
                            $html .= ' selected ';
                        }
                        $html .= '>'.$this->L10N->t($option).'</option>';
                    }
                }
                $html .= '</select>';
                break;
            case 'text':
                $html .= '<input type="text" name="'.$key.'" data-wiid="'.$wIId.'" class="setting" value="'.$this->L10N->t($value).'" />';
                break;
            case 'password':
                $html .= '<input type="password" name="'.$key.'" data-wiid="'.$wIId.'" class="setting" />';
                break;
            default:
                $html .= 'error';
        }
        return $html;
    }

    /**
     *
     * if a value is set in the db, get and return it
     * else return default
     *
     * @param $key
     * @param $default
     * @param $wIId
     * @return int|null|string
     * @internal param IWidgetController $widgetController
     */
    private function getValueForField($key, $default, $wIId) {
        return $this->widgetSettingsService->getConfig(Helper::wId($wIId), Helper::wNo($wIId), $key, $default);
    }

    /**
     *
     * return the default array entries for the widget settings
     *
     * @return array
     */
    private function getDefaultSettings() {
        return array(
            'dimension'     => array(
                'type'          => 'select',
                'options'       => array(
                    '1x1'           => '1 x 1',
                    '1x2'           => '1 x 2',
                    '1x3'           => '1 x 3',
                    '2x1'           => '2 x 1',
                    '2x2'           => '2 x 2',
                    '2x3'           => '2 x 3',
                    '3x1'           => '3 x 1',
                    '3x2'           => '3 x 2',
                    '3x3'           => '3 x 3'
                ),
                'name'          => 'Dimension',
                'default'       => '1x1'
            ),
            'order'         => array(
                'type'          => 'text',
                'name'          => 'Order',
                'default'       => '10',
                'info'          => 'Refresh to apply changes.'
            )
        );
    }

    /**
     *
     * return the html of the head
     * included
     *  - name (title)
     *  - reload icon
     *  - settings icon
     *  - icon
     *
     * @param $data array with basic values from widgetController super class
     * @return string
     */
    private function getHeadHtml( $data ) {
        $html       = '<h1 class="hoverInfo" data-opacitynormal="0.5">';

        if( isset($data['link']) !== null && $data['link'] !== '' ) {
            $html .= '<a href="'.$data['link'].'">'.$data['name'].'</a>';
        } else {
            $html .= $data['name'];
        }

        if( isset($data['refresh']) && $data['refresh'] !== 0 ) {
            $html .= '<span class="hoverInfo icon-play iconReload" data-wiid="'.$data['wIId'].'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
        }

        $html .= '<span class="hoverInfo icon-settings iconSettings">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
        $html .= '</h1>';

        if( isset($data['icon']) && $data['icon'] !== '' ) {
            $html .= '<div class="icon"><img src="'.$data['icon'].'" alt="'.$data['wIId'].' icon" /></div>';
        }

        return $html;
    }

}
