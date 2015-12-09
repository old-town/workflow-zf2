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
 * Class TransitionResultTrait
 *
 * @package OldTown\Workflow\ZF2\Service\Workflow
 */
trait TransitionResultTrait
{

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
     *
     * @var string
     */
    protected $viewName;

    /**
     * @var WorkflowInterface
     */
    protected $workflowManager;

    /**
     *
     * @var integer
     */
    protected $entryId;

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

    /**
     * @return string
     */
    public function getViewName()
    {
        return $this->viewName;
    }

    /**
     * @param string $viewName
     *
     * @return $this
     */
    public function setViewName($viewName)
    {
        $this->viewName = (string)$viewName;

        return $this;
    }

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

    /**
     * @return int
     */
    public function getEntryId()
    {
        return $this->entryId;
    }

    /**
     * @param int $entryId
     *
     * @return $this
     */
    public function setEntryId($entryId)
    {
        $this->entryId = (integer)$entryId;

        return $this;
    }
}
