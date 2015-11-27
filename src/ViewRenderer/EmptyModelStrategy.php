<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ViewRenderer;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\View\ViewEvent;
use Zend\EventManager\EventManagerInterface;


/**
 * Class EmptyModelStrategy
 *
 * @package OldTown\Workflow\ZF2\View
 */
class EmptyModelStrategy extends AbstractListenerAggregate
{
    /**
     * @var EmptyModelRenderer
     */
    protected $renderer;

    /**
     * Constructor
     *
     * @param  EmptyModelRenderer $renderer
     */
    public function __construct(EmptyModelRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, [$this, 'selectRenderer'], $priority);
    }

    /**
     * Detect if we should use the FeedRenderer based on model type and/or
     * Accept header
     *
     * @param  ViewEvent $e
     *
     * @return null|EmptyModelRenderer
     */
    public function selectRenderer(ViewEvent $e)
    {
        $model = $e->getModel();

        if (!$model instanceof EmptyModel) {
            return null;
        }

        return $this->renderer;
    }
}
