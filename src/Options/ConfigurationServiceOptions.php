<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ConfigurationServiceOptions
 *
 * @package OldTown\Workflow\ZF2\Options
 */
class ConfigurationServiceOptions  extends AbstractOptions
{
    /**
     * Имя сервиса
     *
     * @var string
     */
    protected $name;

    /**
     * Настройки сервиса
     *
     * @var array
     */
    protected $options = [];

    /**
     * Возвращает имя сервиса
     *
     * @return string
     *
     * @throws Exception\InvalidServiceConfigException
     */
    public function getName()
    {
        if (!null === $this->name) {
            $errMsg = 'service name not exists';
            throw new Exception\InvalidServiceConfigException($errMsg);
        }
        return $this->name;
    }

    /**
     * Устанавливает имя сервиса
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    /**
     * Возвращает настройки сервиса
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Устанавливает настройки сервиса
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }
}
