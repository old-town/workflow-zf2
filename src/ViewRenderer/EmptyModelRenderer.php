<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ViewRenderer;

use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface as Resolver;

/**
 * Class EmptyModelRenderer
 *
 * @package OldTown\Workflow\ZF2\View
 */
class EmptyModelRenderer  implements Renderer
{
    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param  Resolver $resolver
     * @return $this
     */
    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param string|\Zend\View\Model\ModelInterface $nameOrModel
     * @param null                                   $values
     *
     * @return string
     */
    public function render($nameOrModel, $values = null)
    {
        return  '';
    }
}
