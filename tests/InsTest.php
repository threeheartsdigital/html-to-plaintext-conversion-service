<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class InsTest extends TestCase
{
    public function testIns()
    {
        $html = 'This is <ins>inserted</ins>';
        $expected = 'This is _inserted_';

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
