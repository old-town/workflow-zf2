<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2;

use OldTown\Workflow\ZF2\Controller\EngineController;

return [
    'router' => [
        'routes' => [
            'workflow' => [
                'type'         => 'Literal',
                'options'      => [
                    'route' => '/workflow/',
                ],
                'child_routes' => [
                    'engine' => [
                        'type'         => 'segment',
                        'options'      => [
                            'route'       => 'engine/manager/:managerName/action/:actionName/',
                            'constraints' => [
                                'managerName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'actionName'  => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ]
                        ],
                        'child_routes' => [
                            'doAction'   => [
                                'type'    => 'segment',
                                'options' => [
                                    'route'       => 'entry/:entryId',
                                    'constraints' => [
                                        'entryId' => '\d+'
                                    ],
                                    'defaults'    => [
                                        'controller' => EngineController::class,
                                        'action' => 'do'
                                    ],
                                ],
                            ],
                            'initialize' => [
                                'type'    => 'segment',
                                'options' => [
                                    'route'       => 'name/:workflowName',
                                    'constraints' => [
                                        'workflowName' => '[a-zA-Z][a-zA-Z0-9_-]*'
                                    ],
                                    'defaults'    => [
                                        'controller' => EngineController::class,
                                        'action' => 'initialize'
                                    ],
                                ],
                            ]
                        ]
                    ]
                ],
            ]
        ]
    ]
];