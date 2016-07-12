<?php
/**
* Created by PhpStorm.
* User: flost
* Date: 27.04.15
* Time: 20:29
*/

namespace OCA\Dashboard\Services;

use OCA\Dashboard\Db\WidgetHashDAO;

class WidgetHashService {

    private $widgetHashDAO;
    private $user;

    function __construct(WidgetHashDAO $widgetHashDAO, $user) {
        $this->widgetHashDAO    = $widgetHashDAO;
        $this->user             = $user;
    }

    /**
     *
     * remove all hashes that are older than 24 hours
     * called by cron job
     *
     * @return bool
     */
    public function removeOldHashes() {
        // TODO cron job
        return true;
    }

    /**
     *
     * write a new hash to the db
     * if hash exists, timestamp will be updated
     * if the hash is new, true will be returned
     *
     * @param $wIId
     * @param $data
     * @return bool
     */
    public function isNew($wIId, $data) {
        $hash   = sha1(json_encode($data));
        if( $this->widgetHashDAO->updateTime($wIId, $this->user, $hash) ) {
            return false;
        } else {
            if( !$this->widgetHashDAO->insertHash($wIId, $this->user, $hash) ) {
                \OCP\Util::writeLog('dashboard',"could not insert hash", \OCP\Util::WARN);
            }
            return true;
        }
    }

    /**
     *
     * remove hashes for a wIId
     * (for example if the wIId removes)
     *
     * @param $wIId
     * @return bool
     */
    public function removeHashes($wIId) {
        return $this->widgetHashDAO->removeWidgetHashes($wIId, $this->user);
    }
}
