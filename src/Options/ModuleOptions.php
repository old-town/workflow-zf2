<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * @package OldTown\Workflow\ZF2\Options
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * Конфигурация менеджера
     *
     * @var  array
     */
    protected $manager;

    /**
     * @return array
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param array $manager
     *
     * @return $this
     */
    public function setManager(array $manager)
    {
        $this->manager = $manager;

        return $this;
    }
}
