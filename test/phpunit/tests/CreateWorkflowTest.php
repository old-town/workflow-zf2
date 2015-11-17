<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace  OldTown\Workflow\ZF2\PhpUnit\Test\Manager;

use OldTown\Workflow\ZF2\PhpUnit\TestData\TestPaths;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use OldTown\Workflow\WorkflowInterface;


/**
 * Class ManageTest
 *
 * @package OldTown\Workflow\ZF2\PhpUnit\Test\Manager
 */
class CreateWorkflowTest extends AbstractHttpControllerTestCase
{
    /**
     * Проверка создания менеджера
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     * @throws \Zend\ServiceManager\Exception\RuntimeException
     */
    public function testCreateManager()
    {
        /** @noinspection PhpIncludeInspection */
        $applicationConfig = include TestPaths::getPathToDefaultAppConfig();
        $applicationConfig['module_listener_options']['config_glob_paths'][] = sprintf('%s/{*}.php', TestPaths::getPathToCreateWorkflowFromConfig());

        $this->setApplicationConfig($applicationConfig);

        /** @var WorkflowInterface $workflow */
        $workflow = $this->getApplicationServiceLocator()->get('workflow.manager.test_create_manager');

        //$workflow->getWorkflowDescriptor('test');


        static::assertInstanceOf(WorkflowInterface::class, $workflow);
    }
}
