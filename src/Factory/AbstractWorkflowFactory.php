<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Factory;

use OldTown\Workflow\Util\Properties\Properties;
use OldTown\Workflow\Util\VariableResolverInterface;
use OldTown\Workflow\ZF2\Options\ConfigurationOptions;
use OldTown\Workflow\Config\ArrayConfiguration;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\MutableCreationOptionsTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use OldTown\Workflow\ZF2\Options\ModuleOptions;
use OldTown\Workflow\WorkflowInterface;
use OldTown\Workflow\Config\ConfigurationInterface;
use OldTown\Workflow\Loader\WorkflowFactoryInterface;



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
     * Имя опции через которое можно указать класс для создания конфиа для workflow
     *
     * @var string
     */
    const WORKFLOW_CONFIGURATION_NAME = 'workflowConfigurationName';

    /**
     * Имя класса конфигурации
     *
     * @var string
     */
    protected $workflowConfigurationName = ArrayConfiguration::class;

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
     *
     * @throws \OldTown\Workflow\ZF2\Factory\Exception\FactoryException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $managerName = substr($requestedName, strlen(static::SERVICE_NAME_PREFIX));

        try {
            $creationOptions = $this->getCreationOptions();
            if (array_key_exists(static::WORKFLOW_CONFIGURATION_NAME, $creationOptions)) {
                $this->setWorkflowConfigurationName($creationOptions[static::WORKFLOW_CONFIGURATION_NAME]);
            }

            /** @var ModuleOptions $moduleOptions */
            $moduleOptions = $serviceLocator->get(ModuleOptions::class);

            $managerOptions = $moduleOptions->getManagerOptions($managerName);
            $configName = $managerOptions->getConfiguration();

            $workflowManagerName = $managerOptions->getName();

            $workflowManager = null;
            if ($serviceLocator->has($workflowManagerName)) {
                $workflowManager = $serviceLocator->get($workflowManagerName);
            } elseif (class_exists($workflowManagerName)) {
                $r = new \ReflectionClass($workflowManagerName);
                $workflowManager = $r->newInstance($r);
            }

            if (!$workflowManager instanceof WorkflowInterface) {
                $errMsg = sprintf('Workflow not implements %s', WorkflowInterface::class);
                throw new Exception\InvalidWorkflowException($errMsg);
            }

            $configurationOptions = $moduleOptions->getConfigurationOptions($configName);
            $workflowConfig = $this->buildWorkflowManagerConfig($configurationOptions, $serviceLocator);

            $workflowManager->setConfiguration($workflowConfig);
        } catch (\Exception $e) {
            throw new Exception\FactoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $workflowManager;
    }

    /**
     * Создает конфиг для workflow
     *
     * @param ConfigurationOptions    $config
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConfigurationInterface
     *
     * @throws \OldTown\Workflow\ZF2\Factory\Exception\InvalidVariableResolverException
     * @throws \OldTown\Workflow\ZF2\Options\Exception\InvalidServiceConfigException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\Options\Exception\InvalidPersistenceConfigException
     * @throws \OldTown\Workflow\ZF2\Options\Exception\InvalidFactoryConfigException
     * @throws \OldTown\Workflow\ZF2\Factory\Exception\InvalidWorkflowFactoryException
     * @throws \OldTown\Workflow\ZF2\Factory\Exception\RuntimeException
     */
    public function buildWorkflowManagerConfig(ConfigurationOptions $config, ServiceLocatorInterface $serviceLocator)
    {
        $resolverServiceName = $config->getResolver();
        $resolver = null;
        if ($resolverServiceName) {
            if ($serviceLocator->has($resolverServiceName)) {
                $resolver = $serviceLocator->get($resolverServiceName);
            } elseif (class_exists($resolverServiceName)) {
                $r = new \ReflectionClass($resolverServiceName);
                $resolver = $r->newInstance();
            }

            if (!$resolver instanceof VariableResolverInterface) {
                $errMsg = sprintf('Resolver not implements %s', VariableResolverInterface::class);
                throw new Exception\InvalidVariableResolverException($errMsg);
            }
        }

        $factory = null;
        if ($config->hasFactoryOptions()) {
            $factoryServiceName = $config->getFactoryOptions()->getName();
            if ($serviceLocator->has($factoryServiceName)) {
                $factory = $serviceLocator->get($factoryServiceName);
            } elseif (class_exists($resolverServiceName)) {
                $r = new \ReflectionClass($factoryServiceName);
                $factory = $r->newInstance();
            }

            if (!$factory instanceof WorkflowFactoryInterface) {
                $errMsg = sprintf('Factory not implements %s', WorkflowFactoryInterface::class);
                throw new Exception\InvalidWorkflowFactoryException($errMsg);
            }
            $factoryOptions = $config->getFactoryOptions()->getOptions();
            $properties = new Properties();
            foreach ($factoryOptions as $key => $value) {
                $properties->setProperty($key, $value);
            }
            $factory->init($properties);
        }

        $options = [
            ArrayConfiguration::PERSISTENCE => $config->getPersistenceOptions()->getName(),
            ArrayConfiguration::PERSISTENCE_ARGS => $config->getPersistenceOptions()->getOptions(),
            ArrayConfiguration::VARIABLE_RESOLVER => $resolver,
            ArrayConfiguration::WORKFLOW_FACTORY => $factory

        ];

        $configServiceName = $this->getWorkflowConfigurationName();
        if (!class_exists($configServiceName)) {
            $errMsg = sprintf('Class %s not found', $configServiceName);
            throw new Exception\RuntimeException($errMsg);
        }
        $r = new \ReflectionClass($configServiceName);
        $workflowManagerConfig = $r->newInstance($options);

        if (!$workflowManagerConfig instanceof ConfigurationInterface) {
            $errMsg = sprintf('Class not implement %s', ConfigurationInterface::class);
            throw new Exception\RuntimeException($errMsg);
        }

        return $workflowManagerConfig;
    }

    /**
     * @return string
     */
    public function getWorkflowConfigurationName()
    {
        return $this->workflowConfigurationName;
    }

    /**
     * @param string $workflowConfigurationName
     *
     * @return $this
     */
    public function setWorkflowConfigurationName($workflowConfigurationName)
    {
        $this->workflowConfigurationName = (string)$workflowConfigurationName;

        return $this;
    }
}
