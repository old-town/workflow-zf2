<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\ServiceEngine;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\WorkflowInterface;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow\TransitionResultInterface;

/**
 * Interface WorkflowServiceInterface
 *
 * @package OldTown\Workflow\ZF2\ServiceEngine
 */
interface WorkflowServiceInterface
{
    /**
     * Запуск перехода из отдного состояния в другое
     *
     * @param                        $managerName
     * @param                        $entryId
     * @param                        $actionName
     *
     * @param TransientVarsInterface $transientVars
     *
     * @return TransitionResultInterface
     */
    public function doAction($managerName, $entryId, $actionName, TransientVarsInterface $transientVars = null);

    /**
     * Создание процесса workflow
     *
     * @param                        $managerName
     * @param                        $workflowName
     * @param                        $actionName
     *
     * @param TransientVarsInterface $transientVars
     *
     * @return TransitionResultInterface
     */
    public function initialize($managerName, $workflowName, $actionName, TransientVarsInterface $transientVars = null);


    /**
     * Получение менеджера workflow по имени
     *
     * @param string $managerName
     *
     * @return WorkflowInterface
     *
     */
    public function getWorkflowManager($managerName);

    /**
     * @param WorkflowDescriptor $wf
     * @param                    $actionName
     *
     */
    public function getActionByName(WorkflowDescriptor $wf, $actionName);

    /**
     * Проверят есть ли менеджер workflow с заданным именем
     *
     * @param string $workflowManagerName
     *
     * @return bool
     */
    public function hasWorkflowManager($workflowManagerName);

    /**
     * Возвращает имя менеджера по его пседовниму
     *
     * @param $alias
     *
     * @return string
     *
     */
    public function getManagerNameByAlias($alias);


    /**
     * Проверяет есть ли псевдоним для менеджера workflow
     *
     * @param string $alias
     *
     * @return boolean
     */
    public function hasWorkflowManagerAlias($alias);

    /**
     * Получение менеджера workflow по его псевдониму
     *
     * @param $alias
     *
     * @return WorkflowInterface
     *
     */
    public function getWorkflowManagerByAlias($alias);
}
