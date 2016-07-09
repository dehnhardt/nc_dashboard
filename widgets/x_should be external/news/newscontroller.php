<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\News;

use OCP\App;
use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;

class NewsController extends WidgetController implements IWidgetController {



    // interface needed methods ------------------------------------


    /**
     * see IWidgetController interface
     */
    public function setData() {
        $this->icon     =         'icons/98.png';
        $this->refresh  =                     60;
        $this->wId      =                 'news';
        $this->name     = $this->l10n->t('News');
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
            $news = $this->getNews();
            $data = array(
                'news'          => $news,
                'detailViewKey' => $this->getDetailViewKey($news),
                'showDetail'    => $this->getConfig('showDetail', true, 'bool'),
                'showList'      => $this->getConfig('showList', true, 'bool')
            );
        } else {
            $this->setStatus($this::STATUS_PROBLEM);
            $data = array(
                'msg'     => 'News app must be enabled.'
            );
        }

        // do not remove the following line
        // it creates the status information
        $this->setHash($data);
        return $data;
    }


    // ajax call methods ---------------------------------------------

    public function markAsRead( $newsId ) {
        $newsApp = $this->getNewsApp();
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        if( $newsApp instanceof \OCA\News\Service\ItemService) {
            $this->markAsReadFromItemService($newsApp, $newsId);
            return 1;
        }
        return 0;
    }

    // private services -------------------------------------------------

    private function getDetailViewKey($news) {
        if( count($news) > 0 ) {
            $foundOldId   = false;
            $lastDetailId = $this->getConfig('lastDetailId', '');
            foreach ( $news as $key => $item ) {
                if( $foundOldId ) {
                    $this->setConfig('lastDetailId', $item['id']);
                    return $key;
                }
                if( $item['id'] == $lastDetailId ) {
                    $foundOldId = true;
                }
            }
            $this->setConfig('lastDetailId', $news[0]['id']);
        }
        return 0;
    }

    /**
     * @return mixed
     */
    private function checkDepedencies() {
        /** @noinspection PhpUndefinedMethodInspection */
        return (App::isEnabled('news') && class_exists('\OCA\News\AppInfo\Application'));
    }

    /**
     *
     * return a object to handle news items
     * from the news app
     *
     * @return mixed|null
     */
    private function getNewsApp() {
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $app = new \OCA\News\AppInfo\Application();
        $container = $app->getContainer();
        $service = null;
        try {
            $service = $container->query('OCA\News\Service\ItemService');
        } catch (\InvalidArgumentException $e) {

        }

        if( !$service ) {
            $service = $container->query('ItemService');
        }
        return $service;
    }

    /**
	 * get all unread news-items from the news app
	 *
	 * @return array
	 */
    private function getNews() {
        // max age for items in hours
        $maxItemAge = time() - $this->getConfig('maxItemAge', 1) * 60 * 60;
        $itembusinesslayer = $this->getNewsApp();
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        if( $itembusinesslayer instanceof \OCA\News\Service\ItemService) {
            return $this->getNewsFromItemService($itembusinesslayer, $maxItemAge);
        } else {
            \OC_Log::write('dashboard', 'no correct class found for fetching news', \OC_Log::WARN);
            return array();
        }
    }

    /**
     * @param $itembusinesslayer \OCA\News\Service\ItemService
     * @param $maxItemAge
     * @return array
     */
    private function getNewsFromItemService($itembusinesslayer, $maxItemAge) {
        $items = $itembusinesslayer->findAllNew(0, 99, 0, false, $this->user);
        $news  = array();
        foreach ($items as $item) {
            /** @var $item \OCA\News\Db\Item */
            $n = $item->toApi();
            if( $n['pubDate'] >= $maxItemAge ) {
                $news[] = $n;
            }
        }
        return $news;
    }

    /**
     * @param $itembusinesslayer \OCA\News\Service\ItemService
     * @param $newsId
     */
    private function markAsReadFromItemService($itembusinesslayer, $newsId) {
        $itembusinesslayer->read($newsId, true, $this->user);
    }

} 