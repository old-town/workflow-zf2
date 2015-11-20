<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2;

return [
    'router' => [
        'routes' => [
            'workflow' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/workflow/',
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'engine' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => 'engine/',
                        ]
                    ]
                ]
            ],
        ]
    ]
];