<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class ImageTest extends TestCase
{
    public static function imageDataProvider(): array
    {
        return [
            'Without alt tag' => [
                'html' => '<img src="http://example.com/example.jpg">',
                'expected' => '',
            ],
            'Without alt tag, wrapped in text' => [
                'html' => 'xx<img src="http://example.com/example.jpg">xx',
                'expected' => 'xxxx',
            ],
            'With alt tag' => [
                'html' => '<img src="http://example.com/example.jpg" alt="An example image">',
                'expected' => '[An example image]',
            ],
            'With alt, and title tags' => [
                'html' => '<img src="http://example.com/example.jpg" alt="An example image" title="Should be ignored">',
                'expected' => '[An example image]',
            ],
            'With alt tag, wrapped in text' => [
                'html' => 'xx<img src="http://example.com/example.jpg" alt="An example image">xx',
                'expected' => 'xx[An example image]xx',
            ],
            'With italics' => [
                'html' => '<img src="shrek.jpg" alt="the ogrelord" /> Blah <i>blah</i> blah',
                'expected' => '[the ogrelord] Blah _blah_ blah',
            ],
        ];
    }

    /**
     * @dataProvider imageDataProvider
     */
    public function testImages($html, $expected)
    {
        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
