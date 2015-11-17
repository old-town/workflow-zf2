<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Manager;

use OldTown\Workflow\WorkflowInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class WorkflowPluginManager
 *
 * @package OldTown\Workflow\ZF2\Workflow\Manager
 */
class WorkflowPluginManager extends AbstractPluginManager
{
    /**
     * @param mixed $plugin
     *
     * @throws Exception\InvalidWorkflowException
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof WorkflowInterface) {
            $errMsg = sprintf('Workflow not implements %s', WorkflowInterface::class);
            throw new Exception\InvalidWorkflowException($errMsg);
        }
    }
}
