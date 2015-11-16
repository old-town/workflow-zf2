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
     * Зарегестрированные workflow
     *
     * @var  array
     */
    protected $workflows;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * Имя сервиса/класса являющегося хранилищем состояния workflow
     *
     * @var string
     */
    protected $persistenceName;


    /**
     * @return array
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * @param array $workflows
     *
     * @return $this
     */
    public function setWorkflows(array $workflows)
    {
        $this->workflows = $workflows;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     *
     * @return $this
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

}
