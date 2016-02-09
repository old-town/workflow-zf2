<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Service\Workflow;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\WorkflowInterface;

/**
 * Interface TransitionResultInterface
 *
 * @package OldTown\Workflow\ZF2\Service\Workflow
 */
interface TransitionResultInterface
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
     * @return TransientVarsInterface
     */
    public function getTransientVars();

    /**
     * @param TransientVarsInterface $transientVars
     *
     * @return $this
     */
    public function setTransientVars(TransientVarsInterface $transientVars);

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
