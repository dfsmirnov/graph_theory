<?php

require_once './FindEulerCycle.php';

$graph = GraphConsoleReader::readGraph();

$fec = new FindEulerCycle();
$result = $fec->findIt($graph);
if ($result === false) {
    echo 'NONE';
} else {
    echo implode(' ', $result);
}
