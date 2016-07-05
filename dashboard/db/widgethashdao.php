<?php

namespace OCA\Dashboard\Db;

use \OCP\IDb;


class WidgetHashDAO {

    private $db;
    private $table;

    public function __construct(IDb $db) {
        $this->db       = $db;
        $this->table    = '*PREFIX*dashboard_used_hashs';
    }

    /**
     *
     * insert new hash
     *
     * @param $wIId
     * @param $user
     * @param $usedHash
     * @return bool
     */
    public function insertHash($wIId, $user, $usedHash) {
        $sql = 'INSERT INTO `'.$this->table.'`(`used_hash`, `wiid`, `user`, `timestamp`) VALUES(?,?,?,?)';
        $params = array($usedHash, $wIId, $user, time());
        $query = $this->db->prepareQuery($sql);
        return $query->execute( $params );
    }

    /**
     *
     * update existing hash
     *
     * @param $wIId
     * @param $user
     * @param $usedHash
     * @return bool
     */
    public function updateTime($wIId, $user, $usedHash) {
        $sql = 'UPDATE `'.$this->table.'` SET `timestamp` = ? WHERE `wiid` = ? AND `user` = ? AND `used_hash` = ? ';
        $params = array(time(), $wIId, $user, $usedHash);
        $query = $this->db->prepareQuery($sql);
        if( !$query->execute( $params ) ) {
            $this->removeWidgetHashes($wIId, $user);
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * delete all old hashes
     * max lifetime for a hash is one day
     *
     */
    public function removeOldHashes() {
        $maxTime = time() - 60*60*24;
        $sql = 'DELETE FROM `'.$this->table.'` WHERE `timestamp` < ?';
        $query = $this->db->prepareQuery($sql);
        $query->execute( array($maxTime) );
    }

    /**
     *
     * remove all hashes for a wIId and user
     *
     * @param $wIId
     * @param $user
     * @return bool
     */
    public function removeWidgetHashes($wIId, $user) {
        $sql = 'DELETE FROM `'.$this->table.'` WHERE `wiid` = ? AND `user` = ?';
        $query = $this->db->prepareQuery($sql);
        return ($query->execute( array($wIId, $user) ));
    }
}