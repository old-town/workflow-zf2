<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ViewRenderer;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ModelInterface;


/**
 * Class EmptyModel
 *
 * @package OldTown\Workflow\ZF2\View
 */
class EmptyModel implements ModelInterface
{
    /**
     * Renderer options
     * @var array
     */
    protected $options = [];

    /**
     * @inheritdoc
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function getIterator()
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[(string) $name] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param array|Traversable $options
     *
     * @return $this
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\InvalidArgumentException
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        $this->options = $options;
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param null   $default
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function getVariable($name, $default = null)
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void|ModelInterface
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function setVariable($name, $value)
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @param array|\ArrayAccess $variables
     *
     * @return void|ModelInterface
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function setVariables($variables)
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function getVariables()
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @param string $template
     *
     * @return void|ModelInterface
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function setTemplate($template)
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getTemplate()
    {
        return 'empty';
    }

    /**
     * @inheritdoc
     *
     * @param ModelInterface $child
     * @param null           $captureTo
     * @param bool|false     $append
     *
     * @return void|ModelInterface
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function addChild(ModelInterface $child, $captureTo = null, $append = false)
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function getChildren()
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function hasChildren()
    {
        return false;
    }

    /**
     * @inheritdoc
     *
     * @param string $capture
     *
     * @return void|ModelInterface
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function setCaptureTo($capture)
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function captureTo()
    {
        return false;
    }

    /**
     * @inheritdoc
     *
     * @param bool $terminate
     *
     * @return void|ModelInterface
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function setTerminal($terminate)
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function terminate()
    {
        return false;
    }

    /**
     * @inheritdoc
     *
     * @param bool $append
     *
     * @return void|ModelInterface
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function setAppend($append)
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }

    /**
     * @inheritdoc
     *
     * @return boolean
     */
    public function isAppend()
    {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws \OldTown\Workflow\ZF2\ViewRenderer\Exception\BadMethodCallException
     */
    public function count()
    {
        $errMsg = sprintf('Method %s not supported', __METHOD__);
        throw new Exception\BadMethodCallException($errMsg);
    }
}
