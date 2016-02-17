<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\MutableCreationOptionsTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow;
use OldTown\Workflow\ZF2\Options\ModuleOptions;


/**
 * Class WorkflowServiceFactory
 *
 * @package OldTown\Workflow\ZF2\Factory
 *
 */
class WorkflowServiceFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    use MutableCreationOptionsTrait;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Workflow
     *
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\ZF2\ServiceEngine\Exception\InvalidArgumentException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get(ModuleOptions::class);

        $options = [
            'serviceLocator' => $serviceLocator,
            'moduleOptions'  => $moduleOptions
        ];

        return new Workflow($options);
    }
}
