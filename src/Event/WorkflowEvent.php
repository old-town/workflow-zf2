<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Event;

use OldTown\Workflow\ZF2\Service\Workflow\TransitionResultInterface;
use OldTown\Workflow\ZF2\Service\Workflow\TransitionResultTrait;
use Zend\EventManager\Event;

/**
 * Class WorkflowEvent
 *
 * @package OldTown\Workflow\ZF2\Event
 */
class WorkflowEvent extends Event implements TransitionResultInterface
{
    use TransitionResultTrait;

    /**
     * Отображение результатов работы workflow
     *
     * @var string
     */
    const EVENT_RENDER         = 'render';

    /**
     * Запуск нового процесса workflow
     *
     * @var string
     */
    const EVENT_WORKFLOW_INITIALIZE         = 'initialize';

    /**
     * Запуск перехода между двумя действиями workflow
     *
     * @var string
     */
    const EVENT_DO_ACTION         = 'doAction';
}
