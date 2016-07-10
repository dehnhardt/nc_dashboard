<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 04.07.15
 * Time: 10:33
 */

namespace OCA\Dashboard\Utils;


class Helper {

    static function wId($wIId) {
        $split  = explode('-', $wIId);
        return $split[0];
    }

    static function wNo($wIId) {
        $split  = explode('-', $wIId);
        return $split[1];
    }

    static function wIId($wId, $wNo) {
        return $wId.'-'.$wNo;
    }

    static function encrypt($text) {
        /** @noinspection PhpUndefinedClassInspection */
        return OC::$server->getCrypto()->encrypt($text);
    }

    static function decrypt($cypher) {
        /** @noinspection PhpUndefinedClassInspection */
        return OC::$server->getCrypto()->decrypt($cypher);
    }

    static function gId($wIdG) {
        $split  = explode('-', $wIdG);
        return $split[1];
    }

}