<?php

use PHPUnit\Framework\TestCase;

require_once './FindEulerCycle.php';

class FindEulerCycleTest extends TestCase
{
    public function testIt()
    {
        $i = 1;
        $fec = new FindEulerCycle();

        while (1) {
            $sTestFileName = __DIR__ . '/test-' . $i . '.txt';
            if (!file_exists($sTestFileName)) {
                break;
            }
            $sTestContent = file_get_contents($sTestFileName);
            $arTestContent = preg_split("/[\r\n]+=[\r\n]+/", $sTestContent);
            $this->assertCount(2, $arTestContent, "Неправильный формат теста!");

            $graph = GraphConsoleReader::readGraph('data://text/plain,' . $arTestContent[0]);

            $result = $fec->findIt($graph);
            if ($result === false) {
                $sResult = 'NONE';
            } else {
                $sResult = implode(' ', $result);
            }
            $this->assertEquals($arTestContent[1], $sResult, 'Неправильный ответ для теста №' . $i . " ({$sResult})");


            $i++;
        }
    }
}