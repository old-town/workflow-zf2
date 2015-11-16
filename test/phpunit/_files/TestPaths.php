<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2\PhpUnit\TestData;

/**
 * Class TestPaths
 *
 * @package OldTown\Workflow\ZF2\PhpUnit\TestData
 */
class TestPaths
{
    /**
     * Путь до директории где находится файл инициирующий приложение
     *
     * @return string
     */
    public static function getPathToModule()
    {
        return __DIR__ . '/../../../';
    }

    /**
     * Путь до дефалтового конфига приложения
     *
     * @return string
     */
    public static function getPathToDefaultAppConfig()
    {
        return __DIR__ . '/../_files/DefaultApp/application.config.php';
    }

    /**
     * Путь до директории содержащей конфиги для тестирования создания workflow
     *
     * @return string
     */
    public static function getPathToCreateWorkflowFromConfig()
    {
        return __DIR__ . '/../_files/CreateWorkflowFromConfig/config/';
    }
}
