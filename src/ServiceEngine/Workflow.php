<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ServiceEngine;

use OldTown\Workflow\AbstractWorkflow;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\WorkflowInterface;
use OldTown\Workflow\ZF2\Event\DoActionTransactionEvent;
use OldTown\Workflow\ZF2\Event\InitializeTransactionEvent;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Traversable;
use Zend\Stdlib\ArrayUtils;
use OldTown\Workflow\TransientVars\BaseTransientVars;
use OldTown\Workflow\ZF2\Event\WorkflowEvent;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow\TransitionResult;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow\TransitionResultInterface;
use OldTown\Workflow\ZF2\Options\ModuleOptions;
use OldTown\Workflow\Loader\ActionDescriptor;
use OldTown\Workflow\ZF2\Transaction\WorkflowTransactionInterface;

/**
 * Class Workflow
 *
 * @package OldTown\Workflow\ZF2\ServiceEngine
 */
class Workflow implements WorkflowServiceInterface
{
    use ServiceLocatorAwareTrait, EventManagerAwareTrait;

    /**
     * Паттерн для получения имени сервиса workflow
     *
     * @var string
     */
    protected $workflowManagerServiceNamePattern = 'workflow.manager.%s';

    /**
     * Конфигурация модуля
     *
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * Контекст событий
     *
     * @var array
     */
    protected $eventIdentifier = [
        WorkflowTransactionInterface::class
    ];

    /**
     * @param $options
     *
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidArgumentException
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
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidArgumentException
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

        if (!array_key_exists('moduleOptions', $options)) {
            $errMsg = 'Argument moduleOptions not found';
            throw new Exception\InvalidArgumentException($errMsg);
        }
        $this->setModuleOptions($options['moduleOptions']);
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
     *
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\DoActionException
     */
    public function doAction($managerName, $entryId, $actionName, TransientVarsInterface $transientVars = null)
    {
        $transactionEvent = new DoActionTransactionEvent($managerName, $entryId, $actionName);
        $transactionEvent->setTarget($this);

        try {
            $transactionEvent->setName(DoActionTransactionEvent::START_TRANSACTION);
            $this->getEventManager()->trigger($transactionEvent);


            $event = new WorkflowEvent();
            $event->setTarget($this);
            $event->setEntryId($entryId);

            $manager = $this->getWorkflowManager($managerName);
            $event->setWorkflowManager($manager);

            $workflowStore = $manager->getConfiguration()->getWorkflowStore();

            $entry = $workflowStore->findEntry($entryId);
            if (null === $entry) {
                $errMsg = sprintf('Entry id %s not found', $entryId);
                throw new Exception\InvalidArgumentException($errMsg);
            }


            $workflowName = $entry->getWorkflowName();

            $wf = $manager->getConfiguration()->getWorkflow($workflowName);
            $event->setWorkflow($wf);

            //$action = $this->getActionByName($wf, $actionName);
            $action = $this->findActionByNameForEntry($managerName, $entryId, $actionName, $transientVars);

            if (null === $action) {
                try {
                    $errMsg = $this->generateTxtActionNotFoundException($manager, $entry, $actionName);
                } catch (\Exception $e) {
                    $errMsg = sprintf('Invalid action %s', $actionName);
                }
                throw new Exception\RuntimeException($errMsg);
            }


            $actionId = $action->getId();

            if (null === $transientVars) {
                $transientVars = new BaseTransientVars();
            }

            $event->setTransientVars($transientVars);

            $manager->doAction($entryId, $actionId, $transientVars);

            $this->getEventManager()->trigger(WorkflowEvent::EVENT_DO_ACTION, $this, $event);

            $viewName = $action->getView();
            if (null !== $viewName) {
                $event->setViewName($viewName);
                $event->setName(WorkflowEvent::EVENT_RENDER);
                $this->getEventManager()->trigger($event);
            }


            $result = new TransitionResult($entryId, $manager, $wf, $transientVars);
            if ($viewName) {
                $result->setViewName($viewName);
            }

            $transactionEvent->setName(DoActionTransactionEvent::COMMIT_TRANSACTION);
            $this->getEventManager()->trigger($transactionEvent);
        } catch (\Exception $e) {
            $transactionEvent->setName(DoActionTransactionEvent::ROLLBACK_TRANSACTION);
            $this->getEventManager()->trigger($transactionEvent);

            throw new Exception\DoActionException($e->getMessage(), $e->getCode(), $e);
        }


        return $result;
    }

