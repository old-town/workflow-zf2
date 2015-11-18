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
     * @param string $resolver
     *
     * @return $this
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * @return ConfigurationServiceOptions
     *
     * @throws Exception\InvalidPersistenceConfigException
     */
    public function getPersistenceOptions()
    {
        if ($this->persistenceOptions) {
            return $this->persistenceOptions;
        }
        if (!$this->hasPersistenceOptions()) {
            $errMsg = 'Persistence options not specified';
            throw new Exception\InvalidPersistenceConfigException($errMsg);
        }
        $this->persistenceOptions = new ConfigurationServiceOptions($this->persistence);
        return $this->persistenceOptions;
    }

    /**
     * Определяет есть ли настройки для хранилища
     *
     * @return bool
     */
    public function hasPersistenceOptions()
    {
        return is_array($this->persistence);
    }

    /**
     * @return ConfigurationServiceOptions
     *
     * @throws Exception\InvalidFactoryConfigException
     */
    public function getFactoryOptions()
    {
        if ($this->factoryOptions) {
            return $this->factoryOptions;
        }
        if (!$this->hasFactoryOptions()) {
            $errMsg = 'Factory options not specified';
            throw new Exception\InvalidFactoryConfigException($errMsg);
        }
        $this->factoryOptions = new ConfigurationServiceOptions($this->factory);
        return $this->factoryOptions;
    }

    /**
     * Определяет есть ли настройки для фабрики
     *
     * @return bool
     */
    public function hasFactoryOptions()
    {
        return is_array($this->factory);
    }
}
