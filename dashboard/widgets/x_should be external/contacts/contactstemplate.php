<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Contacts;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class ContactsTemplate extends WidgetTemplate implements IWidgetTemplate
{

    function getContentHtml($data = array())
    {
        return '<div class="search">
                <table>
                    <tr>
                        <td><input type="text" name="contactSearch" class="contactSearch" data-wiid="'.$this->wIId.'"></td>
                    </tr>
                    <tr>
                        <td><div class="contactSearchResults"></div></td>
                    </tr>
                </table>
                </div>';
    }

    function getSettingsArray()
    {
        return array(
        );
    }

    protected function getLicenseInfo()
    {
        return '';
    }

}
