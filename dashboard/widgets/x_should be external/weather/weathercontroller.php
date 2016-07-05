<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Weather;

use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;

class WeatherController extends WidgetController implements IWidgetController {

    private $basicUrlWeatherForecast    = "http://api.openweathermap.org/data/2.5/forecast/daily?";
    private $basicUrlWeatherNow         = 'http://api.openweathermap.org/data/2.5/weather?';



    // interface needed methods ------------------------------------


    /**
     * see IWidgetController interface
     */
    public function setData() {
        $this->icon     =           'icons/165.png';
        $this->refresh  =                       360;
        $this->wId      =                 'weather';
        $this->name     = $this->l10n->t('Weather');
    }

    /**
     *
     * returns all the needed data as array
     * you can access them in the widgetTemplate->getContentHtml with $data['abc']
     *
     * @return array
     */
    public function getData() {
        if( $this->checkDependencies() ) {
            $data = array(
                'weatherData' => $this->getWeatherData(true),
                'city' => $this->getConfig('city', 'Berlin'),
                'unit' => $this->getConfig('unit', 'c'),
                'weatherNow' => $this->getWeatherNow(true)
            );
        } else {
            $this->setStatus($this::STATUS_PROBLEM);
            $data = array(
                'msg'     => 'Curl must be enabled.'
            );
        }

        // do not remove the following line
        // it creates the status information
        $this->setHash($data);
        return $data;
    }

    // ajax call methods ---------------------------------------------

    // private services -------------------------------------------------

    private function checkDependencies() {
        return function_exists('curl_version');
    }

    /**
     *
     * create url to fetch weather data from
     *
     * @return string
     */
    private function getForecastUrl() {
        $city   = $this->getConfig('city', 'Berlin');
        $url    = $this->basicUrlWeatherForecast.'q='.$city;

        $lang   = $this->l10n->getLanguageCode('dashboard');
        $lang   = substr($lang, 0, 2);
        $url   .= '&lang='.$lang;

        $unit   = $this->getConfig('unit', 'c');
        $url   .= ($unit=='c') ? '&units=metric':'';
        $url   .= ($unit=='i') ? '&units=imperial':'';
        return $url;
    }

    /**
     *
     * create url to fetch weather data for now
     *
     * @return string
     */
    private function getWeatherNowUrl() {
        $city   = $this->getConfig('city', 'Berlin');
        $url    = $this->basicUrlWeatherNow.'q='.$city;

        $lang   = $this->l10n->getLanguageCode('dashboard');
        $lang   = substr($lang, 0, 2);
        $url   .= '&lang='.$lang;

        $unit   = $this->getConfig('unit', 'c');
        $url   .= ($unit=='c') ? '&units=metric':'';
        $url   .= ($unit=='i') ? '&units=imperial':'';
        return $url;
    }

    /**
     *
     * get content as array or json
     * sometimes the connection can not be established,
     * therefor we retry it for five times
     *
     * @param bool $asArray
     * @return mixed
     */
    private function getWeatherData($asArray=false) {
        $url        = $this->getForecastUrl();
        $content    = '';
        $n          = 0;
        while( $content == '' && $n < 5) {
            $content    = $this->fetchContent($url);
            $n++;
            sleep(0.5);
        }
        if( $content == '' ) {
            $this->setStatus($this::STATUS_PROBLEM);
        }

        if($asArray) {
            return json_decode( $content, true );
        } else {
            return $content;
        }
    }

    /**
     *
     * get content as array or json
     * sometimes the connection can not be established,
     * therefor we retry it for five times
     *
     * @param bool $asArray
     * @return mixed
     */
    private function getWeatherNow($asArray=false) {
        $url        = $this->getWeatherNowUrl();
        $content    = '';
        $n          = 0;
        while( $content == '' && $n < 3) {
            $content    = $this->fetchContent($url);
            $n++;
            sleep(0.5);
        }
        if( $content == '' ) {
            $this->setStatus($this::STATUS_PROBLEM);
        }

        if($asArray) {
            return json_decode( $content, true );
        } else {
            return $content;
        }
    }

    /**
     *
     * fetch content from openweathermap
     *
     * @param $url
     * @return mixed
     */
    private function fetchContent($url) {
        $ch = curl_init($url);
        $timeout = 2;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);
        if($content==null || strlen($content) <= 30) {
            \OC_Log::write('dashboard', 'could not fetch weather data (url='.$url.')', \OC_Log::DEBUG);
            $content = '';
        }
        return $content;
    }
} 