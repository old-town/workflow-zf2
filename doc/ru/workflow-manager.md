
# Создание менеджера workflow 
Создание и настройка менеджера workflow (объекта реализующего интерфейс \OldTown\Workflow\WorkflowInterface) 
происходит через абстрактую фабрику  [AbstractWorkflowFactory](src/Factory/AbstractWorkflowFactory.php).

Для работый работы [AbstractWorkflowFactory](src/Factory/AbstractWorkflowFactory.php) необходимо описать конфигурацию
менеджера workflow в конфигурационных файлах приложения. В качестве примера можно использовать файл [workflow.config.dist](config/workflow.config.dist).

# Описание структуры конфига 

Все настройки модуля находятся в секции workflow_zf2

## Пример конфига

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

## Структура конфига


### Секция managers
Ключем является имя менеджера, а значением конфиг описывающий настройки для этого менеджера. Возможные настройки:

* configuration - имя конфигурации. Конфигурация с данным именем должна быть описана в секции configurations
* name - клас реализующий интерфейс \OldTown\Workflow\WorkflowInterface. По умолчанию используется \OldTown\Workflow\Basic\BasicWorkflow


### Секция configurations

Ключем является имя конфигурации менеджера workflow, а значением описание данной конфигурации. Возможные настройки:

* persistence - настройка хранилища состояния запущенных процессов workflow. Содержит следующие настройки
  * name - имя класса. Класс должен реализовывать интерфейс \OldTown\Workflow\Spi\WorkflowStoreInterface.
  * options - массив содержащий настройки хранилища

* factory - настройка фабрики создания workflow.
  * name - имя класса фабрики. По умолчанию используется  [ArrayWorkflowFactory](array-workflow-factory.md)
  * options - массив содержащий настройки фабрики

* resolver - имя класса или имя сервиса доступного в ServiceLocator приложения, реализующего функционал резолвера переменных в xml файле описывающем workflow

## Настройка фабрики создания workflow

Фабрика workflow(класс реализующий интерфейс \OldTown\Workflow\Loader\WorkflowFactoryInterface) отвечает за настройку менеджера workflow.
В поставку библиотеки [old-town/workflow](https://github.com/old-town/old-town-workflow) входит фабрика \OldTown\Workflow\Loader\ArrayWorkflowFactory,
предоставляющая возможность настроить workflow с помощью конфига переданного в массиве(также есть фабрики производящие
настройку на основе конфигурационного xml файла, а также на оснвое url по которому можно скачать конфигурационный xml файл).

В случае если используется фабрика [ArrayWorkflowFactory](array-workflow-factory.md), то как регистрировать файлы workflow,
и производить другие настройки менеджера workflow описывается с оответствующем разделе [документации](array-workflow-factory.md)
