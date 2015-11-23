<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2;

use OldTown\Workflow\ZF2\Controller\EngineController;

return [
    'controllers' => [
        'invokables' => [
            EngineController::class => EngineController::class
        ]
    ],
];