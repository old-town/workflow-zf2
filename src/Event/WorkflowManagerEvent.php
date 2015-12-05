<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Event;

use OldTown\Workflow\WorkflowInterface;
use Zend\EventManager\Event;

/**
 * Class WorkflowManagerEvent
 *
 * @package OldTown\Workflow\ZF2\Event
 */
class WorkflowManagerEvent extends Event
{
    /**
     * Отображение результатов работы workflow
     *
     * @var string
     */
    const EVENT_CREATE         = 'workflow.manager.create';

    /**
     * @var WorkflowInterface
     */
    protected $workflowManager;

    /**
     * @return WorkflowInterface
     */
    public function getWorkflowManager()
    {
        return $this->workflowManager;
    }

    /**
     * @param WorkflowInterface $workflowManager
     *
     * @return $this
     */
    public function setWorkflowManager(WorkflowInterface $workflowManager)
    {
        $this->workflowManager = $workflowManager;

        return $this;
    }
}
