<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2;


use Zend\Mvc\Application;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\View\View;
use OldTown\Workflow\ZF2\ViewRenderer\EmptyModelStrategy;
use OldTown\Workflow\ZF2\ViewRenderer\EmptyModelRenderer;


/**
 * Class Module
 *
 * @package OldTown\Workflow\ZF2
 */
class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface
{

    /**
     * Имя секции в конфиги приложения
     *
     * @var string
     */
    const CONFIG_KEY = 'workflow_zf2';

    /**
     * @param EventInterface $e
     *
     * @return array|void
     */
    public function onBootstrap(EventInterface $e)
    {
        /** @var MvcEvent $e */
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);


        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'));
    }

    /**
     * @param MvcEvent $e
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function onRoute(MvcEvent $e)
    {
        /** @var Application $app */
        $app = $e->getParam('application');
        $sm = $app->getServiceManager();

        if ($sm->has('View')) {

            /** @var View $view */
            $view   = $sm->get('View');

            $eventManager = $view->getEventManager();

            $renderer = new EmptyModelRenderer();
            $listener = new EmptyModelStrategy($renderer);
            $eventManager->attach($listener, 200);

        }

    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }





} 