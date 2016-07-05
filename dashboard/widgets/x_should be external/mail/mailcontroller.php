<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Mail;

use DateTime;
use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;

class MailController extends WidgetController implements IWidgetController {

    private $connection;

    // interface needed methods ------------------------------------


    /**
     * see IWidgetController interface
     */
    public function setData() {
        $this->icon     =         'icons/35.png';
        $this->refresh  =                    360;
        $this->wId      =                 'mail';
        $this->name     = $this->l10n->t('Mail');
    }

    /**
     *
     * returns all the needed data as array
     * you can access them in the widgetTemplate->getContentHtml with $data['abc']
     *
     * @return array
     */
    public function getData() {
        $data = array(
            'mails' => $this->getMails()
        );

        // do not remove the following line
        // it creates the status information
        $this->setHash($data);
        return $data;
    }


    // ajax call methods ---------------------------------------------

    // private services -------------------------------------------------


    private function getMails() {
        $mails = array();
        if($this->createConnection()) {
            $unseen = imap_search($this->connection, 'UNSEEN'); // fetch only unseen mails... much faster
            $index = '';
            if($unseen) {
                foreach($unseen as $uMail) {
                    $index .= $uMail.",";
                }
            }
            $mails = array_reverse(imap_fetch_overview($this->connection, "$index"));
        }
        $this->closeConnection();

        $cleanMails = array();
        foreach( $mails as $mail) {
            $tmp = array();
            $tmp['subject'] = $this->decodeSubject($mail->subject);
            $tmp['from']    = $this->decodeSubject($mail->from);
            $datetime       = new DateTime($mail->date);
            $tmp['datetime']= $datetime->getTimestamp()+$datetime->getOffset();
            $cleanMails[]   = $tmp;
        }
        return $cleanMails;
    }

    private function decodeSubject($subject) {
        return iconv_mime_decode($subject, 0, "UTF-8");
    }

    private function closeConnection() {
        if( $this->connection ) {
            imap_close($this->connection);
        }
    }

    /**
     *
     * $mode = {'pop3', 'imap', 'nntp'}
     * $cypher = {'ssl', 'ssl (novalidate-cert)', 'notls', 'none'}
     *
     * @return bool|resource
     */
    private function createConnection() {
        $host   = $this->getConfig('host', '');
        $user   = $this->getConfig('mailboxUser', '');
        $pass   = $this->getConfig('password', '');
        $port   = $this->getConfig('port', '110');
        $folder = $this->getConfig('folder', 'INBOX');
        $mode   = $this->getConfig('mode', 'imap');
        $cypher = $this->getConfig('cypher', 'none');

        //$pass   = utf8_decode($pass);
        //$user   = utf8_decode($user);

        // validation
        if( $host == '' || $user == '') {
            \OC_Log::write('dashboard', 'host or user are missing for mail widget', \OC_Log::WARN);
        } else {
            $loginString = $this->getLoginString($host, $port, $folder, $cypher, $mode);
            $this->connection = $this->login($loginString, $user, $pass);
        }
        if( !$this->connection ) {
            $this->setStatus($this::STATUS_PROBLEM);
            \OC_Log::write('dashboard', 'could not connect to mailbox in mail widget', \OC_Log::ERROR);
            return false;
        } else {
            return true;
        }
    }

    private function login($loginString, $user, $pass) {
        $stream = imap_open($loginString, $user, $pass);
        if($stream) {
            return $stream;
        } else {
            \OC_Log::write('dashboard', 'mail widget connection-string: '.$loginString, \OC_Log::ERROR);
            return null;
        }
    }

    /**
     * $mode   = {'pop3', 'imap', 'nntp'}
     * $cypher = {'ssl', 'ssl (novalidate-cert)', 'notls', 'none'}
     *
     * @param $host
     * @param $port
     * @param string $folder
     * @param $cypher
     * @param $mode
     * @return string
     */
    private function getLoginString($host, $port, $folder="INBOX", $cypher, $mode) {
        $way = '/'.$mode;
        switch ($cypher) {
            case 'ssl':
                $way .= '/ssl';
                break;
            case 'ssl (novalidate-cert)':
                $way .= '/ssl/novalidate-cert';
                break;
            case 'notls':
                $way .= '/notls';
                break;
            case 'tls':
                $way .= '/tls';
                break;
        }
        return '{'.$host.':'.$port.$way.'}'.$folder;
    }

} 