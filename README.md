# Graph

**Graph** - структура данных [граф](https://ru.wikipedia.org/wiki/Граф_(математика)) для **PHP** 7+

Для создания графа можно использовать матрицу смежности, список векторов связанных вершин или список готовых вершин графа. Рассмотрим их на примере этого графа

<img src="https://i.ibb.co/9YnjxSd/Graph.png" alt="Пример графа из кода ниже">

**buildByAdjacencyMatrix**

```php
<?php

use Graph\Graph;

$graph = (new Graph)->buildByAdjacencyMatrix ([
    'A' => ['A' => null, 'B' => 1, 'C' => 1, 'D' => 3],
    'B' => ['A' => 1, 'B' => null, 'C' => null, 'D' => 2],
    'C' => ['A' => 1, 'B' => null, 'C' => null, 'D' => 2],
    'D' => ['A' => 3, 'B' => 2, 'C' => 2, 'D' => null]
]);
```

**buildByVectorsList**

```php
<?php

use Graph\Graph;

$graph = (new Graph)->buildByVectorsList ([
    'A' => ['B' => 1, 'C' => 1, 'D' => 3],
    'B' => ['A' => 1, 'D' => 2],
    'C' => ['A' => 1, 'D' => 2],
    'D' => ['A' => 3, 'B' => 2, 'C' => 2]
]);
```

**buildByNodesList**

```php
<?php

use Graph\Graph;

$nodes = [
    new Node ('A', 0),
    new Node ('B', 1),
    new Node ('C', 2),
    new Node ('D', 3)
];

$graph = (new Graph)->buildByNodesList ([
    $nodes[0]->link ($nodes[1], 1)->link ($nodes[2], 1)->link ($nodes[3], 3),
    $nodes[1]->link ($nodes[0], 1)->link ($nodes[3], 2),
    $nodes[2]->link ($nodes[0], 1)->link ($nodes[3], 2),
    $nodes[3]->link ($nodes[0], 3)->link ($nodes[1], 2)->link ($nodes[2], 2)
]);
```

Для графов доступны:

* просчёт кратчайших дистанций между вершинами
* поиск клик
* поиск циклов
* проход по графу *(**DFS**)*
* конвертация в различные способы представления

Дистанции между вершинами:

```php
<?php

// true - использовать ли для подсчёта веса соединений
// Выведет объект Graph - граф дистанций между вершинами
print_r ($graph->countHipsDistances (true));
```

Клики:

```php
<?php

// Выведет список подграфов-клик
print_r ($graph->findCliques ());
```

Циклы:

```php
<?php

$graph = (new Graph)->buildByVectorsList ([
    'A' => ['B' => 1],
    'B' => ['C' => 1],
    'C' => ['D' => 1],
    'D' => ['B' => 1]
]);

// Выведет список подграфов-циклов:
// B -> C -> D -> B
// C -> D -> B -> C
// D -> B -> C -> D

print_r ($graph->findCycles ());
```

> Граф из примера выше: <br><br>
<img src="https://i.ibb.co/qgH2TKY/Graph-2.png" alt="Граф из примера выше">

Ну, как-то так

Автор: [Подвирный Никита](https://vk.com/technomindlp). Специально для [Enfesto Studio Group](http://vk.com/hphp_convertation)