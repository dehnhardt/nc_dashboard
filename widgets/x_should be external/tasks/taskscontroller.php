<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 16.12.14
 * Time: 08:02
 */

namespace OCA\Dashboard\Widgets\Tasks;

use OCA\Dashboard\Widgets\IWidgetController;
use OCA\Dashboard\Widgets\WidgetController;
use OCA\Tasks\AppInfo\Application as TaskApp;
use OCP\App;

class TasksController extends WidgetController implements IWidgetController {



    // interface needed methods ------------------------------------


    /**
     * see IWidgetController interface
     */
    public function setData() {
        $this->icon     =          'icons/49.png';
        $this->refresh  =                     120;
        $this->wId      =                 'tasks';
        $this->name     = $this->l10n->t('Tasks');
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
                'tasks'     => $this->getTasks(true),
                'calendars' => $this->getCalendars()
            );
        } else {
            $this->setStatus($this::STATUS_PROBLEM);
            $data = array(
                'msg'     => 'Calendar and tasks app must be enabled.'
            );
        }

        // do not remove the following line
        // it creates the status information
        $this->setHash($data);
        return $data;
    }


    // ajax call methods ---------------------------------------------

    /**
     * @param $taskId
     * @return string
     */
    public function markAsDone( $taskId ) {
        //$params = array('taskID' => $taskId);
        //$tasksAppTaskController = $this->getTasksAppTaskController($params);
        //return $tasksAppTaskController->completeTask();
        // ToDo
        return 'This function is not yet implemented. (id='.$taskId.')';
    }

    // private services -------------------------------------------------

    private function checkDependencies() {
        return App::isEnabled('calendar')&&App::isEnabled('tasks');
    }

    /**
     * fetch a instance of the TasksController from the tasks app
     *
     * @param array string with values for the DIContainer
     * @return \OCA\Tasks\Controller\TasksController
     */
    private function getTasksAppTaskController($params = Array()) {
        $taskApp                    = new TaskApp($params);
        $taskContainer              = $taskApp->getContainer();
        /** @var $tasksController \OCA\Tasks\Controller\TasksController */
        $tasksAppTaskController     = $taskContainer->query('TasksController');
        return $tasksAppTaskController;
    }

    /**
     * @param bool $onlyNotCompleted
     * @return Array with tasks as array
     */
    private function getTasks($onlyNotCompleted = false) {
        $tasksAppTasksController    = $this->getTasksAppTaskController();
        $tasks                      = $tasksAppTasksController->getTasks()->getData();
        if( $onlyNotCompleted ) {
            $return = array();
            foreach ($tasks['data']['tasks'] as $task) {
                if( $task['completed'] != true && $task['complete'] != '100') {
                    $return[] = $task;
                }
            }
        } else {
            $return = $tasks['data']['tasks'];
        }
        return $return;
    }

    /**
     *
     * return array
     * key   = calendar id
     * value = name of calendar
     *
     * @return array
     */
    private function getCalendars() {
        $calendars = Array();
        foreach( \OC_Calendar_Calendar::allCalendars($this->user, true) as $cal ) {
            if( $cal['active'] == '1' ) {
                $calendars[$cal['id']] = array(
                    'name'      => $cal['displayname'],
                    'color'     => $cal['calendarcolor']
                );
            }
        }
        return $calendars;
    }

} 