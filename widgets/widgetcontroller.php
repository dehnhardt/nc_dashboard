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
use OCP\AppFramework\Controller;
use OCP\IL10N;

abstract class WidgetController extends Controller implements IWidgetController{

    protected $icon;
    protected $refresh;
    protected $name;
    protected $wId;
    protected $user;
    protected $wNo;
    protected $dataHash;
    protected $link;
    protected $status;

    protected $L10N;
    protected $widgetSettingsService;
    protected $dateTimeFormatter;



    // ----- abstract and magic methods ----------------------------------------------

    public function secondInjection ($wNo, WidgetSettingsService $widgetSettingsService, $user, IL10N $l10n, DateTimeFormatter $dateTimeFormatter) {
        $this->wNo                      = intval($wNo);
        $this->user                     = $user;
        $this->L10N                     = $l10n;
        $this->widgetSettingsService    = $widgetSettingsService;
        $this->status                   = Status::STATUS_OKAY;
        $this->dateTimeFormatter        = $dateTimeFormatter;

        // load widget specific values
        $this->setBasicValues();
    }


    // ----- public methods ---------------------------------------------------

    /**
     *
     * returns the status of this widget
     *
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     *
     * update status only if the new status
     * is more important that the old one
     *
     * @param $status
     */
    protected function setStatus($status) {
        if( $status > $this->status ) {
            $this->status = $status;
        }
    }

    /**
     *
     * returns all the needed data as array
     * you can access them in the widgetTemplate->getContentHtml with $data['abc']
     *
     * @return array
     */
    public function getBasicValues() {
        /** @noinspection PhpUndefinedMethodInspection */
        $name = (!method_exists($this, 'getName')) ? $this->getConfig('name'): $this->getName();
        return array(
            'wId'       => $this->getConfig('wId'),
            'wNo'       => $this->getConfig('wNo'),
            'wIId'      => $this->getConfig('wIId'),
            'name'      => $name,
            'dimension' => $this->getConfig('dimension', '1x1'),
            'refresh'   => $this->getConfig('refresh', '30', 'int'),
            'icon'      => $this->getConfig('icon'),
            'link'		=> $this->getConfig('link'),
            'status'    => $this->getStatus()
        );
    }

    /**
     *
     * tells you the chosen value for a key
     * if no value is set yet, the default will return
     *
     * @param $key
     * @param string $default
     * @param string $returnType
     * {'string', 'int', 'bool'}
     * @return string
     */
    public function getConfig ( $key, $default = '', $returnType = 'string' ) {
        $value = null;
        switch( $key ) {
            case 'wIId':
                $value = $this->getConfig('wId').'-'.$this->getConfig('wNo');
                break;
            case 'wName':
                $value = $this->name;
                break;
            case 'name':
                $value = $this->name;
                break;
            case 'wNo':
                $value = $this->wNo;
                break;
            case 'user':
                $value = $this->user;
                break;
            case 'icon':
                $value = $this->icon;
                break;
            case 'refresh':
                $value = $this->refresh;
                break;
            case 'wId':
                $value = $this->wId;
                break;
            case 'appName':
                $value = $this->widgetSettingsService->getAppName($this->wId);
        		\OCP\Util::writeLog('dashboard', 'AppName: '.$value, \OCP\Util::ERROR);
                break;
            case 'appPath':
            	$value = \OC_App::getAppPath($this->widgetSettingsService->getAppName($this->wId));
        		\OCP\Util::writeLog('dashboard', 'AppPath: '.$value, \OCP\Util::ERROR);
                break;
            case 'link':
                $value = $this->widgetSettingsService->getLink($this->wId);
        		\OCP\Util::writeLog('dashboard', 'Link: '.$value, \OCP\Util::DEBUG);
                break;
            default:
                $value = $this->widgetSettingsService->getConfig($this->wId, $this->wNo, $key, $default);
                break;
        }
        $return = isset($value) ? $value: $default;
        

        switch( $returnType ) {
            case 'int':
                return intval($return);
                break;
            case 'bool':
                if( $return === '1' || $return || $return === 'true' ) {
                    return true;
                } else {
                    return false;
                }
            default:
                return ''.$return;
        }
    }















    /**
     *
     * remove old hashes
     * insert or update actual hash
     * set status, if hash is new
     *
     * @param $data
     */
    protected function x_setHash($data) {
        $this->widgetHashDAO->removeOldHashes();
        $hash = sha1(json_encode($data));
        $usedHash = $this->widgetHashDAO->getHash($this->getConfig('wIId'), $this->user);
        if( $usedHash === $hash ) {
            // update timestamp
            $this->widgetHashDAO->updateHash($this->getConfig('wIId'), $this->user, $hash);
        } else {
            // insert new and mark as new widget content
            $this->widgetHashDAO->removeWidgetHashes($this->getConfig('wIId'), $this->user);
            $this->widgetHashDAO->insertHash($this->getConfig('wIId'), $this->user, $hash);
            $this->setStatus($this::STATUS_NEW);
        }
    }


} 