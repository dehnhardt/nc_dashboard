<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Bookmarks;

use OC_Bookmarks_Bookmarks;
use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;
use OCP\App;

class BookmarksController extends WidgetController implements IWidgetController {



    // interface needed methods ------------------------------------


    /**
     * see IWidgetController interface
     */
    public function setData() {
        $this->icon     =              'icons/83.png';
        $this->refresh  =                         360;
        $this->wId      =                 'bookmarks';
        $this->name     = $this->l10n->t('Bookmarks');
    }

    /**
     *
     * returns all the needed data as array
     * you can access them in the widgetTemplate->getContentHtml with $data['abc']
     *
     * @return array
     */
    public function getData() {
        if( $this->checkDepedencies() ) {
            $data = array(
                'bookmarks' => $this->getBookmarks()
            );
        } else {
            $this->setStatus($this::STATUS_PROBLEM);
            $data = array(
                'msg'     => 'Bookmarks app must be enabled.'
            );
        }

        // do not remove the following line
        // it creates the status information
        $this->setHash($data);
        return $data;
    }


    // ajax call methods ---------------------------------------------


    // private services -------------------------------------------------

    private function checkDepedencies() {
        return (App::isEnabled('bookmarks') &&
            (class_exists('OC_Bookmarks_Bookmarks') || class_exists('OCA\Bookmarks\Controller\Rest\BookmarkController'))
        );
    }

    /**
     * @return array
     */
    private function getBookmarks() {
        if( class_exists('OC_Bookmarks_Bookmarks') ) {
            return $this->getBookmarksOC7();
        } elseif( class_exists('OCA\Bookmarks\Controller\Rest\BookmarkController')) {
            return $this->getBookmarksOC8();
        }

    }

    private function getBookmarksOC7() {
        $filters = $this->getConfig('tagKeyword', 'Dashboard');
        /** @var array $bookmarks */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $bookmarks = OC_Bookmarks_Bookmarks::findBookmarks(0, 'clickcount', $filters, true, -1);
        return $bookmarks;
    }

    private function getBookmarksOC8() {
        $filters = $this->getConfig('tagKeyword', 'Dashboard');
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedNamespaceInspection */
        $bookmarksApp = new \OCA\Bookmarks\AppInfo\Application();
        if( $bookmarksApp ) {
            $container = $bookmarksApp->getContainer();
            $bookmarksController = $container->query('BookmarkController');
            $bookmarks = $bookmarksController->getBookmarks('bookmark', $filters);
            $bookmarks = $bookmarks->getData();
            return $bookmarks['data'];
        }
        return;
    }
}