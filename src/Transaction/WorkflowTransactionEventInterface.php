<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Transaction;

use Zend\EventManager\EventInterface;

/**
 * Interface WorkflowTransactionEventInterface
 *
 * @package OldTown\Workflow\ZF2\Transaction
 */
interface WorkflowTransactionEventInterface extends EventInterface
{
    /**
     * Подготовка к запуску процесса workflow
     *
     * @var string
     */
    const PREPARE_STATE = 'prepare';

    /**
     * Процесс workflow запущен
     *
     * @var string
     */
    const START_STATE = 'start';

    /**
     * Workflow успешно отработало
     *
     * @var string
     */
    const COMMIT_STATE = 'commit';

    /**
     * Необходимо сделать откат результатов работы wf
     *
     * @var string
     */
    const ROLLBACK_STATE = 'rollback';

    /**
     * Возвращает имя Workflow
     *
     * @return string
     */
    public function getWorkflowName();

    /**
     * Состояние процесса запуска Workflow
     *
     * @return string
     */
    public function getState();

    /**
     * Устанавливает состояние процесса запуска Workflow
     *
     * @param string $state
     *
     * @return $this
     */
    public function setState($state);

    /**
     * Возвращает уникальный идендификатор процесса запуска workflow
     *
     * @return string
     */
    public function getUuid();

    /**
     * Возвращает исключение  возникшее в результате выполнения wf
     *
     * @return \Exception
     */
    public function getException();

    /**
     * Устанавливает исключение возникшее в результате выполнения
     *
     * @param \Exception $exception
     *
     * @return $this
     */
    public function setException(\Exception $exception);

    /**
     * Возвращает флаг определяющий нужно ли подавить исключение
     *
     * @return bool|null
     */
    public function getFlagSuppressException();

    /**
     * Устанавливает флаг определяющий нужно ли подавить исключение
     *
     * @param bool|null $flagSuppressException
     *
     * @return $this
     */
    public function setFlagSuppressException($flagSuppressException);
}
