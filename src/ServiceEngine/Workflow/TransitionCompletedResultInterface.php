<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ServiceEngine\Workflow;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\WorkflowInterface;

/**
 * Interface TransitionCompletedResultInterface
 *
 * @package OldTown\Workflow\ZF2\ServiceEngine\Workflow
 */
interface TransitionCompletedResultInterface extends TransitionResultInterface
{
    /**
     * @return WorkflowDescriptor
     */
    public function getWorkflow();

    /**
     * @param WorkflowDescriptor $workflow
     *
     * @return $this
     */
    public function setWorkflow(WorkflowDescriptor $workflow);

    /**
     * @return string
     */
    public function getViewName();

    /**
     * @param string $viewName
     *
     * @return $this
     */
    public function setViewName($viewName);

    /**
     * @return WorkflowInterface
     */
    public function getWorkflowManager();

    /**
     * @param WorkflowInterface $workflowManager
     *
     * @return $this
     */
    public function setWorkflowManager(WorkflowInterface $workflowManager);

    /**
     * @return int
     */
    public function getEntryId();

    /**
     * @param int $entryId
     *
     * @return $this
     */
    public function setEntryId($entryId);
}
