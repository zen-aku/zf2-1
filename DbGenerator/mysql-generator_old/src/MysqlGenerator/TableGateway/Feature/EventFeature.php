<?php

namespace MysqlGenerator\TableGateway\Feature;

use MysqlGenerator\Adapter\Driver\ResultInterface;
use MysqlGenerator\Adapter\Driver\StatementInterface;
use MysqlGenerator\ResultSet\ResultSetInterface;
use MysqlGenerator\Sql\Delete;
use MysqlGenerator\Sql\Insert;
use MysqlGenerator\Sql\Select;
use MysqlGenerator\Sql\Update;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;

class EventFeature extends AbstractFeature implements EventsCapableInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var null
     */
    protected $event = null;

    /**
     * @param EventManagerInterface $eventManager
     * @param EventFeature\TableGatewayEvent $tableGatewayEvent
     */
    public function __construct(
        EventManagerInterface $eventManager = null,
        EventFeature\TableGatewayEvent $tableGatewayEvent = null
    ) {
        $this->eventManager = ($eventManager instanceof EventManagerInterface)
                            ? $eventManager
                            : new EventManager;

        $this->eventManager->addIdentifiers(array(
            'MysqlGenerator\TableGateway\TableGateway',
        ));

        $this->event = ($tableGatewayEvent) ?: new EventFeature\TableGatewayEvent();
    }

    /**
     * Retrieve composed event manager instance
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Retrieve composed event instance
     *
     * @return EventFeature\TableGatewayEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Initialize feature and trigger "preInitialize" event
     *
     * Ensures that the composed TableGateway has identifiers based on the
     * class name, and that the event target is set to the TableGateway
     * instance. It then triggers the "preInitialize" event.
     *
     * @return void
     */
    public function preInitialize()
    {
        if (get_class($this->tableGateway) != 'MysqlGenerator\TableGateway\TableGateway') {
            $this->eventManager->addIdentifiers(get_class($this->tableGateway));
        }

        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postInitialize" event
     *
     * @return void
     */
    public function postInitialize()
    {
        $this->event->setName(__FUNCTION__);
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "preSelect" event
     *
     * Triggers the "preSelect" event mapping the following parameters:
     * - $select as "select"
     *
     * @param  Select $select
     * @return void
     */
    public function preSelect(Select $select)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('select' => $select));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postSelect" event
     *
     * Triggers the "postSelect" event mapping the following parameters:
     * - $statement as "statement"
     * - $result as "result"
     * - $resultSet as "result_set"
     *
     * @param  StatementInterface $statement
     * @param  ResultInterface $result
     * @param  ResultSetInterface $resultSet
     * @return void
     */
    public function postSelect(StatementInterface $statement, ResultInterface $result, ResultSetInterface $resultSet)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
            'result_set' => $resultSet
        ));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "preInsert" event
     *
     * Triggers the "preInsert" event mapping the following parameters:
     * - $insert as "insert"
     *
     * @param  Insert $insert
     * @return void
     */
    public function preInsert(Insert $insert)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('insert' => $insert));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postInsert" event
     *
     * Triggers the "postInsert" event mapping the following parameters:
     * - $statement as "statement"
     * - $result as "result"
     *
     * @param  StatementInterface $statement
     * @param  ResultInterface $result
     * @return void
     */
    public function postInsert(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "preUpdate" event
     *
     * Triggers the "preUpdate" event mapping the following parameters:
     * - $update as "update"
     *
     * @param  Update $update
     * @return void
     */
    public function preUpdate(Update $update)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('update' => $update));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postUpdate" event
     *
     * Triggers the "postUpdate" event mapping the following parameters:
     * - $statement as "statement"
     * - $result as "result"
     *
     * @param  StatementInterface $statement
     * @param  ResultInterface $result
     * @return void
     */
    public function postUpdate(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "preDelete" event
     *
     * Triggers the "preDelete" event mapping the following parameters:
     * - $delete as "delete"
     *
     * @param  Delete $delete
     * @return void
     */
    public function preDelete(Delete $delete)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('delete' => $delete));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postDelete" event
     *
     * Triggers the "postDelete" event mapping the following parameters:
     * - $statement as "statement"
     * - $result as "result"
     *
     * @param  StatementInterface $statement
     * @param  ResultInterface $result
     * @return void
     */
    public function postDelete(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }
}
