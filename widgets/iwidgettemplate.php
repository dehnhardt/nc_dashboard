<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 16:27
 */

namespace OCA\Dashboard\Widgets;


interface IWidgetTemplate {

    /**
     *
     * returns the complete content html
     *
     * @param array $data
     * @return mixed
     */
    public function getContentHtml($data = array());

    /**
     *
     * returns a array with settings
     * specially for this widget-type
     *
     * @return mixed
     */
    public function getSettingsArray();

    public function getLicenseInfo();

    public function getCompleteHtml( $data );

}