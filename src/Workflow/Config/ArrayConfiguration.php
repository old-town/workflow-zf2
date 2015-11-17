<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Workflow\Config;

use OldTown\Workflow\Config\ConfigurationInterface;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use Psr\Http\Message\UriInterface;
use OldTown\Workflow\Loader\WorkflowFactoryInterface;


/**
 * Interface ConfigurationInterface
 *
 * @package OldTown\Workflow\Config
 */
class  ArrayConfiguration implements ConfigurationInterface
{
    /**
     * Флаг определяющий было ли иницилизированно workflow
     *
     * @var bool
     */
    protected $initialized = false;

    /**
     * Фабрика для создания workflow
     *
     * @var WorkflowFactoryInterface
     */
    protected $factory;

    /**
     * @param $options
     */
    public function __construct($options)
    {
    }

    /**
     * Определяет была ли иницилазированна дананя конфигурация
     *
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * Определяет есть ли возможность модифицировать workflow  с з
     *
     * @param string $name
     *
     * @return bool
     */
    public function isModifiable($name)
    {
        return $this->getFactory()->isModifiable($name);
    }

    public function getPersistence()
    {
        // TODO: Implement getPersistence() method.
    }

    public function getPersistenceArgs()
    {
        // TODO: Implement getPersistenceArgs() method.
    }

    public function getVariableResolver()
    {
        // TODO: Implement getVariableResolver() method.
    }

    public function getWorkflow($name)
    {
        // TODO: Implement getWorkflow() method.
    }

    public function getWorkflowNames()
    {
        // TODO: Implement getWorkflowNames() method.
    }

    public function getWorkflowStore()
    {
        // TODO: Implement getWorkflowStore() method.
    }

    public function load(UriInterface $url = null)
    {
        // TODO: Implement load() method.
    }

    public function removeWorkflow($workflow)
    {
        // TODO: Implement removeWorkflow() method.
    }

    public function saveWorkflow($name, WorkflowDescriptor $descriptor, $replace = false)
    {
        // TODO: Implement saveWorkflow() method.
    }

    /**
     * @return WorkflowFactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param WorkflowFactoryInterface $factory
     *
     * @return $this
     */
    public function setFactory(WorkflowFactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }
}
