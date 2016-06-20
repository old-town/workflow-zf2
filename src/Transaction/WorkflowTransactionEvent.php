<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Transaction;

use Ramsey\Uuid\Uuid;
use Zend\EventManager\Event;


/**
 * Class WorkflowTransactionEvent
 *
 * @package OldTown\Workflow\ZF2\Transaction
 */
class WorkflowTransactionEvent extends Event implements WorkflowTransactionEventInterface
{
    /**
     * Разрешенные состояния wf
     *
     * @var array
     */
    protected $accessStates = [
        self::PREPARE_STATE => self::PREPARE_STATE,
        self::START_STATE => self::START_STATE,
        self::COMMIT_STATE => self::COMMIT_STATE,
        self::ROLLBACK_STATE => self::ROLLBACK_STATE,
    ];

    /**
     * Имя Workflow
     *
     * @var string
     */
    protected $workflowName;

    /**
     * Состояние процесса запуска Workflow
     *
     * @var string
     */
    protected $state = self::PREPARE_STATE;

    /**
     * Уникальный идендификатор процесса запуска workflow
     *
     * @var string
     */
    protected $uuid;

    /**
     * Ошибка возникшая в результате выполнения
     *
     * @var \Exception|null
     */
    protected $exception;

    /**
     * Флаг определяет нужно ли подавить исключение
     *
     * @var null|boolean
     */
    protected $flagSuppressException = false;


    /**
     * Возвращает имя Workflow
     *
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Устанавливает имя workflow
     *
     * @param string $workflowName
     *
     * @return $this
     * @throws \OldTown\Workflow\ZF2\Transaction\Exception\RuntimeException
     */
    public function setWorkflowName($workflowName)
    {
        if (self::PREPARE_STATE !== $this->getState()) {
            $errMsg = sprintf('Workflow name can not be changed in the state %s', $this->getState());
            throw new Exception\RuntimeException($errMsg);
        }

        $this->workflowName = $workflowName;

        return $this;
    }

    /**
     * Состояние процесса запуска Workflow
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Устанавливает состояние процесса запуска Workflow
     *
     * @param string $state
     *
     * @return $this
     * @throws \OldTown\Workflow\ZF2\Transaction\Exception\RuntimeException
     * @throws \OldTown\Workflow\ZF2\Transaction\Exception\InvalidArgumentException
     */
    public function setState($state)
    {
        if (!array_key_exists($state, $this->accessStates)) {
            $errMsg = sprintf('Invalid state name %s', $state);
            throw new Exception\InvalidArgumentException($errMsg);
        }
        if (null === $this->workflowName && self::PREPARE_STATE !== $this->getState()) {
            $errMsg = sprintf('You can not set the state %s, without specifying the name of the workflow', $this->getState());
            throw new Exception\RuntimeException($errMsg);
        }


        $this->state = $state;
        $this->setName($state);

        return $this;
    }

    /**
     * Возвращает уникальный идендификатор процесса запуска workflow
     *
     * @return string
     */
    public function getUuid()
    {
        if (null === $this->uuid) {
            $this->uuid = Uuid::uuid4()->toString();
        }
        return $this->uuid;
    }

    /**
     * Возвращает исключение  возникшее в результате выполнения wf
     *
     * @return \Exception
     * @throws \OldTown\Workflow\ZF2\Transaction\Exception\RuntimeException
     */
    public function getException()
    {
        if (self::ROLLBACK_STATE !== $this->getState()) {
            $errMsg = sprintf('An exception can not be obtained for the state in %s', $this->getState());
            throw new Exception\RuntimeException($errMsg);
        }
        return $this->exception;
    }

    /**
     * Устанавливает исключение возникшее в результате выполнения
     *
     * @param \Exception $exception
     *
     * @return $this
     * @throws \OldTown\Workflow\ZF2\Transaction\Exception\RuntimeException
     */
    public function setException(\Exception $exception)
    {
        if (self::ROLLBACK_STATE !== $this->getState()) {
            $errMsg = sprintf('Set exception not available for %s state', $this->getState());
            throw new Exception\RuntimeException($errMsg);
        }
        $this->exception = $exception;

        return $this;
    }

    /**
     * Возвращает флаг определяющий нужно ли подавить исключение
     *
     * @return bool|null
     * @throws \OldTown\Workflow\ZF2\Transaction\Exception\RuntimeException
     */
    public function getFlagSuppressException()
    {
        if (self::ROLLBACK_STATE !== $this->getState()) {
            $errMsg = sprintf('Flag can not be obtained for the state of %s', $this->getState());
            throw new Exception\RuntimeException($errMsg);
        }
        return $this->flagSuppressException;
    }

    /**
     * Устанавливает флаг определяющий нужно ли подавить исключение
     *
     * @param bool|null $flagSuppressException
     *
     * @return $this
     * @throws \OldTown\Workflow\ZF2\Transaction\Exception\RuntimeException
     */
    public function setFlagSuppressException($flagSuppressException)
    {
        if (self::ROLLBACK_STATE !== $this->getState()) {
            $errMsg = sprintf('Flag can not be set to the state of %s', $this->getState());
            throw new Exception\RuntimeException($errMsg);
        }
        $this->flagSuppressException = (boolean)$flagSuppressException;

        return $this;
    }
}
