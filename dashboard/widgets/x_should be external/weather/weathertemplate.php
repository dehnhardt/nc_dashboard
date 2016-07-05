<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Weather;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;
use OCP\Util;

class WeatherTemplate extends WidgetTemplate implements IWidgetTemplate {

    function getContentHtml($data = array()) {
        // if problem exists
        if( isset($data['msg']) ) {
            return '<div class="msg">'.$this->p($data['msg']).'</div>';
        }

        $html   = '<table>';
        $html  .= '    <tr>';
        $html  .=       $this->getTodayPart($data);
        $html  .=       $this->getMiddlePart($data);
        $html  .=       $this->getTomorrowPart($data);
        $html  .= '    </tr>';
        $html  .= '    <tr>';
        $html  .=       $this->getNowPart($data);
        $html  .= '    </tr>';
        $html  .= '    <tr>';
        $html  .=       $this->getForecastPart($data);
        $html  .= '    </tr>';
        $html  .= '</table>';

        return $html;
    }

    function getSettingsArray()
    {
        return array(
            'city'  => array(
                'type'      => 'text',
                'default'   => 'Berlin',
                'name'      => 'City'
            ),
            'unit'  => array(
                'type'      => 'select',
                'options'       => array(
                    'c'           => 'metric (&deg;C)',
                    'i'           => 'imperial (F)'
                ),
                'name'          => 'Units',
                'default'       => 'c'
            ),
        );
    }

    // private services

    private function getForecastPart($data) {
        $list = $data['weatherData']['list'];
        $html = '';
        $html .= '<td colspan="3"><br>';
        if( count($list)>0 ) {
            foreach ($list as $item) {
                $html .= '<div class="forecastItem">';
                $html .= '  <img class="weatherIcon" src="' . Util::imagePath('dashboard', $this->getIconMapping($this->p($item['weather'][0]['icon']))) . '" alt="weather icon" /><br>';
                $html .= '  <div class="forecastTemp">' . substr($this->l10n->l('date', $this->p($item['dt'])), 0, -4) . '<br>' . $this->round($this->p($item['temp']['day'])) . $this->getTemperatureUnit($this->p($data['unit'])) . '</div>';
                $html .= '</div>';
            }
        }
        $html .= '</td>';
        return $html;
    }

    private function getNowPart($data) {
        $weather = $data['weatherNow'];
        $html = '';
        $html .= '<td colspan="3">';
        $html .= '<table>';
        $html .= '    <tr>';
        $html .= '        <td colspan="3">';
        $html .= '          <div class="align-center hoverInfo" data-opacitynormal="0.5">'.$this->p($weather['weather'][0]['description']).'</div><br>';
        $html .= '        </td>';
        $html .= '    </tr>';
        $html .= '    <tr>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('Temperature');
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          <div class="text-align-right">'.$this->round($this->p($weather['main']['temp'])).'</div>';
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          '.$this->getTemperatureUnit($data['unit']);
        $html .= '        </td>';
        $html .= '    </tr>';
        $html .= '    <tr>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('Pressure');
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          <div class="text-align-right">'.$this->p($weather['main']['pressure']).'</div>';
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          '.$this->getPressureUnit($data['unit']);
        $html .= '        </td>';
        $html .= '    </tr>';
        $html .= '    <tr>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('Humidity');
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          <div class="text-align-right">'.$this->p($weather['main']['humidity']).'</div>';
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          &nbsp;%';
        $html .= '        </td>';
        $html .= '    </tr>';
        $html .= '    <tr>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('Wind');
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          <div class="text-align-right">'.$this->getWindSpeed($this->p($weather['wind']['speed']), $this->p($data['unit'])).'</div>';
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          '.$this->getWindUnit($data['unit']);
        $html .= '        </td>';
        $html .= '    </tr>';
        $html .= '    <tr>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('Cloud coverage');
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          <div class="text-align-right">'.$this->p($weather['clouds']['all']).'</div>';
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          &nbsp;%';
        $html .= '        </td>';
        $html .= '    </tr>';
        $html .= '    <tr>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('Sunrise');
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          <div class="text-align-right">'.$this->l10n->l('time', $weather['sys']['sunrise']).'</div>';
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('&nbsp;o\' clock');
        $html .= '        </td>';
        $html .= '    </tr>';
        $html .= '    <tr>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('Sunset');
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          <div class="text-align-right">'.$this->l10n->l('time', $weather['sys']['sunset']).'</div>';
        $html .= '        </td>';
        $html .= '        <td>';
        $html .= '          '.$this->l10n->t('&nbsp;o\' clock');
        $html .= '        </td>';
        $html .= '    </tr>';
        $html .= '</table>';
        $html .= '</td>';
        return $html;
    }

