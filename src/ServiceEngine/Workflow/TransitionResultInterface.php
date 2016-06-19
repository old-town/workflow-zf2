<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ServiceEngine\Workflow;

use OldTown\Workflow\TransientVars\TransientVarsInterface;


/**
 * Interface TransitionResultInterface
 *
 * @package OldTown\Workflow\ZF2\ServiceEngine\Workflow
 */
interface TransitionResultInterface
{
    /**
     * @return TransientVarsInterface
     */
    public function getTransientVars();

    /**
     * @param TransientVarsInterface $transientVars
     *
     * @return $this
     */
    public function setTransientVars(TransientVarsInterface $transientVars);
}
