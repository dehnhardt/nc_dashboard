<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Mail;


use OCA\Dashboard\Widgets\IWidgetTemplate;
use OCA\Dashboard\Widgets\WidgetTemplate;

class MailTemplate extends WidgetTemplate implements IWidgetTemplate {

    function getContentHtml($data = array()) {
        $html = '<table>';
        foreach ($data['mails'] as $mail) {
            $html .= '<tr>
                        <td><div class="subject">'.$this->p($mail['subject']).'</div>
                        <div class="from">'.$this->l10n->t('from').' '.$this->p($mail['from']).'</div>
                        <div class="datetime">'.$this->l10n->l('datetime', $mail['datetime']).'</div>
                        </td>
                      </tr>';
        }
        $html .= '</table>';
        return $html;
    }

    function getSettingsArray()
    {
        return array(
            'host'  => array(
                'type'      => 'text',
                'default'   => '',
                'name'      => 'Host server'
            ),
            'port'  => array(
                'type'      => 'text',
                'default'   => '',
                'name'      => 'Port of the Server'
            ),
            'mailboxUser'  => array(
                'type'      => 'text',
                'default'   => '',
                'name'      => 'User',
                'info'      => 'mostly its the mail address'
            ),
            'password'  => array(
                'type'      => 'password',
                'default'   => '',
                'name'      => 'Password'
            ),
            'mode'     => array(
                'type'          => 'select',
                'options'       => array(
                    'pop3'           => 'POP3',
                    'imap'           => 'IMAP',
                    'nntp'           => 'NNTP'
                ),
                'name'          => 'Mode',
                'default'       => 'pop3'
            ),
            'cypher'     => array(
                'type'          => 'select',
                'options'       => array(
                    'ssl'                   => 'SSL',
                    'ssl (novalidate-cert)' => 'SSL (no cert validation)',
                    'tls'                   => 'TLS',
                    'notls'                 => 'NO TLS',
                    'none'                  => 'none'
                ),
                'name'          => 'Cypher mode',
                'default'       => 'none'
            ),
        );
    }
}