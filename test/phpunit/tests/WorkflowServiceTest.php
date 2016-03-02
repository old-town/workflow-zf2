<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\PhpUnit\Test\Manager;

use OldTown\Workflow\ZF2\PhpUnit\TestData\TestPaths;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow;


/**
 * Class WorkflowServiceTest
 *
 * @package OldTown\Workflow\ZF2\PhpUnit\Test\Manager
 */
class WorkflowServiceTest extends AbstractHttpControllerTestCase
{
    /**
     * Проверка того что имена ищутся в рамках текущего степа
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     * @throws \Zend\ServiceManager\Exception\RuntimeException
     */
    public function testCorrectResoleNameForDoAction()
    {
        /** @noinspection PhpIncludeInspection */
        $applicationConfig = include TestPaths::getPathToDefaultAppConfig();
        $applicationConfig['module_listener_options']['config_glob_paths'][] = sprintf('%s/{*}.php', TestPaths::getPathToWorkflowServiceConfig());

        $this->setApplicationConfig($applicationConfig);

        $wfManager = 'test_create_manager';
        $wfName = 'test';


        /** @var Workflow $workflow */
        $workflow = $this->getApplicationServiceLocator()->get(Workflow::class);

        $result = $workflow->initialize($wfManager, $wfName, 'startWorkflow');

        $entryId = $result->getEntryId();

        static::assertEquals(1010, $workflow->findActionByNameForEntry($wfManager, $entryId, 'testAction')->getId());
        $workflow->doAction($wfManager, $entryId, 'testAction');
        static::assertEquals(2010, $workflow->findActionByNameForEntry($wfManager, $entryId, 'testAction')->getId());
    }
}
