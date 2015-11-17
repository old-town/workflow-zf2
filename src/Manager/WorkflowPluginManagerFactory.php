<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Manager;

use Zend\Mvc\Service\AbstractPluginManagerFactory;

/**
 * Class WorkflowPluginManagerFactory
 *
 * @package OldTown\Workflow\ZF2\Manager
 */
class WorkflowPluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = WorkflowPluginManager::class;
}
