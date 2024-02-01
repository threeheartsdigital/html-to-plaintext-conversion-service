<?php

namespace ThreeHeartsDigital\Tests;

use PHPUnit\Framework\TestCase;
use ThreeHeartsDigital\HtmlToPlaintextConversionService;

class BlockquoteTest extends TestCase
{
    public static function blockquoteDataProvider(): array
    {
        return [
            'Basic blockquote' => [
                'html' => <<<EOT
<p>Before</p>
<blockquote>

Foo bar baz


HTML symbols &amp;

</blockquote>
<p>After</p>
EOT
                ,
                'expected' => <<<EOT
Before

> Foo bar baz HTML symbols &

After

EOT
                ,
            ],
            'Multiple blockquotes in text' => [
                'html' => <<<EOF
<p>Highlights from today&rsquo;s <strong>Newlyhired Game</strong>:</p><blockquote><p><strong>Sean:</strong> What came first, Blake&rsquo;s first <em>Chief Architect position</em> or Blake&rsquo;s first <em>girlfriend</em>?</p> </blockquote> <blockquote> <p><strong>Sean:</strong> Devin, Bryan spent almost five years of his life slaving away for this vampire squid wrapped around the face of humanity&hellip;<br/><strong>Devin:</strong> Goldman Sachs?<br/><strong>Sean:</strong> Correct!</p> </blockquote> <blockquote> <p><strong>Sean:</strong> What was the name of the girl Zhu took to prom three months ago?<br/><strong>John:</strong> What?<br/><strong>Derek (from the audience):</strong> Destiny!<br/><strong>Zhu:</strong> Her name is Jolene. She&rsquo;s nice. I like her.</p></blockquote><p>I think the audience is winning.&nbsp; - Derek</p>
EOF
                ,
                'expected' => <<<EOF
Highlights from today’s NEWLYHIRED GAME:

> SEAN: What came first, Blake’s first _Chief Architect position_ or
> Blake’s first _girlfriend_?

> SEAN: Devin, Bryan spent almost five years of his life slaving away
> for this vampire squid wrapped around the face of humanity…
> DEVIN: Goldman Sachs?
> SEAN: Correct!

> SEAN: What was the name of the girl Zhu took to prom three months
> ago?
> JOHN: What?
> DEREK (FROM THE AUDIENCE): Destiny!
> ZHU: Her name is Jolene. She’s nice. I like her.

I think the audience is winning.  - Derek

EOF
            ],
            'Multibyte strings before blockquote' => [
                'html' => <<<EOF
“Hello”

<blockquote>goodbye</blockquote>

EOF
                ,
                'expected' => <<<EOF
“Hello” 

> goodbye

EOF
            ],
        ];
    }

    /**
     * @dataProvider blockquoteDataProvider
     */
    public function testBlockquote($html, $expected)
    {
        $service = new HtmlToPlaintextConversionService();
        $this->assertEquals($expected, $service->setHtml($html)->getPlainText());
    }
}
