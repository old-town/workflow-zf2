<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\Factory;


use Zend\Mvc\Application;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\MutableCreationOptionsTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use OldTown\Workflow\ZF2\Options\ModuleOptions;
use OldTown\Workflow\Basic\BasicWorkflow;
use OldTown\Workflow\ZF2\Event\CallerEvent;
use OldTown\Workflow\ZF2\Workflow\Config\ArrayConfiguration;


/**
 * Class PluginMessageAbstractFactory
 *
 * @package OldTown\EventBus\Message
 */
class BasicWorkflowFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    use MutableCreationOptionsTrait;


    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BasicWorkflow
     *
     * @throws Exception\RuntimeException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {


        $caller = false;
        if ($serviceLocator->has('Application')) {
            /** @var Application $app */
            $app = $serviceLocator->get('Application');
            $callerEvent = new CallerEvent();
            $app->getEventManager()->trigger(CallerEvent::EVENT_RESOLVE_CALLER, $callerEvent);
            if ($callerEvent->getCaller()) {
                $caller = $callerEvent->getCaller();
            }
        }

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get(ModuleOptions::class);
        $config = $this->buildWorkflowConfig($moduleOptions);

        $w = new BasicWorkflow($caller);
        $w->setConfiguration($config);


        //$w->initialize();

        return $w;
    }

    /**
     * @param ModuleOptions $moduleOptions
     *
     * @return ArrayConfiguration
     */
    protected function buildWorkflowConfig(ModuleOptions $moduleOptions)
    {
//        $options = $moduleOptions->
//        $config = new ArrayConfiguration();


        return $config;
    }
}
