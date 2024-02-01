<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class DelTest extends TestCase
{
    public function testDel()
    {
        $html = 'My <del>Résumé</del> Curriculum Vitæ';
        $expected = 'My R̶é̶s̶u̶m̶é̶ Curriculum Vitæ';

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
