<?php
/*
 *
На вход программе подаётся описание простого связного графа.
Первая строка содержит два числа: число вершин V≤10000 и число рёбер E≤30000 графа.
Следующие E строк содержат номера пар вершин, соединенных рёбрами.
Вершины имеют номера от 0 до V−1.
Выведите список из V чисел — расстояний от вершины 0 до соответствующих вершин графа.

Sample Input:

6 7
0 1
1 2
2 0
3 2
4 3
4 2
5 4
Sample Output:

0 1 1 2 2 3
 */
ini_set("auto_detect_line_endings", true);

class WideSearchForVerticesDistance
{
    /**
     * @var Graph $graph
     */
    private $graph;

    /**
     * @return array
     * @throws Exception
     */
    public function doSearch()
    {
        $arVertexQueue = array();
        $this->graph = GraphConsoleReader::readGraph();

        $rootVertex = $this->graph->getVertex(0);
        $rootVertex->addDataToVertex('distance', 0);
        $rootVertex->addTagToVertex('red');
        array_push($arVertexQueue, $this->graph->getVertex(0));
        /**
         * @var VertexWithEdges $vertex
         */
        while ($vertex = array_shift($arVertexQueue)) {
            $iDistance = $vertex->getData('distance');
            $vertex->addTagToVertex('red');
            $arAdjIx = $vertex->getAdjecentIndexes();
            foreach ($arAdjIx as $AdjIx) {
                $adjVertex = $this->graph->getVertex($AdjIx);
                if (!$adjVertex->isHasTag('red')) {
                    $adjVertex->addDataToVertex('distance', $iDistance + 1);
                    $adjVertex->addTagToVertex('red');
                    array_push($arVertexQueue, $adjVertex);
                }
            }
        }
        $arResultDistances = array();
        $arAllV = $this->graph->getVerticesItemIndexes();
        foreach($arAllV as $iVertex){
            $vertex = $this->graph->getVertex($iVertex);
            $arResultDistances[] = $vertex->getData('distance');
        }
        return $arResultDistances;
    }

}

/**
 * Class VertexWithEdges
 */
class VertexWithEdges
{
    private $arAdjacentVerticesIndex;
    private $arTags;
    private $arData;

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

    /**
     * @param $sTag
     */
    public function addDataToVertex($sDataName, $dataValue)
    {
        $this->arData[$sDataName] = $dataValue;
    }

    /**
     * @param $sTag
     * @return bool
     */
    public function getData($sDataName)
    {
        if (isset($this->arData[$sDataName])) {
            return $this->arData[$sDataName];
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
        for ($i = 0; $i < $nV; $i++) {
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
        $fh = fopen('WideSearch.txt', 'r') or die($php_errormsg);
//        $fh = fopen('php://stdin', 'r') or die($php_errormsg);
        $sFirstLine = fgets($fh);
        $sFirstLine = trim(preg_replace('/\s\s+/', '', $sFirstLine));
        $arFirstLine = explode(' ', $sFirstLine);
        if (count($arFirstLine) != 2) {
            throw new Exception("Invalid format of first line: needed number_of_vertex number_of_edges");
        }
        $nVertex = $arFirstLine[0];
        $nEdges = $arFirstLine[1];
        $graph = new Graph($nVertex);
        for ($i = 0; $i < $nEdges; $i++) {
            $sLine = fgets($fh);
            $sLine = trim(preg_replace('/\s\s+/', '', $sLine));
            $arLine = explode(' ', $sLine);
            if (count($arLine) != 2) {
                throw new Exception("Invalid format of line with edge: needed index_of_first_vertex index_of_second_vertex");
            }
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

$ds = new WideSearchForVerticesDistance();
echo implode(' ', $ds->doSearch());