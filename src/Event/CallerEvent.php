<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Event;


use Zend\EventManager\Event;

/**
 * Class CallerEvent
 *
 * @package OldTown\Workflow\ZF2\Event
 */
class CallerEvent extends Event
{
    /**
     * @var string
     */
    const EVENT_RESOLVE_CALLER = 'resolve.caller';

    /**
     * @var string|null
     */
    protected $caller;

    /**
     * @return null|string
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * @param null|string $caller
     *
     * @return $this
     */
    public function setCaller($caller)
    {
        $this->caller = $caller;

        return $this;
    }

}
