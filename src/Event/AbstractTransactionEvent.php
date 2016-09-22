<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Event;

use Zend\EventManager\Event;
use Ramsey\Uuid\Uuid;

/**
 * Class AbstractTransactionEvent
 *
 * @package OldTown\Workflow\ZF2\Event
 */
abstract class AbstractTransactionEvent extends Event
{
    /**
     * Инициализаци транзакции
     *
     * @var string
     */
    const START_TRANSACTION = 'startTransaction';

    /**
     * Фиксация транзакции
     *
     * @var string
     */
    const COMMIT_TRANSACTION = 'startTransaction';

    /**
     * Отка транзакции транзакции
     *
     * @var string
     */
    const ROLLBACK_TRANSACTION = 'startTransaction';

    /**
     * Имя wf менеджера
     *
     * @var string
     */
    private $managerName;

    /**
     * Имя перехода
     *
     * @var string
     */
    private $actionName;

    /**
     * Уникальный идендификатор комманды
     *
     * @var string
     */
    private $commandUuid;

    /**
     * AbstractTransactionEvent constructor.
     *
     * @param string $managerName
     * @param string $actionName
     */
    public function __construct($managerName, $actionName)
    {
        $this->managerName = $managerName;
        $this->actionName = $actionName;

        $this->commandUuid = Uuid::uuid4()->toString();
        parent::__construct();
    }

    /**
     * Имя wf менеджера
     *
     * @return string
     */
    public function getManagerName()
    {
        return $this->managerName;
    }

    /**
     * Имя перехода
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Уникальный идендификатор комманды
     *
     * @return string
     */
    public function getCommandUuid()
    {
        return $this->commandUuid;
    }
}
