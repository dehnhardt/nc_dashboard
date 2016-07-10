<?php

namespace OCA\Dashboard\Db;

use \OCP\IDb;

class WidgetConfigDAO {

    private $db;
    private $table;

    public function __construct(IDb $db) {
        $this->db       = $db;
        $this->table    = '*PREFIX*dashboard_config';
    }

    /**
     *
     * insert or if key exists update config value
     *
     * @param $wId
     * @param $wNo
     * @param $user
     * @param $key
     * @param $value
     * @return int|\OC_DB_StatementWrapper
     */
    public function insertOrUpdateConfig($wId, $wNo, $user, $key, $value) {
        $wNo = intval($wNo);
        $cId = $this->getConfig($wId, $wNo, $user, $key, true);
        if($cId) {
            $sql = 'UPDATE `'.$this->table.'` SET `value` = ? WHERE `id` = ? ';
            $params = array($value, $cId);
        } else {
            $sql = 'INSERT INTO `' .$this->table.'`(`user`, `wid`, `wno`, `key`, `value`) VALUES(?,?,?,?,?)';
            $params = array($user, $wId, $wNo, $key, $value);
        }
        $query = $this->db->prepareQuery($sql);
        return $query->execute( $params );
    }

    /**
     *
     * read the value for the given key from database
     * set $returnId true, if you want to know the id of the config-database-row
     *
     * @param $wId
     * @param $wNo
     * @param $user
     * @param $key
     * @param bool $returnId
     * @return null|integer|string
     */
    public function getConfig($wId, $wNo, $user, $key, $returnId = false) {
        $sql = 'SELECT * FROM `'.$this->table.'` WHERE `wid` = ? AND `wno` = ? AND `user` = ? AND `key` = ?';
        $query = $this->db->prepareQuery($sql, 1);
        $wNo = intval($wNo);
        $result = $query->execute( array($wId, $wNo, $user, $key) );
        if( $row = $result->fetchRow() ) {
            if( $returnId ) {
                return intval( $row['id'] );
            } else {
                return $row['value'];
            }
        }
        return null;
    }

    /**
     *
     * look for the highest wNo
     *
     * @param $wId
     * @param $user
     * @return int
     */
    public function getHighestNo($wId, $user) {
        $sql = 'SELECT `wno` FROM `'.$this->table.'` WHERE `wid` = ? AND `user` = ? ORDER BY `wno` DESC';
        $query = $this->db->prepareQuery($sql, 1);
        $result = $query->execute( array($wId, $user) );
        if( $row = $result->fetchRow() ) {
            return intval($row['wno']);
        }
        return 0;
    }

    /**
     *
     * return array of wIId's that are enabled for the specified user
     *
     * @param $user
     * @return array
     */
    public function findEnabledWidgets($user) {
        $sql    = 'SELECT * FROM `'.$this->table.'` WHERE `user` = ? AND `key` = ? AND `value` = ? ORDER BY `id` DESC';
        $query  = $this->db->prepareQuery($sql);
        $result = $query->execute( array($user, 'enabled', '1') );
        $arr    = array();
        while( $row = $result->fetchRow() ) {
            $arr[] = $row['wid'].'-'.$row['wno'];
        }
        return $arr;
    }

    /**
     *
     * remove all settings by wIId
     * after that the widget-instance with its settings is deleted
     *
     * @param $wId
     * @param $wNo
     * @param $user
     * @return int|\OC_DB_StatementWrapper
     */
    public function removeWidgetConfigs($wId, $wNo, $user) {
        $sql = 'DELETE FROM `'.$this->table.'` WHERE `wid` = ? AND `wno` = ? AND `user` = ?';
        $query = $this->db->prepareQuery($sql);
        $wNo = intval($wNo);
        return $query->execute( array($wId, $wNo, $user) );
    }
}