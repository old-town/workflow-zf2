<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Controller;


use \Zend\Mvc\Controller\AbstractActionController;


/**
 * Class EngineController
 *
 * @package OldTown\Workflow\ZF2\Controller
 *
 * @method \Zend\Http\PhpEnvironment\Request getRequest()
 */
class EngineController extends AbstractActionController
{

    /**
     * Создание нового процесса workflow
     *
     *
     */
    public function initializeAction()
    {
        $routeMatch = $this->getEvent()->getRouteMatch();
        $workflowManagerName = $routeMatch->getParam('managerName', null);
        $workflowActionName = $routeMatch->getParam('actionName', null);
        $workflowName = $routeMatch->getParam('workflowName', null);

        return [];
    }

    /**
     * Выполнение нового действия
     *
     *
     */
    public function doAction()
    {
        $routeMatch = $this->getEvent()->getRouteMatch();

        $workflowManagerName = $routeMatch->getParam('managerName', null);
        $workflowActionName = $routeMatch->getParam('actionName', null);
        $entryId = $routeMatch->getParam('entryId', null);


        return [];
    }

}
