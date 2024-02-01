<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class ListTest extends TestCase
{
    public function testList()
    {
        $html = <<< EOT
<ul>
  <li>Item 1</li>
  <li>Item 2</li>
  <li>Item 3</li>
</ul>
EOT;

        $expected = <<< EOT
 	* Item 1
 	* Item 2
 	* Item 3


EOT;

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }

    public function testOrderedList()
    {
        $html = <<< EOT
<ol>
  <li>Item 1</li>
  <li>Item 2</li>
  <li>Item 3</li>
</ol>
EOT;

        $expected = <<< EOT
 	* Item 1
 	* Item 2
 	* Item 3


EOT;

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
