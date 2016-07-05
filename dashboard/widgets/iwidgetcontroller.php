<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 16:27
 */

namespace OCA\Dashboard\Widgets;


interface IWidgetController {

    public function getData();

    public function setBasicValues();

    public function getBasicValues();

} 