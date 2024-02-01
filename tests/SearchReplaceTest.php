<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class SearchReplaceTest extends TestCase
{
    public static function searchReplaceDataProvider(): array
    {
        return [
            'Bold' => [
                'html' => 'Hello, &quot;<b>world</b>&quot;!',
                'expected' => 'Hello, "WORLD"!',
            ],
            'Strong' => [
                'html' => 'Hello, &quot;<strong>world</strong>&quot;!',
                'expected' => 'Hello, "WORLD"!',
            ],
            'Italic' => [
                'html' => 'Hello, &quot;<i>world</i>&quot;!',
                'expected' => 'Hello, "_world_"!',
            ],
            'Header' => [
                'html' => '<h1>Hello, world!</h1>',
                'expected' => "HELLO, WORLD!\n\n",
            ],
            'Table Header' => [
                'html' => '<th>Hello, World!</th>',
                'expected' => "\t\tHELLO, WORLD!\n",
            ],
            'Apostrophe' => [
                'html' => 'I can&#39;t believe it&#39;s snowing again.',
                'expected' => "I can't believe it's snowing again.",
            ],
        ];
    }

    /**
     * @dataProvider searchReplaceDataProvider
     */
    public function testSearchReplace($html, $expected)
    {
        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
