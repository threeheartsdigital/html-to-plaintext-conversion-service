<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class TableTest extends TestCase
{
    public function testTable()
    {
        $html = <<< EOT
<table>
  <tr>
    <th>Heading 1</th>
    <td>Data 1</td>
  </tr>
  <tr>
    <th>Heading 2</th>
    <td>Data 2</td>
  </tr>
</table>
EOT;

        $expected = <<< EOT
 		HEADING 1
 		Data 1

 		HEADING 2
 		Data 2


EOT;

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
