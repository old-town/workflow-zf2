# Сервис для работы с workflow

Сервис [Workflow](./../../src/Service/Workflow.php) предоставляет базовый функционал для работы с workflow.

## Получение сервиса

Для получения сервиса, необходим получить экземплря ServiceLocator приложения. Пример получения.

```php
  
        /** @var \OldTown\Workflow\ZF2\Service\Workflow $workflowService */
        $workflowService = $this->getServiceLocator()->get(\OldTown\Workflow\ZF2\Service\Workflow::class);
```

## Базовые методы сервиса

### Инициализация нового процесса workflow

```php
        /** @var \OldTown\Workflow\ZF2\Service\Workflow $workflowService */
        $workflowService = $this->getServiceLocator()->get(\OldTown\Workflow\ZF2\Service\Workflow::class);
        $workflowService->initialize($workflowManagerName, $workflowName, $workflowActionName);
```

Праметра:
* $workflowManagerName - имя менеджера workflow
* $workflowName - имя зарегестрированного workflow
* $workflowActionName - имя действия которое должно быть вызвано для инициализации


### Запуск перехода между двумя состояниями workflow

```php

        /** @var \OldTown\Workflow\ZF2\Service\Workflow $workflowService */
        $workflowService = $this->getServiceLocator()->get(\OldTown\Workflow\ZF2\Service\Workflow::class);
        $workflowService->doAction($workflowManagerName, $entryId, $workflowActionName);
```

Праметра:
* $workflowManagerName - имя менеджера workflow
* $entryId - id уже существующего запущенного процесса workflow
* $workflowActionName - имя действия которое должно быть вызвано для инициализации


## Базовые события сервиса

Сервис имеет своей менеджер событий. Во время работые сервиса, могут бросаться события позволяющие взаймодействовать с
сервисом из других модулей. Для передачи информации о событии, используется объект события инстанцированного от класса
[WorkflowEvent](./../../src/Event/WorkflowEvent.php). Сервис бросает следующие события:

### initialize
 Событие бросается когда создается новый процесс workflow.
 
### doAction
 Событие бросается когда происходит переход между двумя состояниями
 
### render
 Событие бросается когда необходимо отобразаить результаты работы workflow
 
### Объект события сервиса
 Для взаимодействия с другими модулями, используется менеджер событий. События бросамые сервисом инкапсулированы от класса
 [WorkflowEvent](./../../src/Event/WorkflowEvent.php). Событие содержит следующую информацию:
 
 * workflow - дескриптор описывающий workflow. Объект реализующий \OldTown\Workflow\Loader\WorkflowDescriptor
 * transientVars - переменные времени выполнения workflow. Там содержаться результаты работы workflow
 * viewName - имя вида, используется для отображения результатов работы workflow в интегрируемой системе. 
 * workflowManager - менеджер workflow. Объект реализующий интерфейс \OldTown\Workflow\WorkflowInterface
 * entryId - идендификатор запущенного процесса workflow

 