    /**
     * Генерация сообщения о ошибке, для ситуации когда не найдено действие
     *
     * @param WorkflowInterface      $wfManager
     * @param WorkflowEntryInterface $entry
     * @param                        $actionName
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\RuntimeException
     *
     * @return string
     */
    protected function generateTxtActionNotFoundException(WorkflowInterface $wfManager, WorkflowEntryInterface $entry, $actionName)
    {
        $workflowStore = $wfManager->getConfiguration()->getWorkflowStore();
        $wf = $wfManager->getConfiguration()->getWorkflow($entry->getWorkflowName());

        $currentSteps = $workflowStore->findCurrentSteps($entry->getId());

        $stepInfo = [];
        foreach ($currentSteps as $currentStep) {
            $stepId = $currentStep->getStepId();
            $step = $wf->getStep($stepId);

            if (null === $step) {
                $errMsg = sprintf('Step with id %s not found', $stepId);
                throw new  Exception\RuntimeException($errMsg);
            }

            $stepName = $step->getName();
            $stepInfo[] = sprintf('%s[status="%s"](stepId="%s")', $stepName, $currentStep->getStatus(), $stepId);
        }

        $errMsg = sprintf(
            'Current steps %s of workflow process(name="%s",entryId="%s") is not "%s" action',
            implode(',', $stepInfo),
            $entry->getWorkflowName(),
            $entry->getId(),
            $actionName
        );

        return $errMsg;
    }

    /**
     * Возвращает доступные действия для текущего состояния процесса
     *
     * @param                        $managerName
     * @param                        $entryId
     *
     * @param TransientVarsInterface $transientVars
     *
     * @return array|\OldTown\Workflow\Loader\ActionDescriptor[]
     * @throws \OldTown\Workflow\Exception\WorkflowException
     * @throws \OldTown\Workflow\Exception\StoreException
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\FactoryException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidWorkflowManagerException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidManagerNameException
     */
    public function getAvailableActions($managerName, $entryId, TransientVarsInterface $transientVars = null)
    {
        /** @var AbstractWorkflow $manager */
        $manager = $this->getWorkflowManager($managerName);

        $actionIds = $manager->getAvailableActions($entryId, $transientVars);

        $entry = $manager->getConfiguration()->getWorkflowStore()->findEntry($entryId);
        $workflowName = $entry->getWorkflowName();

        $wf = $manager->getConfiguration()->getWorkflow($workflowName);

        $findActions = [];
        foreach ($actionIds as $actionId) {
            $findActions[] = $wf->getAction($actionId);
        }

        return $findActions;
    }


    /**
     * Ишет действие по имени. Поиск происходит в рамках текущего step'a.
     *
     * @param                        $managerName
     * @param                        $entryId
     * @param                        $actionName
     *
     * @param TransientVarsInterface $transientVars
     *
     * @return null|ActionDescriptor
     * @throws \OldTown\Workflow\Exception\WorkflowException
     * @throws \OldTown\Workflow\Exception\StoreException
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\FactoryException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidWorkflowActionNameException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidWorkflowManagerException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidManagerNameException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function findActionByNameForEntry($managerName, $entryId, $actionName, TransientVarsInterface $transientVars = null)
    {
        $actions = $this->getAvailableActions($managerName, $entryId, $transientVars);
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
     *
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidInitializeWorkflowEntryException
     */
    public function initialize($managerName, $workflowName, $actionName, TransientVarsInterface $transientVars = null)
    {
        $transactionEvent = new InitializeTransactionEvent($managerName, $workflowName, $actionName);
        $transactionEvent->setTarget($this);
        try {
            $transactionEvent->setName(InitializeTransactionEvent::START_TRANSACTION);
            $this->getEventManager()->trigger($transactionEvent);

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
            $entryId = $manager->initialize($workflowName, $actionId, $transientVars);
            $event->setEntryId($entryId);

            $this->getEventManager()->trigger(WorkflowEvent::EVENT_WORKFLOW_INITIALIZE, $this, $event);


            $initialActions = $wf->getInitialAction($actionId);
            $viewName = $initialActions->getView();
            if (null !== $viewName) {
                $event->setViewName($viewName);
                $event->setName(WorkflowEvent::EVENT_RENDER);
                $this->getEventManager()->trigger($event);
            }

            $result = new TransitionResult($entryId, $manager, $wf, $transientVars);
            if ($viewName) {
                $result->setViewName($viewName);
            }

            $transactionEvent->setName(InitializeTransactionEvent::COMMIT_TRANSACTION);
            $this->getEventManager()->trigger($transactionEvent);
        } catch (\Exception $e) {
            $transactionEvent->setName(InitializeTransactionEvent::ROLLBACK_TRANSACTION);
            $this->getEventManager()->trigger($transactionEvent);

            throw new Exception\InvalidInitializeWorkflowEntryException($e->getMessage(), $e->getCode(), $e);
        }



        return $result;
    }

    /**
     * Конфигурация модуля
     *
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->moduleOptions;
    }

    /**
     * Устанавливает конфигурацию модуля
     *
     * @param ModuleOptions $moduleOptions
     *
     *
     * @return $this
     */
    public function setModuleOptions(ModuleOptions $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;

        return $this;
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

        $manager = $this->getServiceLocator()->get($workflowManagerServiceName);

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
        $aliasMap = $this->getModuleOptions()->getManagerAliases();
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
        $aliasMap = $this->getModuleOptions()->getManagerAliases();

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
}
