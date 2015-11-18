# workflow-zf2
Интеграция workflow c Zend Framework

## Создание менеджера workflow ##
Создание и настройка менеджера workflow (объекта реализующего интерфейс \OldTown\Workflow\WorkflowInterface) 
происходит через абстрактую фабрику  [AbstractWorkflowFactory](src/Factory/AbstractWorkflowFactory.php).

Для работый работы [AbstractWorkflowFactory](src/Factory/AbstractWorkflowFactory.php) необходимо описать конфигурацию
менеджера workflow в конфигурационных файлах приложения. В качестве примера можно использовать файл [workflow.config.dist](config/workflow.config.dist).

Описание структуры конфига
* Пример конфига
```php
use OldTown\Workflow\Basic\BasicWorkflow;
use OldTown\Workflow\Spi\Memory\MemoryWorkflowStore;
use OldTown\Workflow\Loader\ArrayWorkflowFactory;
use OldTown\Workflow\Util\DefaultVariableResolver;

return [
     //Секция содержащая настройки модуля
    'workflow_zf2'    => [

        //Секция описывающая менеджеры workflow
        'managers' => [
            //Имя менеджера
            'test_create_manager' => [
                //Имя ключа в секции configuration. Описание настроек менеджера workflow
                'configuration' => 'default',
                //Имя класса менеджера workflow или имя сервиса доступного в ServiceLocator приложения
                'name' => BasicWorkflow::class
            ]
        ],
    
        //Секция описывающая конфигурации для менеджереров workflow
        'configurations' => [
            //имя конфигурации
            'default' => [
                //настройки хранилища состояния запущенного процесса workflow
                'persistence' => [
                    //Имя класса реализующего функционал по сохранению состояния workflow
                    'name' => MemoryWorkflowStore::class,
                    //Настройки для хранилища workflow
                    'options' => [

                    ]
                ],
                //Настройки фабрики создания workflow
                'factory' => [
                    //Имя фабрики
                    'name' => ArrayWorkflowFactory::class,
                    //Опции для фабрики
                    'options' => [
                        'reload' => true,
                        'workflows' => [
                            'test' => [
                                'location' => __DIR__ . '/../../../../../../../../config/workflow/example.xml'
                            ]
                        ]
                    ]
                ],
                //Имя класса или имя сервиса доступного в ServiceLocator приложения, реализующего функционал
                //резолвера переменных в xml файле описывающем workflow
                'resolver' => DefaultVariableResolver::class,
            ]
        ]
    ]
];
```

* Описание конфига(Все настройки модуля находятся в секции workflow_zf2)
** Секция managers. Описание зарегестрированных менеджеров workflow. Ключем является имя мендежра.
*** Настройки конкретного менеджера
