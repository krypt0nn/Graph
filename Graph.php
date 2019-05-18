<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     Graph
 * @copyright   2019 Podvirnyy Nikita (KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @author      Podvirnyy Nikita (KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

/**
 * @example
 * 
 * <?php
 * 
 * require 'Graph.php';
 * 
 * use Graph\Graph;
 * 
 * $graph = (new Graph)->buildByVectorsList ([
 *     'A' => ['B' => 1, 'D' => 2, 'E' => 3],
 *     'B' => ['A' => 1, 'C' => 4, 'D' => 5],
 *     'C' => ['B' => 4, 'E' => 7],
 *     'D' => ['A' => 2, 'B' => 5, 'E' => 6],
 *     'E' => ['A' => 3, 'C' => 7, 'D' => 6],
 *     'F' => []
 * ]);
 * 
 * // Минимальные расстояния между вершинами
 * print_r ($graph->countHipsDistances (true));
 * 
 */

namespace Graph;

/**
 * Вершина графа
 */
class Node
{
    public $data;
    public $id;
    public $links = []; // Список связей

    /**
     * Конструктор
     * 
     * @param mixed $data - данные вершины
     * @param int $id     - ID вершины
     */
    public function __construct ($data, int $id)
    {
        $this->data = $data;
        $this->id   = $id;
    }

    /**
     * Создать ориентированную связь
     * 
     * @param Node $node    - нода для связи
     * @param mixed $weight - вес связи
     * 
     * @return Node - возвращает саму себя
     */
    public function link (Node $node, $weight): Node
    {
        $this->links[] = [$node->id, $weight];

        return $this;
    }

    /**
     * Удалить связь
     * 
     * @param Node $node - нода для удаления связи
     * 
     * @return Node - возвращает саму себя
     */
    public function unlink (Node $node): Node
    {
        $id = $node->id;

        foreach ($this->links as $id => $link)
            if ($link[0] == $id)
                unset ($this->links[$id]);

        return $this;
    }
}

/**
 * Интерфейс для работы с графом
 */
class Graph
{
    public $graph = [];
    public $count = 0;

    /**
     * Конструктор графа по матрице смежности
     * 
     * @param array $matrix - матрица смежности
     * 
     * @return Graph - возвращает созданный граф
     */
    public function buildByAdjacencyMatrix (array $matrix): Graph
    {
        $this->graph = [];
        $this->count = 0;

        foreach ($matrix as $node => $links)
            $this->graph[] = new Node ($node, $this->count++);
        
        $i = 0;

        foreach ($matrix as $node1 => $links)
        {
            $j = 0;

            foreach ($links as $node2 => $linkWeight)
                if ($linkWeight !== null)
                    $this->graph[$i]->link ($this->graph[$j++], $linkWeight);

                else ++$j;

            ++$i;
        }

        return $this;
    }

    /**
     * Конструктор графа по списку векторов вершин
     * 
     * @param array $vectors - список векторов вершин (ассоциативный массив [вершина] => ассоциативных массивов [связанная вершина] => [вес связи])
     * 
     * @return Graph - возвращает созданный граф
     */
    public function buildByVectorsList (array $vectors): Graph
    {
        $this->graph = [];
        $this->count = 0;

        $selectors = [];
        $i = 0;

        foreach ($vectors as $node => $links)
        {
            $this->graph[] = new Node ($node, $this->count++);

            $selectors[$node] = $i++;
        }

        foreach ($vectors as $node1 => $links)
            foreach ($links as $node2 => $linkWeight)
                $this->graph[$selectors[$node1]]->link ($this->graph[$selectors[$node2]], $linkWeight);

        return $this;
    }

    /**
     * Конструктор графа по списку готовых вершин
     * 
     * @param array $nodes - список вершин графа
     * 
     * @return Graph - возвращает созданный граф
     */
    public function buildByNodesList (array $nodes): Graph
    {
        $this->graph = $nodes;
        $this->count = sizeof ($nodes);

        return $this;
    }

    /**
     * Получение списка вершин
     * 
     * @return array - возвращает список вершин
     */
    public function getNodes (): array
    {
        return $this->graph;
    }

    /**
     * Получение списка значений вершин
     * 
     * @return array - возвращает список значений вершин
     */
    public function list (): array
    {
        return array_map (function ($node)
        {
            return $node->data;
        }, $this->graph);
    }

    /**
     * Получение матрицы смежности графа
     * 
     * [@param bool $useNodesIds = false]    - использовать ли ID вершин вместо их значений
     * [@param bool $useLinksWeights = true] - использовать ли веса связей вершин вместо (bool) true
     * 
     * @return array - возвращает матрицу смежности графа
     */
    public function getAdjacencyMatrix (bool $useNodesIds = false, bool $useLinksWeights = true): array
    {
        $matrix = [];
        $links  = [];

        foreach ($this->graph as $node)
            foreach ($node->links as $link)
                $links[$useNodesIds ? $node->id : $node->data][$link[0]] = $useLinksWeights ?
                    $link[1] : true;

        foreach ($this->graph as $node)
            for ($i = 0; $i < $this->count; ++$i)
            {
                $id = $useNodesIds ?
                    $node->id : $node->data;

                $id2 = $useNodesIds ?
                    $this->graph[$i]->id : $this->graph[$i]->data;

                $matrix[$id][$id2] = isset ($links[$id][$i]) ?
                    $links[$id][$i] : null;
            }

        return $matrix;
    }

    /**
     * Получение списка векторов вершин графа
     * 
     * [@param bool $useNodesIds = false] - использовать ли ID вершин вместо их значений
     * 
     * @return array - возвращает список векторов вершин графа
     */
    public function getVectorsList (bool $useNodesIds = false): array
    {
        $list = [];

        foreach ($this->graph as $node)
        {
            $id = $useNodesIds ?
                $node->id : $node->data;

            $list[$id] = [];

            foreach ($node->links as $link)
            {
                $id2 = $useNodesIds ?
                    $this->graph[$link[0]]->id : $this->graph[$link[0]]->data;

                $list[$id][$id2] = $link[1];
            }
        }

        return $list;
    }

    /**
     * Подсчёт дистанций между вершинами графа (алгоритм Флойда)
     * 
     * [@param bool $useLinksWeights = false] - использовать ли веса связей между вершинами вместо (bool) true
     * 
     * @return Graph - возвращает граф дистанций между вершинами
     */
    public function countHipsDistances (bool $useLinksWeights = false): Graph
    {
        $distances = $this->getAdjacencyMatrix (true, $useLinksWeights);

        for ($k = 0; $k < $this->count; ++$k)
            for ($i = 0; $i < $this->count; ++$i)
                for ($j = 0; $j < $this->count; ++$j)
                {
                    if ($distances[$i][$j] === null)
                        $distances[$i][$j] = PHP_INT_MAX;

                    if ($distances[$i][$k] === null)
                        $distances[$i][$k] = PHP_INT_MAX;

                    if ($distances[$k][$j] === null)
                        $distances[$k][$j] = PHP_INT_MAX;

                    $distances[$i][$j] = min ((double) $distances[$i][$j], (double) $distances[$i][$k] + (double) $distances[$k][$j]);
                }

        $vectorsList = [];

        foreach ($distances as $node1 => $nodeDistances)
        {
            $vectorsList[$this->graph[$node1]->data] = [];

            foreach ($nodeDistances as $node2 => $distance)
                if ($distance < PHP_INT_MAX)
                    $vectorsList[$this->graph[$node1]->data][$this->graph[$node2]->data] = $distance;
        }

        return (new Graph ())->buildByVectorsList ($vectorsList);
    }

    /**
     * DFS; последовательный проход по всем вершинам графа
     * 
     * @param Node $node - нода для запуска DFS
     * @param \Closure $callable - функция для итерации вершины (аргумент - Node)
     * [@param array $used = []] - список ID посещённых вершин
     */
    public function DFS (Node $node, \Closure $callable, array $used = [])
    {
        $used[$node->id] = true;

        foreach ($node->links as $link)
            if (!isset ($used[$link[0]]))
                $this->DFS ($this->graph[$link[0]], $callable, $used);

        return $callable ($node);
    }

    /**
     * Поиск циклов
     * 
     * @return array - возвращает список циклов
     */
    public function findCycles (): array
    {
        $cycles = [];

        foreach ($this->graph as $node)
            $cycles = array_merge ($cycles, $this->cyclesDFS ($node));

        return $cycles;
    }

    // Специализированный DFS для поиска циклов
    protected function cyclesDFS (Node $node, array $path = [], array $used = [], $cycles = []): array
    {
        $path[] = $node;
        $used[$node->id] = true;

        foreach ($node->links as $link)
            if (!isset ($used[$link[0]]))
                $cycles = $this->cyclesDFS ($this->graph[$link[0]], $path, $used, $cycles);

            else $cycles[] = (new Graph)->buildByNodesList ($path);

        return $cycles;
    }

    /**
     * Поиск клик (взаимосвязанных подграфов текущего графа)
     * 
     * [@param array $nodes = []] - вершины для поиска клик
     * [@param array $potentialClique = []] - потенциальные вершины клики
     * [@param array $skipNodes = []] - список пропускаемых вершин
     * 
     * @return array - возвращает список подграфов-клик
     */
    public function findCliques (array $nodes = null, array $potentialClique = [], array $skipNodes = []): array
    {
        if ($nodes === null)
            $nodes = $this->graph;
        
        if (sizeof ($nodes) == 0 && sizeof ($skipNodes) == 0)
            return [(new Graph)->buildByNodesList ($potentialClique)];

        $cliques = [];

        foreach ($nodes as $id => $node)
        {
            $newPotentialClique = array_merge ($potentialClique, [$node]);
            $newNodes = [];

            foreach ($nodes as $node2)
                foreach ($node->links as $link)
                    if ($link[0] == $node2->id)
                        $newNodes[] = $node2;

            $newSkipNodes = [];

            foreach ($skipNodes as $node2)
                foreach ($node->links as $link)
                    if ($link[0] == $node2->id)
                        $newSkipNodes[] = $node2;

            $cliques = array_merge ($cliques, $this->findCliques ($newNodes, $newPotentialClique, $newSkipNodes));

            unset ($nodes[$id]);
            $skipNodes[] = $node;
        }

        return $cliques;
    }
}
