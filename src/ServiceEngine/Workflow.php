<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ServiceEngine;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\WorkflowInterface;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow\TransitionCompletedResultInterface;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow\TransitionErrorResultInterface;
use OldTown\Workflow\ZF2\Transaction\WorkflowTransactionEvent;
use OldTown\Workflow\ZF2\Transaction\WorkflowTransactionEventInterface;
use Zend\EventManager\EventManagerAwareTrait;
use ReflectionClass;
use Zend\ServiceManager\ServiceLocatorInterface;
use OldTown\Workflow\TransientVars\BaseTransientVars;
use OldTown\Workflow\ZF2\Event\WorkflowEvent;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow\TransitionCompletedResult;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow\TransitionResultInterface;
use OldTown\Workflow\Loader\ActionDescriptor;
use OldTown\Workflow\ZF2\Transaction\WorkflowTransactionServiceInterface;


/**
 * Class Workflow
 *
 * @package OldTown\Workflow\ZF2\ServiceEngine
 */
class Workflow implements WorkflowServiceInterface, WorkflowTransactionServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * Паттерн для получения имени сервиса workflow
     *
     * @var string
     */
    protected $workflowManagerServiceNamePattern = 'workflow.manager.%s';

    /**
     * Контейнер для получения экземпляров workflow
     *
     * @var ServiceLocatorInterface
     */
    protected $workflowContainer;

    /**
     * Псевдонимы для менеджеров workflow
     *
     * @var array
     */
    protected $managerAliases = [];

    /**
     * Имя класса  - реализующего событие используемое для работы с транзакциями
     *
     * @var string
     */
    protected $workflowTransactionEventClassName = WorkflowTransactionEvent::class;

    /**
     * Имя класса реализующего интерфейс описывающий результаты успешного запуска Workflow
     *
     * @var string
     */
    protected $transitionCompletedResultClassName = TransitionCompletedResult::class;


    /**
     * Имя класса реализующего интерфейс описывающий результаты не успешного запуска Workflow
     *
     * @var string
     */
    protected $transitionErrorResultClassName = TransitionCompletedResult::class;

    /**
     * Список добавляемых идендификаторов в EventManager
     *
     * @var array
     */
    protected $eventIdentifier = [
        WorkflowTransactionServiceInterface::class
    ];

    /**
     * Workflow constructor.
     *
     * @param ServiceLocatorInterface $workflowContainer
     */
    public function __construct(ServiceLocatorInterface $workflowContainer)
    {
        $this->setWorkflowContainer($workflowContainer);
    }

    /**
     * Запуск перехода из отдного состояния в другое
     *
     * @param                        $managerName
     * @param                        $entryId
     * @param                        $actionName
     *
     * @param TransientVarsInterface $transientVars
     *
     * @return TransitionResultInterface
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\RuntimeException
     *
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\DoActionException
     */
    public function doAction($managerName, $entryId, $actionName, TransientVarsInterface $transientVars = null)
    {
        $eventManager = $this->getEventManager();
        $transactionEvent = $this->workflowTransactionEventFactory();
        try {
            $eventManager->trigger($transactionEvent);

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

            //$action = $this->getActionByName($wf, $actionName);
            $action = $this->findActionByNameForEntry($managerName, $entryId, $actionName);


            $actionId = $action->getId();

            if (null === $transientVars) {
                $transientVars = new BaseTransientVars();
            }

            $event->setTransientVars($transientVars);

            $transactionEvent->setState(WorkflowTransactionEventInterface::START_STATE);
            $eventManager->trigger($transactionEvent);

            $manager->doAction($entryId, $actionId, $transientVars);

            $eventManager->trigger(WorkflowEvent::EVENT_DO_ACTION, $this, $event);

            $viewName = $action->getView();
            if (null !== $viewName) {
                $event->setViewName($viewName);
                $event->setName(WorkflowEvent::EVENT_RENDER);
                $eventManager->trigger($event);
            }

            $result = $this->transitionCompletedResultFactory($entryId, $manager, $wf, $transientVars);

            if ($viewName) {
                $result->setViewName($viewName);
            }

            $transactionEvent->setState(WorkflowTransactionEventInterface::COMMIT_STATE);
            $eventManager->trigger($transactionEvent);
        } catch (\Exception $e) {
            $transactionEvent->setState(WorkflowTransactionEventInterface::ROLLBACK_STATE);
            $eventManager->trigger($transactionEvent);

            if (false === $transactionEvent->getFlagSuppressException()) {
                throw new Exception\DoActionException($e->getMessage(), $e->getCode(), $e);
            }

            $transitionErrorResult = $this->transitionErrorResultFactory($e);
            $transitionErrorResult->setTransientVars($transientVars);

            $result = $this->transitionErrorResultFactory($e);
        }


        return $result;
    }

    /**
     * Возвращает доступные действия для текущего состояния процесса
     *
     * @param $managerName
     * @param $entryId
     *
     * @return array|ActionDescriptor[]
     *
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidManagerNameException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidWorkflowManagerException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function getAvailableActions($managerName, $entryId)
    {
        $manager = $this->getWorkflowManager($managerName);
        $currentSteps = $manager->getCurrentSteps($entryId);

        $entry = $manager->getConfiguration()->getWorkflowStore()->findEntry($entryId);
        $workflowName = $entry->getWorkflowName();

        $wf = $manager->getConfiguration()->getWorkflow($workflowName);

        $findActions = [];
        foreach ($currentSteps as $currentStep) {
            $stepId = $currentStep->getStepId();
            $step = $wf->getStep($stepId);
            $actions = $step->getActions();

            foreach ($actions as $action) {
                $findActions[] = $action;
            }
        }

        return $findActions;
    }


    /**
     * Ишет действие по имени. Поиск происходит в рамках текущего step'a.
     *
     * @param $managerName
     * @param $entryId
     * @param $actionName
     *
     * @return ActionDescriptor|null
     *
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidManagerNameException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidWorkflowManagerException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidWorkflowActionNameException
     * @throws \OldTown\Workflow\Exception\FactoryException
     */
    public function findActionByNameForEntry($managerName, $entryId, $actionName)
    {
        $actions = $this->getAvailableActions($managerName, $entryId);
        $findActions = [];
        foreach ($actions as $action) {
            if ($actionName === $action->getName()) {
                $findActions[] = $action;
            }
        }


        $countActions = count($findActions);
        if ($countActions > 1) {
            $errMsg = sprintf(
                'Found more than one action workflow. Manager name: %s. Name action: %s. Ids: %s',
                $managerName,
                $actionName,
                implode(',', array_map(function ($action) {
                    return is_object($action) && method_exists($action, 'getId') ? call_user_func([$action, 'getId']) : '';
                }, $findActions))
            );

            throw new Exception\InvalidWorkflowActionNameException($errMsg);
        }
        $findAction = null;
        if (1 === $countActions) {
            reset($findActions);
            $findAction = current($findActions);
        }
        return $findAction;
    }

    /**
     * Создание процесса workflow
     *
     * @param                        $managerName
     * @param                        $workflowName
     * @param                        $actionName
     *
     * @param TransientVarsInterface $transientVars
     *
     * @return TransitionResultInterface
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\RuntimeException
     *
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidInitializeWorkflowEntryException
     */
    public function initialize($managerName, $workflowName, $actionName, TransientVarsInterface $transientVars = null)
    {
        $eventManager = $this->getEventManager();
        $transactionEvent = $this->workflowTransactionEventFactory();

        try {
            $eventManager->trigger($transactionEvent);

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

            if (null === $transientVars) {
                $transientVars = new BaseTransientVars();
            }
            $event->setTransientVars($transientVars);

            $transactionEvent->setState(WorkflowTransactionEventInterface::START_STATE);
            $eventManager->trigger($transactionEvent);

            $entryId = $manager->initialize($workflowName, $actionId, $transientVars);
            $event->setEntryId($entryId);

            $eventManager->trigger(WorkflowEvent::EVENT_WORKFLOW_INITIALIZE, $this, $event);


            $initialActions = $wf->getInitialAction($actionId);
            $viewName = $initialActions->getView();
            if (null !== $viewName) {
                $event->setViewName($viewName);
                $event->setName(WorkflowEvent::EVENT_RENDER);
                $eventManager->trigger($event);
            }

            $result = $this->transitionCompletedResultFactory($entryId, $manager, $wf, $transientVars);

            if ($viewName) {
                $result->setViewName($viewName);
            }

            $transactionEvent->setState(WorkflowTransactionEventInterface::COMMIT_STATE);
            $eventManager->trigger($transactionEvent);
        } catch (\Exception $e) {
            $transactionEvent->setState(WorkflowTransactionEventInterface::ROLLBACK_STATE);
            $eventManager->trigger($transactionEvent);

            if (false === $transactionEvent->getFlagSuppressException()) {
                throw new Exception\InvalidInitializeWorkflowEntryException($e->getMessage(), $e->getCode(), $e);
            }
            $transitionErrorResult = $this->transitionErrorResultFactory($e);
            $transitionErrorResult->setTransientVars($transientVars);

            $result = $this->transitionErrorResultFactory($e);
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
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidManagerNameException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidWorkflowManagerException
     */
    public function getWorkflowManager($managerName)
    {
        if (!$this->hasWorkflowManager($managerName)) {
            $errMsg = sprintf('Invalid workflow manager name %s', $managerName);
            throw new Exception\InvalidManagerNameException($errMsg);
        }
        $workflowManagerServiceName = $this->getWorkflowManagerServiceName($managerName);

        $manager =  $this->getWorkflowContainer()->get($workflowManagerServiceName);

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
     * @return null|ActionDescriptor
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

        return $this->getWorkflowContainer()->has($workflowManagerServiceName);
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

    /**
     * Возвращает имя менеджера по его пседовниму
     *
     * @param $alias
     *
     * @return string
     *
     * @throws Exception\InvalidWorkflowManagerAliasException
     */
    public function getManagerNameByAlias($alias)
    {
        $aliasMap = $this->getManagerAliases();
        if (!array_key_exists($alias, $aliasMap)) {
            $errMsg = sprintf('Invalid workflow manager alias: %s', $alias);
            throw new Exception\InvalidWorkflowManagerAliasException($errMsg);
        }

        return $aliasMap[$alias];
    }


    /**
     * Проверяет есть ли псевдоним для менеджера workflow
     *
     * @param string $alias
     *
     * @return boolean
     */
    public function hasWorkflowManagerAlias($alias)
    {
        $aliasMap = $this->getManagerAliases();
        return array_key_exists($alias, $aliasMap);
    }

    /**
     * Получение менеджера workflow по его псевдониму
     *
     * @param $alias
     *
     * @return WorkflowInterface
     *
     * @throws Exception\InvalidWorkflowManagerAliasException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidManagerNameException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidWorkflowManagerException
     */
    public function getWorkflowManagerByAlias($alias)
    {
        $name = $this->getManagerNameByAlias($alias);
        return $this->getWorkflowManager($name);
    }

    /**
     * Возвращает контейнер для получения экземпляров workflow
     *
     * @return ServiceLocatorInterface
     */
    public function getWorkflowContainer()
    {
        return $this->workflowContainer;
    }

    /**
     * Устанавливает контейнер для получения экземпляров workflow
     *
     * @param ServiceLocatorInterface $workflowContainer
     *
     * @return $this
     */
    public function setWorkflowContainer(ServiceLocatorInterface $workflowContainer)
    {
        $this->workflowContainer = $workflowContainer;

        return $this;
    }

    /**
     * Возвращает псевдонимы для менеджеров workflow
     *
     * @return array
     */
    public function getManagerAliases()
    {
        return $this->managerAliases;
    }

    /**
     * Устанавливает псевдонимы для менеджеров workflow
     *
     * @param array $managerAliases
     *
     * @return $this
     */
    public function setManagerAliases(array $managerAliases)
    {
        $this->managerAliases = $managerAliases;

        return $this;
    }

    /**
     * Возвращает имя класса  - реализующего событие используемое для работы с транзакциями
     *
     * @return string
     */
    public function getWorkflowTransactionEventClassName()
    {
        return $this->workflowTransactionEventClassName;
    }

    /**
     * Устанавливает имя класса  - реализующего событие используемое для работы с транзакциями
     *
     * @param string $workflowTransactionEventClassName
     *
     * @return $this
     */
    public function setWorkflowTransactionEventClassName($workflowTransactionEventClassName)
    {
        $this->workflowTransactionEventClassName = $workflowTransactionEventClassName;

        return $this;
    }

    /**
     * Фабрика для создания события имспользуемого для организации работоы с транзакциями

     * @return WorkflowTransactionEventInterface
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\RuntimeException
     */
    protected function workflowTransactionEventFactory()
    {
        $className = $this->getWorkflowTransactionEventClassName();
        $r = new ReflectionClass($className);
        $event = $r->newInstance();

        if (!$event instanceof WorkflowTransactionEventInterface) {
            $errMsg = sprintf('Event not implement %s', WorkflowTransactionEventInterface::class);
            throw new Exception\RuntimeException($errMsg);
        }

        return $event;
    }

    /**
     * Возвращает имя класса реализующего интерфейс описывающий результаты успешного запуска Workflow
     *
     * @return string
     */
    public function getTransitionCompletedResultClassName()
    {
        return $this->transitionCompletedResultClassName;
    }

    /**
     * Устанавливает имя класса реализующего интерфейс описывающий результаты успешного запуска Workflow
     *
     * @param string $transitionCompletedResultClassName
     *
     * @return $this
     */
    public function setTransitionCompletedResultClassName($transitionCompletedResultClassName)
    {
        $this->transitionCompletedResultClassName = $transitionCompletedResultClassName;

        return $this;
    }


    /**
     * Создает объект с результатами успешного запуска Workflow
     *
     * @param                        $entryId
     * @param WorkflowInterface      $workflowManager
     * @param WorkflowDescriptor     $workflow
     * @param TransientVarsInterface $transientVars
     *
     * @return TransitionCompletedResultInterface
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\RuntimeException
     */
    protected function transitionCompletedResultFactory($entryId, WorkflowInterface $workflowManager, WorkflowDescriptor $workflow, TransientVarsInterface $transientVars)
    {
        $className = $this->getTransitionCompletedResultClassName();
        $r = new ReflectionClass($className);
        $event = $r->newInstance($entryId, $workflowManager, $workflow, $transientVars);

        if (!$event instanceof TransitionCompletedResultInterface) {
            $errMsg = sprintf('Transition result not implement %s', TransitionCompletedResultInterface::class);
            throw new Exception\RuntimeException($errMsg);
        }

        return $event;
    }

    /**
     * Возвращает имя класса реализующего интерфейс описывающий результаты не успешного запуска Workflow
     *
     * @return string
     */
    public function getTransitionErrorResultClassName()
    {
        return $this->transitionErrorResultClassName;
    }

    /**
     * Устанавливает имя класса реализующего интерфейс описывающий результаты не успешного запуска Workflow
     *
     * @param string $transitionErrorResultClassName
     *
     * @return $this
     */
    public function setTransitionErrorResultClassName($transitionErrorResultClassName)
    {
        $this->transitionErrorResultClassName = $transitionErrorResultClassName;

        return $this;
    }


    /**
     * Создает объект с результатами не удавшегося запуска workflow
     *
     * @param \Exception $e
     *
     * @return TransitionErrorResultInterface
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\RuntimeException
     */
    protected function transitionErrorResultFactory(\Exception $e)
    {
        $className = $this->getTransitionErrorResultClassName();
        $r = new ReflectionClass($className);
        $event = $r->newInstance($e);

        if (!$event instanceof TransitionErrorResultInterface) {
            $errMsg = sprintf('Error transition result not implement %s', TransitionErrorResultInterface::class);
            throw new Exception\RuntimeException($errMsg);
        }

        return $event;
    }
}
