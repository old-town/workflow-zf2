<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use OldTown\Workflow\ZF2\Module;

/**
 * Class ModuleOptionsFactory
 *
 * @package OldTown\Workflow\ZF2\Options
 */
class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ModuleOptions
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var array $appConfig */
        $appConfig = $serviceLocator->get('config');
        $config = [];
        if (array_key_exists(Module::CONFIG_KEY, $appConfig)) {
            $config = $appConfig[Module::CONFIG_KEY];
        }
        $moduleOptions = new ModuleOptions($config);
        return $moduleOptions;
    }
}
