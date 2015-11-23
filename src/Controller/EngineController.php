<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use OldTown\Workflow\ZF2\Service\Workflow;


/**
 * Class EngineController
 *
 * @package OldTown\Workflow\ZF2\Controller
 *
 */
class EngineController extends AbstractActionController
{

    /**
     * Создание нового процесса workflow
     *
     * @throws \OldTown\Workflow\ZF2\Controller\Exception\InvalidArgumentException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OldTown\Workflow\ZF2\Service\Exception\InvalidInitializeWorkflowEntryException
     */
    public function initializeAction()
    {
        $routeMatch = $this->getEvent()->getRouteMatch();
        $workflowManagerName = $routeMatch->getParam('managerName', null);
        if (null === $workflowManagerName) {
            $errMsg = 'Param managerName not found';
            throw new Exception\InvalidArgumentException($errMsg);
        }

        $workflowActionName = $routeMatch->getParam('actionName', null);
        if (null === $workflowActionName) {
            $errMsg = 'Param actionName not found';
            throw new Exception\InvalidArgumentException($errMsg);
        }

        $workflowName = $routeMatch->getParam('workflowName', null);
        if (null === $workflowName) {
            $errMsg = 'Param workflowName not found';
            throw new Exception\InvalidArgumentException($errMsg);
        }

        /** @var Workflow $workflowService */
        $workflowService = $this->getServiceLocator()->get(Workflow::class);

        $workflowService->initialize($workflowManagerName, $workflowName, $workflowActionName);

        return [];
    }

    /**
     * Выполнение нового действия
     *
     *
     */
    public function doAction()
    {
//        $routeMatch = $this->getEvent()->getRouteMatch();
//
//        $workflowManagerName = $routeMatch->getParam('managerName', null);
//        $workflowActionName = $routeMatch->getParam('actionName', null);
//        $entryId = $routeMatch->getParam('entryId', null);


        return [];
    }

}
