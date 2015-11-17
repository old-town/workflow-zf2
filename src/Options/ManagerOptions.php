<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Options;

use Zend\Stdlib\AbstractOptions;
use OldTown\Workflow\Basic\BasicWorkflow;


/**
 * Class ManagerOptions
 *
 * @package OldTown\Workflow\ZF2\Options
 */
class ManagerOptions extends AbstractOptions
{
    /**
     * Имя конфигурации для данного менеджера workflow
     *
     * @var string
     */
    protected $configuration;

    /**
     * Имя класса менеджера workflow
     *
     * @var string
     */
    protected $name = BasicWorkflow::class;

    /**
     * Зарегестрированные workflows
     *
     * @var array
     */
    protected $workflows = [];

    /**
     * Возвращает имя конфигурации для данного менеджера workflow
     *
     * @return string
     *
     * @throws Exception\InvalidConfigurationNameException
     */
    public function getConfiguration()
    {
        if (null === $this->configuration) {
            $errMsg = 'no attribute \'configuration\'';
            throw new Exception\InvalidConfigurationNameException($errMsg);
        }
        return $this->configuration;
    }

    /**
     * Устанавливает имя конфигурации для данного менеджера workflow
     *
     * @param string $configuration
     *
     * @return $this
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Возвращает имя класса менеджера workflow
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Устанавливает имя класса менеджера workflow
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает зарегестрированные workflows
     *
     * @return array
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * Устанавливает зарегестрированные workflows
     *
     * @param array $workflows
     *
     * @return $this
     */
    public function setWorkflows(array $workflows)
    {
        $this->workflows = $workflows;

        return $this;
    }
}
