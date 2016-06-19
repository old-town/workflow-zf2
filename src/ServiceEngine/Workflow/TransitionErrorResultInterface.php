<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ServiceEngine\Workflow;

/**
 * Interface TransitionErrorResultInterface
 *
 * @package OldTown\Workflow\ZF2\ServiceEngine\Workflow
 */
interface TransitionErrorResultInterface extends TransitionResultInterface
{
    /**
     * Исключение
     *
     * @return \Exception
     */
    public function getException();

    /**
     * Устанавливает исключение
     *
     * @param \Exception $e
     *
     * @return $this
     */
    public function setException(\Exception $e);
}
