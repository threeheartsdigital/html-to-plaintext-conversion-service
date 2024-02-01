<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class DefinitionListTest extends TestCase
{
    public function testDefinitionList()
    {
        $html = <<< EOT
<dl>
  <dt>Definition Term:</dt>
  <dd>Definition Description<dd>
</dl>
EOT;
        $expected = <<< EOT
 	* Definition Term: Definition Description 


EOT;

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
