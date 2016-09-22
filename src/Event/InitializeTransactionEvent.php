<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Event;

/**
 * Class InitializeTransactionEvent
 *
 * @package OldTown\Workflow\ZF2\Event
 */
class InitializeTransactionEvent extends AbstractTransactionEvent
{
    /**
     * Имя workflow
     *
     * @var string
     */
    private $workflowName;

    /**
     * InitializeTransactionEvent constructor.
     *
     * @param string $managerName
     * @param string $workflowName
     * @param string $actionName
     */
    public function __construct($managerName, $workflowName, $actionName)
    {
        $this->workflowName = $workflowName;
        parent::__construct($managerName, $actionName);
    }

    /**
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }
}