    private function getWindSpeed($speed, $unit) {
        if( $unit == 'c') {
            $speed = round($speed * 3.6);
        }
        return $speed;
    }

    private function getTodayPart($data) {
        if( !isset($data['weatherData']['list']) ) {
            return '<td></td>';
        }
        $html = '';
        $html .= '<td>';
        $html .=    '<div class="h2">'.$this->l10n->t('today').'</div>';
        $html .=    $this->l10n->t('Min').' '.$this->round($this->p($data['weatherData']['list'][0]['temp']['min'])).$this->getTemperatureUnit($data['unit']).'<br>';
        $html .=    $this->l10n->t('Max').' '.$this->round($this->p($data['weatherData']['list'][0]['temp']['max'])).$this->getTemperatureUnit($data['unit']).'<br>';
        $html .= '</td>';
        return $html;
    }

    private function getTomorrowPart($data) {
        if( !isset($data['weatherData']['list']) ) {
            return '<td></td>';
        }
        $html = '';
        $html .= '<td>';
        $html .=    '<div class="h2">'.$this->l10n->t('tomorrow').'</div>';
        $html .=    $this->l10n->t('Min').' '.$this->round($this->p($data['weatherData']['list'][1]['temp']['min'])).$this->getTemperatureUnit($data['unit']).'<br>';
        $html .=    $this->l10n->t('Max').' '.$this->round($this->p($data['weatherData']['list'][1]['temp']['max'])).$this->getTemperatureUnit($data['unit']).'<br>';
        $html .= '</td>';
        return $html;
    }

    private function getMiddlePart($data) {
        if( !isset($data['weatherData']['list']) ) {
            return '<td></td>';
        }
        $html = '';
        $html .= '<td>';
        $html .=    '<div class="h1">'.$data['city'].'</div>';
        $html .=    '<div class="align-center">';
        $html .=        '<img class="weatherIcon" src="'.Util::imagePath( 'dashboard', $this->getIconMapping($this->p($data['weatherData']['list'][0]['weather'][0]['icon'])) ).'" alt="weather icon" />';
        $html .=        '&nbsp;&nbsp;&nbsp;';
        $html .=        '<img class="weatherIcon" src="'.Util::imagePath( 'dashboard', $this->getIconMapping($this->p($data['weatherData']['list'][1]['weather'][0]['icon'])) ).'" alt="weather icon" />';
        $html .=    '</div>';
        $html .= '</td>';
        return $html;
    }

    /**
     *
     * returns the correct unit
     *
     * @param string $unitId
     * @return string
     */
    private function getTemperatureUnit($unitId = 'c') {
        $unit = '';
        switch ($unitId) {
            case 'c':
                $unit = '&nbsp;&deg;C';
                break;
            case 'i':
                $unit = '&nbsp;F';
                break;
        }
        return $unit;
    }

    private function getWindUnit($unitId = 'c') {
        $unit = '';
        switch ($unitId) {
            case 'c':
                $unit = '&nbsp;km/h';
                break;
            case 'i':
                $unit = '&nbsp;mph';
                break;
        }
        return $unit;
    }

    private function getPressureUnit($unitId = 'c') {
        switch ($unitId) {
            default:
                $unit = '&nbsp;mb';
                break;
        }
        return $unit;
    }

    /**
     *
     * round and nice output
     *
     * @param $value
     * @return float|string
     */
    private function round($value) {
        $value = intval($value);
        $v = round($value);
        if($v == '-0') {
            $v = '0';
        }
        if( substr($v, 0, 1) != '-') {
            $v = '&nbsp;'.$v;
        }
        return $v;
    }

    /**
     *
     * map the icon from open weather to the local iconset
     *
     * @param $openWeatherIcon
     * @return string
     */
    private function getIconMapping($openWeatherIcon) {
        $mapping = array(
            '01d'  => '160',
            '01n'  => '161',
            '02d'  => '165',
            '02n'  => '161',
            '03d'  => '164',
            '03n'  => '164',
            '04d'  => '164',
            '04n'  => '164',
            '09d'  => '167',
            '09n'  => '167',
            '10d'  => '167',
            '10n'  => '167',
            '11d'  => '166',
            '11n'  => '166',
            '13d'  => '168',
            '13n'  => '168',
            '50d'  => '169',
            '50n'  => '169'
        );
        return 'icons/'.$mapping[$openWeatherIcon].'.png';
    }

    protected function getLicenseInfo() {
        return 'This widget uses data from openweathermap.org.<br>For more details look at the license from openweathermap.org.';
    }


}