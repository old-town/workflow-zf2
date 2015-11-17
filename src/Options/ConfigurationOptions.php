<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Options;

use Zend\Stdlib\AbstractOptions;


/**
 * Class ManagerOptions
 *
 * @package OldTown\Workflow\ZF2\Options
 */
class ConfigurationOptions extends AbstractOptions
{
    /**
     * Конфиг хранилища запущенного состояния workflow
     *
     * @var array
     */
    protected $persistence;

    /**
     * Конфиг фабрики по созданию workflow
     *
     * @var array
     */
    protected $factory;

    /**
     * Конфиг сервиса работы с переменными в workflow
     *
     * @var array
     */
    protected $resolver;

    /**
     * Настройки хранилища запущенного состояния workflow
     *
     * @var ConfigurationServiceOptions
     */
    protected $persistenceOptions;

    /**
     * Настройки фабрики по созданию workflow
     *
     * @var ConfigurationServiceOptions
     */
    protected $factoryOptions;

    /**
     * Настройки сервиса работы с переменными в workflow
     *
     * @var ConfigurationServiceOptions
     */
    protected $resolverOptions;

    /**
     * @return array
     */
    public function getPersistence()
    {
        return $this->persistence;
    }

    /**
     * @param array $persistence
     *
     * @return $this
     */
    public function setPersistence(array $persistence)
    {
        $this->persistence = $persistence;
        $this->persistenceOptions = null;

        return $this;
    }

    /**
     * @return array
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param array $factory
     *
     * @return $this
     */
    public function setFactory(array $factory)
    {
        $this->factory = $factory;
        $this->factoryOptions = null;

        return $this;
    }

    /**
     * @return array
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * @param array $resolver
     *
     * @return $this
     */
    public function setResolver(array $resolver)
    {
        $this->resolver = $resolver;
        $this->resolverOptions = null;

        return $this;
    }

    /**
     * @return ConfigurationServiceOptions
     */
    public function getPersistenceOptions()
    {
        if ($this->persistenceOptions) {
            return $this->persistenceOptions;
        }
        $this->persistenceOptions = new ConfigurationServiceOptions($this->persistence);
        return $this->persistenceOptions;
    }

    /**
     * @return ConfigurationServiceOptions
     */
    public function getFactoryOptions()
    {
        if ($this->factoryOptions) {
            return $this->factoryOptions;
        }
        $this->factoryOptions = new ConfigurationServiceOptions($this->factory);
        return $this->factoryOptions;
    }

    /**
     * @return ConfigurationServiceOptions
     */
    public function getResolverOptions()
    {
        if ($this->resolverOptions) {
            return $this->resolverOptions;
        }
        $this->resolverOptions = new ConfigurationServiceOptions($this->resolver);
        return $this->resolverOptions;
    }
}
