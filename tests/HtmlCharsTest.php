<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class HtmlCharsTest extends TestCase
{
    public function testLaquoAndRaquo()
    {
        $html = 'This library name is &laquo;HtmlToPlaintextConversionService&raquo;';
        $expected = 'This library name is «HtmlToPlaintextConversionService»';

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }

    public static function provideSymbols(): array
    {
        // A variety of symbols that either used to have special handling
        // or still does.
        return [
            // Non-breaking space, not a regular one.
            ['&nbsp;', ' '],
            ['&gt;', '>'],
            ['&lt;', '<'],
            ['&copy;', '©'],
            ['&#169;', '©'],
            ['&trade;', '™'],
            // The TM symbol in Windows-1252, invalid in HTML...
            ['&#153;', '™'],
            // Correct TM symbol numeric code
            ['&#8482;', '™'],
            ['&reg;', '®'],
            ['&#174;', '®'],
            ['&mdash;', '—'],
            // The m-dash in Windows-1252, invalid in HTML...
            ['&#151;', '—'],
            // Correct m-dash numeric code
            ['&#8212;', '—'],
            ['&bull;', '•'],
            ['&pound;', '£'],
            ['&#163;', '£'],
            ['&euro;', '€'],
            ['&amp;', '&'],
        ];
    }

    /**
     * @dataProvider provideSymbols
     */
    public function testSymbol($entity, $symbol)
    {
        $html = "$entity signs should be UTF-8 symbols";
        $expected = "$symbol signs should be UTF-8 symbols";

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }

    public function testSingleQuote()
    {
        $html = 'Single quote&#039;s preservation';
        $expected = "Single quote's preservation";

        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
