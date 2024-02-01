<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class LinkTest extends TestCase
{
    const HTML = <<< EOT
<a href="http://example.com">Example link without URL parameters</a>
<br>
<a href="http://example.com?param1=value1&param2=value2&param3=value3">Example link with URL parameters</a>
<br>
<a href="http://example.com?param1=value1&amp;param2=value2&amp;param3=value3">Example link with HTML encoded URL parameters</a>
EOT;

    public function testDoLinksAfter()
    {
        $expected = <<<EOT
Example link without URL parameters [1] 
Example link with URL parameters [2] 
Example link with HTML encoded URL parameters [3]

Links:
------
[1] http://example.com
[2] http://example.com?param1=value1&param2=value2&param3=value3
[3] http://example.com?param1=value1&param2=value2&param3=value3

EOT;

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml(self::HTML)->getPlainText());
    }

    public function testDoLinksNone()
    {
        $expected = <<<EOT
Example link without URL parameters 
Example link with URL parameters 
Example link with HTML encoded URL parameters
EOT;

        $service = new HtmlToPlaintextConversionService();
        $actual = $service->hideLinks()->setHtml(self::HTML)->getPlainText();
        $this->assertEquals($expected, $actual);
    }
}
