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
     * Префикс с которого начинаются имена сервисов являющихся менеджерами wf
     *
     * @var string
     */
    protected $workflowServiceNamePrefix = 'workflow.manager.';

    /**
     * Паттерн для получения имени сервиса workflow
     *
     * @var string
     */
    protected $workflowManagerServiceNamePattern = 'workflow.manager.%s';

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

    /**
     * Префикс с которого начинаются имена сервисов являющихся менеджерами wf
     *
     * @return string
     */
    public function getWorkflowServiceNamePrefix()
    {
        return $this->workflowServiceNamePrefix;
    }

    /**
     * Префикс с которого начинаются имена сервисов являющихся менеджерами wf
     *
     * @param string $workflowServiceNamePrefix
     *
     * @return $this
     */
    public function setWorkflowServiceNamePrefix($workflowServiceNamePrefix)
    {
        $this->workflowServiceNamePrefix = $workflowServiceNamePrefix;

        return $this;
    }

    /**
     * Паттерн для получения имени сервиса workflow
     *
     * @return string
     */
    public function getWorkflowManagerServiceNamePattern()
    {
        return $this->workflowManagerServiceNamePattern;
    }

    /**
     * Паттерн для получения имени сервиса workflow
     *
     * @param string $workflowManagerServiceNamePattern
     *
     * @return $this
     */
    public function setWorkflowManagerServiceNamePattern($workflowManagerServiceNamePattern)
    {
        $this->workflowManagerServiceNamePattern = $workflowManagerServiceNamePattern;

        return $this;
    }
}
