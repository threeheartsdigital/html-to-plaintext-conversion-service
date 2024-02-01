<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class PreTest extends TestCase
{
    public static function preDataProvider(): array
    {
        return [
            'Basic pre' => [
                'html' => <<<EOT
<p>Before</p>
<pre>

Foo bar baz


HTML symbols &amp;

</pre>
<p>After</p>
EOT
                ,
                'expected' => <<<EOT
Before

Foo bar baz

HTML symbols &

After

EOT
                ,
            ],
            'br in pre' => [
                'html' => <<<EOT
<pre>
some<br />  indented<br />  text<br />    on<br />    several<br />  lines<br />
</pre>
EOT
                ,
                'expected' => <<<EOT
some
  indented
  text
    on
    several
  lines


EOT
                ,
            ],
        ];
    }

    /**
     * @dataProvider preDataProvider
     */
    public function testPre($html, $expected)
    {
        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
