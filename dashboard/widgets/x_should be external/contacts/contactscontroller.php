<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Contacts;

use OC;
use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;
use OCP\App;

class ContactsController extends WidgetController implements IWidgetController {



    // interface needed methods ------------------------------------


    /**
     * see IWidgetController interface
     */
    public function setData() {
        $this->icon     =             'icons/76.png';
        $this->refresh  =                          0;
        $this->wId      =                 'contacts';
        $this->name     = $this->l10n->t('Contacts');
    }

    /**
     *
     * returns all the needed data as array
     * you can access them in the widgetTemplate->getContentHtml with $data['abc']
     *
     * @return array
     */
    public function getData() {
        if( $this->checkDependencies() ) {
            $data = array(
            );
        } else {
            $this->setStatus($this::STATUS_PROBLEM);
            $data = array(
                'msg'     => 'Dependency error.'
            );
        }

        // do not remove the following line
        // it creates the status information
        $this->setHash($data);
        return $data;
    }


    // ajax call methods ------------------------------------------------

    public function getContacts($term) {
        /** @noinspection PhpUndefinedClassInspection */
        $cm = OC::$server->getContactsManager();
        // The API is not active -> nothing to do
        if (!$cm->isEnabled()) {
            json_encode( array() );
        }

        $result = $cm->search($term, array('FN', 'EMAIL'));
        $receivers = array();
        foreach ($result as $r) {
            $id = $r['id'];
            $fn = $r['FN'];

            $email = (isset($r['EMAIL'])) ? $r['EMAIL']: array();
            if (!is_array($email)) {
                $email = array($email);
            }

            $phone = (isset($r['TEL'])) ? $r['TEL']: array();
            if ( !is_array($phone) ) {
                $phone = array($phone);
            }

            $receivers[] = array(
                'id'    => $id,
                'fn'    => $fn,
                'mail'  => $email,
                'phone' => $phone
            );
        }

        return json_encode( $receivers );
    }

    public function getDetails( $contactId ) {
        return array('test'.$contactId);
    }

    // private services -------------------------------------------------

    private function checkDependencies() {
        return App::isEnabled('contacts');
    }
} 