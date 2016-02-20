<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * @package OldTown\Workflow\ZF2\Options
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * Менеджеры workflow
     *
     * @var  array
     */
    protected $managers;

    /**
     * Конфигурации для менеджеров
     *
     * @var array
     */
    protected $configurations;

    /**
     * Псевдонимы для имен менеджеров wf
     *
     * @var array
     */
    protected $managerAliases = [];

    /**
     * @return array
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * @param array $managers
     *
     * @return $this
     */
    public function setManagers(array $managers)
    {
        $this->managers = $managers;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfigurations()
    {
        return $this->configurations;
    }

    /**
     * @param array $configurations
     *
     * @return $this
     */
    public function setConfigurations(array $configurations)
    {
        $this->configurations = $configurations;

        return $this;
    }

    /**
     * Псевдонимы для имен менеджеров wf
     *
     * @return array
     */
    public function getManagerAliases()
    {
        return $this->managerAliases;
    }

    /**
     * Устанавливает псевдонимы для имен менеджеров wf
     *
     * @param array $managerAliases
     *
     * @return $this
     */
    public function setManagerAliases(array $managerAliases = [])
    {
        $this->managerAliases = $managerAliases;

        return $this;
    }


    /**
     * Возвращает настройки менеджера по его имени
     *
     * @param string $managerName
     *
     * @return ManagerOptions
     *
     * @throws Exception\InvalidManagerNameException
     */
    public function getManagerOptions($managerName)
    {
        if (!array_key_exists($managerName, $this->managers)) {
            $errMsg = sprintf('Invalid manager name %s', $managerName);
            throw new Exception\InvalidManagerNameException($errMsg);
        }

        return new ManagerOptions($this->managers[$managerName]);
    }


    /**
     * Возвращает настройки с заданным именем
     *
     * @param string $configurationName
     *
     * @return ConfigurationOptions
     *
     * @throws Exception\InvalidConfigurationNameException
     */
    public function getConfigurationOptions($configurationName)
    {
        if (!array_key_exists($configurationName, $this->configurations)) {
            $errMsg = sprintf('Invalid configuration name %s', $configurationName);
            throw new Exception\InvalidConfigurationNameException($errMsg);
        }

        return new ConfigurationOptions($this->configurations[$configurationName]);
    }
}
