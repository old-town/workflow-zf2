<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Service;

use OldTown\Workflow\WorkflowInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Traversable;
use Zend\Stdlib\ArrayUtils;


/**
 * Class Workflow
 *
 * @package OldTown\Workflow\ZF2\Service
 *
 */
class Workflow
{
    use ServiceLocatorAwareTrait;

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
     * Создание процесса workflow
     *
     * @param $managerName
     * @param $workflowName
     * @param $actionName
     *
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidInitializeWorkflowEntryException
     */
    public function initialize($managerName, $workflowName, $actionName)
    {
        try {
            $manager = $this->getWorkflowManager($managerName);

        } catch (\Exception $e) {
            throw new Exception\InvalidInitializeWorkflowEntryException($e->getMessage(), $e->getCode(), $e);
        }



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
