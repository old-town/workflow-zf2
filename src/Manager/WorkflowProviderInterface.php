<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Manager;

/**
 * Interface WorkflowProviderInterface
 *
 * @package OldTown\Workflow\ZF2\Manager
 */
interface WorkflowProviderInterface
{
    /**
     * @return array
     */
    public function getWorkflowManagerConfig();
}
