<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class BasicTest extends TestCase
{
    public static function basicDataProvider(): array
    {
        return [
            'Readme usage' => [
                'html' => 'Hello, &quot;<b>world</b>&quot;',
                'expected' => 'Hello, "WORLD"',
            ],
            'No stripslashes on HTML content' => [
                // HTML content does not escape slashes, therefore nor should we.
                'html' => 'Hello, \"<b>world</b>\"',
                'expected' => 'Hello, \"WORLD\"',
            ],
            'Zero is not empty' => [
                'html' => '0',
                'expected' => '0',
            ],
            'Paragraph with whitespace wrapping it' => [
                'html' => 'Foo <p>Bar</p> Baz',
                'expected' => "Foo\nBar\nBaz",
            ],
            'Paragraph text with linebreak flat' => [
                'html' => '<p>Foo<br/>Bar</p>',
                'expected' => <<<EOT
Foo
Bar

EOT
            ],
            'Paragraph text with linebreak formatted with newline' => [
                'html' => <<<EOT
<p>
    Foo<br/>
    Bar
</p>
EOT
                ,
                'expected' => <<<EOT
Foo
Bar

EOT
            ],
            'Paragraph text with linebreak formatted whth newline, but without whitespace' => [
                'html' => <<<EOT
<p>Foo<br/>
Bar</p>
EOT
                ,
                'expected' => <<<EOT
Foo
Bar

EOT
            ],
            'Paragraph text with linebreak formatted with indentation' => [
                'html' => <<< EOT
<p>
    Foo<br/>Bar
</p>
EOT
                ,
                'expected' => <<< EOT
Foo
Bar

EOT
            ],
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic($html, $expected)
    {
        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
