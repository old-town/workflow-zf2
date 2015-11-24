<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Event;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use Zend\EventManager\Event;

/**
 * Class WorkflowEvent
 *
 * @package OldTown\Workflow\ZF2\Event
 */
class WorkflowEvent extends Event
{
    /**
     * Отображение результатов работы workflow
     *
     * @var string
     */
    const EVENT_RENDER         = 'render';

    /**
     * @var WorkflowDescriptor
     */
    protected $workflow;

    /**
     * Переменные контекста исполнения workflow
     *
     * @var TransientVarsInterface
     */
    protected $transientVars;

    /**
     * @return WorkflowDescriptor
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @param WorkflowDescriptor $workflow
     *
     * @return $this
     */
    public function setWorkflow(WorkflowDescriptor $workflow)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * @return TransientVarsInterface
     */
    public function getTransientVars()
    {
        return $this->transientVars;
    }

    /**
     * @param TransientVarsInterface $transientVars
     *
     * @return $this
     */
    public function setTransientVars(TransientVarsInterface $transientVars)
    {
        $this->transientVars = $transientVars;

        return $this;
    }
}
