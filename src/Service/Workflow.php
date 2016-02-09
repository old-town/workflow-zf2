<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Service;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\WorkflowInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Traversable;
use Zend\Stdlib\ArrayUtils;
use OldTown\Workflow\TransientVars\BaseTransientVars;
use OldTown\Workflow\ZF2\Event\WorkflowEvent;
use OldTown\Workflow\ZF2\Service\Workflow\TransitionResult;
use OldTown\Workflow\ZF2\Service\Workflow\TransitionResultInterface;


/**
 * Class Workflow
 *
 * @package OldTown\Workflow\ZF2\Service
 *
 */
class Workflow
{
    use ServiceLocatorAwareTrait, EventManagerAwareTrait;

    /**
     * Паттерн для получения имени сервиса workflow
     *
     * @var string
     */
    protected $workflowManagerServiceNamePattern = 'workflow.manager.%s';

    /**
     * @param $options
     *
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidArgumentException
     */
    public function __construct($options)
    {
        $this->init($options);
    }

    /**
     * Инициализация сервиса
     *
     * @param $options
     *
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidArgumentException
     */
    protected function init($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            $errMsg = sprintf('%s  expects an array or Traversable config', __METHOD__);
            throw new Exception\InvalidArgumentException($errMsg);
        }

        if (!array_key_exists('serviceLocator', $options)) {
            $errMsg = 'Argument serviceLocator not found';
            throw new Exception\InvalidArgumentException($errMsg);
        }
        $this->setServiceLocator($options['serviceLocator']);
    }

    /**
     * Запуск перехода из отдного состояния в другое
     *
     * @param $managerName
     * @param $entryId
     * @param $actionName
     *
     * @return TransitionResultInterface
     *
     * @throws \OldTown\Workflow\ZF2\Service\Exception\DoActionException
     */
    public function doAction($managerName, $entryId, $actionName)
    {
        try {
            $event = new WorkflowEvent();
            $event->setTarget($this);
            $event->setEntryId($entryId);

            $manager = $this->getWorkflowManager($managerName);
            $event->setWorkflowManager($manager);

            $workflowStore = $manager->getConfiguration()->getWorkflowStore();

            $entry = $workflowStore->findEntry($entryId);
            $workflowName = $entry->getWorkflowName();

            $wf = $manager->getConfiguration()->getWorkflow($workflowName);
            $event->setWorkflow($wf);

            $action = $this->getActionByName($wf, $actionName);
            $actionId = $action->getId();

            $input = new BaseTransientVars();
            $event->setTransientVars($input);

            $manager->doAction($entryId, $actionId, $input);

            $this->getEventManager()->trigger(WorkflowEvent::EVENT_DO_ACTION, $this, $event);

            $viewName = $action->getView();
            if (null !== $viewName) {
                $event->setViewName($viewName);
                $event->setName(WorkflowEvent::EVENT_RENDER);
                $this->getEventManager()->trigger($event);
            }
        } catch (\Exception $e) {
            throw new Exception\DoActionException($e->getMessage(), $e->getCode(), $e);
        }

        $result = new TransitionResult($entryId, $manager, $wf, $input);
        if ($viewName) {
            $result->setViewName($viewName);
        }

        return $result;
    }

    /**
     * Создание процесса workflow
     *
     * @param $managerName
     * @param $workflowName
     * @param $actionName
     *
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidInitializeWorkflowEntryException
     *
     * @return TransitionResultInterface
     */
    public function initialize($managerName, $workflowName, $actionName)
    {
        try {
            $event = new WorkflowEvent();
            $event->setTarget($this);

            $manager = $this->getWorkflowManager($managerName);
            $event->setWorkflowManager($manager);
            $wf = $manager->getConfiguration()->getWorkflow($workflowName);
            $event->setWorkflow($wf);

            $actionId = null;
            $initialActions = $wf->getInitialActions();

            foreach ($initialActions as $initialAction) {
                if ($actionName === $initialAction->getName()) {
                    $actionId = $initialAction->getId();
                    break;
                }
            }

            if (null === $actionId) {
                $errMsg = sprintf('Action %s not found', $actionName);
                throw new Exception\ActionNotFoundException($errMsg);
            }

            $input = new BaseTransientVars();
            $event->setTransientVars($input);
            $entryId = $manager->initialize($workflowName, $actionId, $input);
            $event->setEntryId($entryId);

            $this->getEventManager()->trigger(WorkflowEvent::EVENT_WORKFLOW_INITIALIZE, $this, $event);


            $initialActions = $wf->getInitialAction($actionId);
            $viewName = $initialActions->getView();
            if (null !== $viewName) {
                $event->setViewName($viewName);
                $event->setName(WorkflowEvent::EVENT_RENDER);
                $this->getEventManager()->trigger($event);
            }
        } catch (\Exception $e) {
            throw new Exception\InvalidInitializeWorkflowEntryException($e->getMessage(), $e->getCode(), $e);
        }

        $result = new TransitionResult($entryId, $manager, $wf, $input);
        if ($viewName) {
            $result->setViewName($viewName);
        }

        return $result;
    }


    /**
     * Получение менеджера workflow по имени
     *
     * @param string $managerName
     *
     * @return WorkflowInterface
     *
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidWorkflowManagerException
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidManagerNameException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function getWorkflowManager($managerName)
    {
        if (!$this->hasWorkflowManager($managerName)) {
            $errMsg = sprintf('Invalid workflow manager name %s', $managerName);
            throw new Exception\InvalidManagerNameException($errMsg);
        }
        $workflowManagerServiceName = $this->getWorkflowManagerServiceName($managerName);

        $manager =  $this->getServiceLocator()->get($workflowManagerServiceName);

        if (!$manager instanceof WorkflowInterface) {
            $errMsg = sprintf('Workflow manager not implement %s', WorkflowInterface::class);
            throw new Exception\InvalidWorkflowManagerException($errMsg);
        }

        return $manager;
    }

    /**
     * @param WorkflowDescriptor $wf
     * @param                    $actionName
     *
     * @return null|\OldTown\Workflow\Loader\ActionDescriptor
     */
    public function getActionByName(WorkflowDescriptor $wf, $actionName)
    {
        $actionName = (string)$actionName;

        foreach ($wf->getGlobalActions() as $actionDescriptor) {
            if ($actionName === $actionDescriptor->getName()) {
                return $actionDescriptor;
            }
        }

        foreach ($wf->getSteps() as $stepDescriptor) {
            $actions = $stepDescriptor->getActions();
            foreach ($actions as $actionDescriptor) {
                if ($actionName === $actionDescriptor->getName()) {
                    return $actionDescriptor;
                }
            }
        }

        foreach ($wf->getInitialActions() as $actionDescriptor) {
            if ($actionName === $actionDescriptor->getName()) {
                return $actionDescriptor;
            }
        }

        return null;
    }

    /**
     * Проверят есть ли менеджер workflow с заданным именем
     *
     * @param string $workflowManagerName
     *
     * @return bool
     */
    public function hasWorkflowManager($workflowManagerName)
    {
        $workflowManagerServiceName = $this->getWorkflowManagerServiceName($workflowManagerName);

        return $this->getServiceLocator()->has($workflowManagerServiceName);
    }

    /**
     * Имя сервиса для получения менеджера workflow
     *
     * @param string $workflowManagerName
     *
     * @return string
     */
    public function getWorkflowManagerServiceName($workflowManagerName)
    {
        return sprintf($this->getWorkflowManagerServiceNamePattern(), $workflowManagerName);
    }

    /**
     * @return string
     */
    public function getWorkflowManagerServiceNamePattern()
    {
        return $this->workflowManagerServiceNamePattern;
    }

    /**
     * @param string $workflowManagerServiceNamePattern
     *
     * @return $this
     */
    public function setWorkflowManagerServiceNamePattern($workflowManagerServiceNamePattern)
    {
        $this->workflowManagerServiceNamePattern = (string)$workflowManagerServiceNamePattern;

        return $this;
    }
}
