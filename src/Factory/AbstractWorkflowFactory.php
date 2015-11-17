<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\MutableCreationOptionsTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use OldTown\Workflow\ZF2\Options\ModuleOptions;
use OldTown\Workflow\WorkflowInterface;
use OldTown\Workflow\ZF2\Manager\WorkflowPluginManager;

/**
 * Class PluginMessageAbstractFactory
 *
 * @package OldTown\EventBus\Message
 */
class AbstractWorkflowFactory implements AbstractFactoryInterface, MutableCreationOptionsInterface
{
    use MutableCreationOptionsTrait;

    /**
     * Префикс с которого должно начинаться имя сервиса
     *
     * @var string
     */
    const SERVICE_NAME_PREFIX = 'workflow.manager.';

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $flag = 0 === strpos($requestedName, static::SERVICE_NAME_PREFIX) && strlen($requestedName) > strlen(static::SERVICE_NAME_PREFIX);
        return $flag;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return WorkflowInterface
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\Options\Exception\InvalidManagerNameException
     * @throws \OldTown\Workflow\ZF2\Options\Exception\InvalidConfigurationNameException
     *
     * @throws Exception\InvalidWorkflowException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $managerName = substr($requestedName, strlen(static::SERVICE_NAME_PREFIX));

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get(ModuleOptions::class);

        $managerOptions = $moduleOptions->getManagerOptions($managerName);
        $configName = $managerOptions->getConfiguration();
        $workflowConfig = $moduleOptions->getConfigurationOptions($configName);

        $workflowClassName = $managerOptions->getName();
        /** @var ServiceLocatorInterface $workflowPluginManager */
        $workflowPluginManager = $serviceLocator->get(WorkflowPluginManager::class);

        /** @var WorkflowInterface $workflow */
        $workflow = $workflowPluginManager->get($workflowClassName);





        return $managerOptions;
    }
}
