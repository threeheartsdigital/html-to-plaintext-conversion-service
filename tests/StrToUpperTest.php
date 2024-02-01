<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class StrToUpperTest extends TestCase
{
    public function testToUpper()
    {
        $html = <<< EOT
<h1>Will be UTF-8 (äöüèéилčλ) uppercased</h1>
<p>Will remain lowercased</p>
EOT;
        $expected = <<< EOT
WILL BE UTF-8 (ÄÖÜÈÉИЛČΛ) UPPERCASED

Will remain lowercased

EOT;

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
