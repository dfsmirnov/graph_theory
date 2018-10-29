<?php

//Найдите эйлеров цикл в графе.
//
//Формат входных данных:
//В первой строке указаны два числа разделенных пробелом: v (число вершин) и e (число ребер). В следующих e строках указаны пары вершин, соединенных ребром. Выполняются ограничения: 2≤v≤1000,0≤e≤1000 .
//
//Формат выходных данных:
//Одно слово: NONE, если в графе нет эйлерова цикла, или список вершин в порядке обхода эйлерова цикла, если он есть.
//
ini_set("auto_detect_line_endings", true);

class DeepSearchForNKS
{
    /**
     * @var Graph $graph
     */
    private $graph;
    private $nKS;

    public function doSearch()
    {
        $this->graph = GraphConsoleReader::readGraph();
        $this->nKS = 0;
        $arIxs = $this->graph->getVerticesItemIndexes();
        foreach ($arIxs as $iVertex) {
            $vertex = $this->graph->getVertex($iVertex);
            if ($vertex->isHasTag('red')) {
                continue;
            }
            $this->nKS++;
            $this->processOneVertexWithIndex($vertex, $iVertex, 0);
        }
        return $this->nKS;
    }

    private function processOneVertexWithIndex(VertexWithEdges $vertex, $iCurVertex, $iPrevVertex)
    {
        if ($vertex->isHasTag('red')) {
            return;
        }
        $vertex->addTagToVertex('red');
        $arAdjIx = $vertex->getAdjecentIndexes();
        foreach ($arAdjIx as $iChildVertex) {
            if ($iChildVertex != $iPrevVertex) {
                $childVertex = $this->graph->getVertex($iChildVertex);
                $this->processOneVertexWithIndex($childVertex, $iChildVertex, $iCurVertex);
            }
        }
    }

}

/**
 * Class VertexWithEdges
 */
class VertexWithEdges
{
    private $arAdjacentVerticesIndex;
    private $arTags;

    /**
     * VertexWithEdges constructor.
     */
    public function __construct()
    {
        $this->arAdjacentVerticesIndex = array();
        $this->arTags = array();
    }

    /**
     * @param $iVertex
     */
    public function addEdgeToVertex($iVertex)
    {
        $this->arAdjacentVerticesIndex[$iVertex] = $iVertex;
    }

    /**
     * @param $sTag
     */
    public function addTagToVertex($sTag)
    {
        $this->arTags[$sTag] = true;
    }

    /**
     * @param $sTag
     * @return bool
     */
    public function isHasTag($sTag)
    {
        if (isset($this->arTags[$sTag])) {
            return true;
        } else {
            return false;
        }
    }

    public function getAdjecentIndexes()
    {
        return array_keys($this->arAdjacentVerticesIndex);
    }

}

/**
 * Class Graph
 */
class Graph
{
    /**
     * @var array of VertexWithEdges $arVertex
     */
    private $arVertex;

    /**
     * Graph constructor.
     * @param $nV
     */
    public function __construct($nV)
    {
        $this->arVertex = array();
        for ($i = 1; $i <= $nV; $i++) {
            $this->arVertex[$i] = new VertexWithEdges();
        }
    }

    /**
     * @param VertexWithEdges $vertex
     * @param bool $index
     * @throws Exception
     */
    public function addVertex(VertexWithEdges $vertex, $index = false)
    {
        if ($index && isset($this->arVertex[$index])) {
            throw new Exception("Вершина с интексом {$index} уже существует!");
        }
        if (!$index) {
            $index = $this->findMaxVertexIndex() + 1;
        }
        $this->arVertex[$index] = $vertex;
    }


    /**
     * @return int|string
     */
    public function findMaxVertexIndex()
    {
        $iMax = 0;
        foreach ($this->arVertex as $index => $v) {
            if ($index > $iMax) {
                $iMax = $index;
            }
        }
        return $iMax;
    }

    /**
     * @param $index
     * @return bool|mixed
     */

    /**
     * @return array
     */
    public function getVerticesItemIndexes()
    {
        return array_keys($this->arVertex);

    }

    /**
     * Прверяет есть ли у этой вршины петли
     * @param $iVertex
     * @return bool
     */
    public function isVertexLoop($iVertex)
    {
        $vertex = $this->getVertex($iVertex);
        $arAdjecents = $vertex->getAdjecentIndexes();
        if (in_array($iVertex, $arAdjecents)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $index
     * @return bool|VertexWithEdges
     */
    public function getVertex($index)
    {
        if (!isset($this->arVertex[$index])) {
            return false;
        }
        return $this->arVertex[$index];
    }
}

/**
 * Class GraphConsoleReader
 */
class GraphConsoleReader
{

    /**
     * @throws Exception
     */
    public static function readGraph()
    {
        $fh = fopen('php://stdin', 'r') or die($php_errormsg);
        $sFirstLine = fgets($fh);
        $sFirstLine = trim(preg_replace('/\s\s+/', '', $sFirstLine));
        $arFirstLine = explode(' ', $sFirstLine);
        if (count($arFirstLine) != 2) {
            throw new Exception("Invalid format of first line: needed number_of_vertex number_of_edges");
        }
        $nVertex = $arFirstLine[0];
        $nEdges = $arFirstLine[1];
//        echo $nVertex . "\n";
//        echo $nEdges . "\n";
        $graph = new Graph($nVertex);
        for ($i = 0; $i < $nEdges; $i++) {
            $sLine = fgets($fh);
            $sLine = trim(preg_replace('/\s\s+/', '', $sLine));
            $arLine = explode(' ', $sLine);
            if (count($arLine) != 2) {
                throw new Exception("Invalid format of line with edge: needed index_of_first_vertex index_of_second_vertex");
            }
//            echo $arLine[0] . '->' . $arLine[1];
            $Vertex1 = $graph->getVertex($arLine[0]);
            $Vertex2 = $graph->getVertex($arLine[1]);
            if (!$Vertex1) {
                throw new Exception("Такой вершины не существует:" . $arLine[0]);
            }
            if (!$Vertex2) {
                throw new Exception("Такой вершины не существует:" . $arLine[1]);
            }
            $Vertex1->addEdgeToVertex($arLine[1]);
            $Vertex2->addEdgeToVertex($arLine[0]);
        }
        return $graph;
    }

}

$ds = new DeepSearchForNKS();
echo $ds->doSearch();