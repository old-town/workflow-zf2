<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Event;

/**
 * Class DoActionTransactionEvent
 *
 * @package OldTown\Workflow\ZF2\Event
 */
class DoActionTransactionEvent extends AbstractTransactionEvent
{
    /**
     * Уникальный идендфикатор процесса workflow
     *
     * @var string
     */
    private $entryId;

    /**
     * DoActionTransactionEvent constructor.
     *
     * @param string $managerName
     * @param string $entryId
     * @param string $actionName
     */
    public function __construct($managerName, $entryId, $actionName)
    {
        $this->entryId = $entryId;
        parent::__construct($managerName, $actionName);
    }

    /**
     * Уникальный идендфикатор процесса workflow
     *
     * @return string
     */
    public function getEntryId()
    {
        return $this->entryId;
    }
}
