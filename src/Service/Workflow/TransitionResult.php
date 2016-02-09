<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Service\Workflow;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\WorkflowInterface;


/**
 * Class TransitionResult
 *
 * @package OldTown\Workflow\ZF2\Service\Workflow
 */
class TransitionResult implements TransitionResultInterface
{
    use TransitionResultTrait;

    /**
     * @param                           $entryId
     * @param WorkflowInterface         $workflowManager
     * @param WorkflowDescriptor        $workflow
     * @param TransientVarsInterface    $transientVars
     */
    public function __construct($entryId, WorkflowInterface $workflowManager, WorkflowDescriptor $workflow, TransientVarsInterface $transientVars)
    {
        $this->setEntryId($entryId);
        $this->setWorkflowManager($workflowManager);
        $this->setWorkflow($workflow);
        $this->setTransientVars($transientVars);
    }
}
