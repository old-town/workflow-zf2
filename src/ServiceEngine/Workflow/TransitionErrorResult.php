<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ServiceEngine\Workflow;

use OldTown\Workflow\TransientVars\TransientVarsInterface;

/**
 * Class TransitionErrorResult
 *
 * @package OldTown\Workflow\ZF2\ServiceEngine\Workflow
 */
class TransitionErrorResult implements TransitionErrorResultInterface
{
    /**
     * Исключение
     *
     * @var \Exception
     */
    protected $exception;

    /**
     * Переменные контекста исполнения workflow
     *
     * @var TransientVarsInterface
     */
    protected $transientVars;

    /**
     * TransitionErrorResult constructor.
     *
     * @param \Exception $e
     */
    public function __construct(\Exception $e)
    {
        $this->setException($e);
    }

    /**
     * Исключение
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Устанавливает исключение
     *
     * @param \Exception $exception
     *
     * @return $this
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * @return TransientVarsInterface
     */
    public function getTransientVars()
    {
        return $this->transientVars;
    }

    /**
     * @param TransientVarsInterface $transientVars
     *
     * @return $this
     */
    public function setTransientVars(TransientVarsInterface $transientVars)
    {
        $this->transientVars = $transientVars;

        return $this;
    }
}
