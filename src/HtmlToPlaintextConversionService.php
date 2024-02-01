<?php

/*
 * Copyright (c) 2005-2007 Jon Abernathy <jon@chuggnutt.com>
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

namespace ThreeHeartsDigital;

class HtmlToPlaintextConversionService
{
    private string $html;
    private string $text;

    /**
     * List of preg* regular expression patterns to search for,
     * used in conjunction with $replace.
     *
     * @see $replace
     */
    private array $search = [
        "/\r/",                                           // Non-legal carriage return
        "/[\n\t]+/",                                      // Newlines and tabs
        '/<head\b[^>]*>.*?<\/head>/i',                    // <head>
        '/<script\b[^>]*>.*?<\/script>/i',                // <script>s -- which strip_tags supposedly has problems with
        '/<style\b[^>]*>.*?<\/style>/i',                  // <style>s -- which strip_tags supposedly has problems with
        '/<i\b[^>]*>(.*?)<\/i>/i',                        // <i>
        '/<em\b[^>]*>(.*?)<\/em>/i',                      // <em>
        '/<ins\b[^>]*>(.*?)<\/ins>/i',                    // <ins>
        '/(<ul\b[^>]*>|<\/ul>)/i',                        // <ul> and </ul>
        '/(<ol\b[^>]*>|<\/ol>)/i',                        // <ol> and </ol>
        '/(<dl\b[^>]*>|<\/dl>)/i',                        // <dl> and </dl>
        '/<li\b[^>]*>(.*?)<\/li>/i',                      // <li> and </li>
        '/<dd\b[^>]*>(.*?)<\/dd>/i',                      // <dd> and </dd>
        '/<dt\b[^>]*>(.*?)<\/dt>/i',                      // <dt> and </dt>
        '/<li\b[^>]*>/i',                                 // <li>
        '/<hr\b[^>]*>/i',                                 // <hr>
        '/<div\b[^>]*>/i',                                // <div>
        '/(<table\b[^>]*>|<\/table>)/i',                  // <table> and </table>
        '/(<tr\b[^>]*>|<\/tr>)/i',                        // <tr> and </tr>
        '/<td\b[^>]*>(.*?)<\/td>/i',                      // <td> and </td>
        '/<(img)\b[^>]*alt=\"([^>"]+)\"[^>]*>/i',         // <img> with alt tag
    ];

    /**
     * List of pattern replacements corresponding to patterns searched.
     *
     * @see $search
     */
    private array $replace = [
        '',                              // Non-legal carriage return
        ' ',                             // Newlines and tabs
        '',                              // <head>
        '',                              // <script>s -- which strip_tags supposedly has problems with
        '',                              // <style>s -- which strip_tags supposedly has problems with
        '_\\1_',                         // <i>
        '_\\1_',                         // <em>
        '_\\1_',                         // <ins>
        "\n\n",                          // <ul> and </ul>
        "\n\n",                          // <ol> and </ol>
        "\n\n",                          // <dl> and </dl>
        "\t* \\1\n",                     // <li> and </li>
        " \\1\n",                        // <dd> and </dd>
        "\t* \\1",                       // <dt> and </dt>
        "\n\t* ",                        // <li>
        "\n-------------------------\n", // <hr>
        "<div>\n",                       // <div>
        "\n\n",                          // <table> and </table>
        "\n",                            // <tr> and </tr>
        "\t\t\\1\n",                     // <td> and </td>
        '[\\2]',                         // <img> with alt tag
    ];

    /**
     * List of preg* regular expression patterns to search for,
     * used in conjunction with $entReplace.
     *
     * @see $entReplace
     */
    private array $entSearch = [
        '/&#153;/i',      // TM symbol in win-1252
        '/&#151;/i',      // m-dash in win-1252
        '/&(amp|#38);/i', // Ampersand: see converter()
        '/[ ]{2,}/',      // Runs of spaces, post-handling
        '/&#39;/i',       // The apostrophe symbol
    ];

    /**
     * List of pattern replacements corresponding to patterns searched.
     *
     * @see $entSearch
     */
    private array $entReplace = [
        '™',         // TM symbol
        '—',         // m-dash
        '|+|amp|+|', // Ampersand: see converter()
        ' ',         // Runs of spaces, post-handling
        '\'',        // Apostrophe
    ];

    /**
     * List of preg* regular expression patterns to search for
     * and replace using callback function.
     */
    private array $callbackSearch = [
        '/<(h)[123456]( [^>]*)?>(.*?)<\/h[123456]>/i',           // h1 - h6
        '/[ ]*<(p)( [^>]*)?>(.*?)<\/p>[ ]*/si',                  // <p> with surrounding whitespace.
        '/<(br)[^>]*>[ ]*/i',                                    // <br> with leading whitespace after the newline.
        '/<(b)( [^>]*)?>(.*?)<\/b>/i',                           // <b>
        '/<(strong)( [^>]*)?>(.*?)<\/strong>/i',                 // <strong>
        '/<(del)( [^>]*)?>(.*?)<\/del>/i',                       // <del>
        '/<(th)( [^>]*)?>(.*?)<\/th>/i',                         // <th> and </th>
        '/<(a) [^>]*href=("|\')([^"\']+)\2([^>]*)>(.*?)<\/a>/i', // <a href="">
    ];

    /**
     * List of preg* regular expression patterns to search for in PRE body,
     * used in conjunction with $preReplace.
     *
     * @see $preReplace
     */
    private array $preSearch = [
        "/\n/",
        "/\t/",
        '/ /',
        '/<pre[^>]*>/',
        '/<\/pre>/',
    ];

    /**
     * List of pattern replacements corresponding to patterns searched for PRE body.
     *
     * @see $preSearch
     */
    private array $preReplace = [
        '<br>',
        '&nbsp;&nbsp;&nbsp;&nbsp;',
        '&nbsp;',
        '',
        '',
    ];

    /**
     * Temporary workspace used during PRE processing.
     */
    private string $preContent = '';

    /**
     * Indicates whether content in the $html variable has been converted yet.
     *
     * @see $html, $text
     */
    private bool $converted = false;

    /**
     * Contains URL addresses from links to be rendered in plain text.
     *
     * @see convertLinkElements()
     */
    private array $linkList = [];

    /**
     * Maximum width of the formatted text, in columns.
     */
    private int $textWidth = 70;
    private bool $showLinks = true;

    public function hideLinks(): self
    {
        $this->showLinks = false;
        $this->converted = false;

        return $this;
    }

    public function showLinks(): self
    {
        $this->showLinks = true;
        $this->converted = false;

        return $this;
    }

    public function setHtml(?string $html): self
    {
        $this->html = $html ?? '';
        $this->converted = false;

        return $this;
    }

    public function getPlainText(): string
    {
        if (!$this->converted) {
            $this->convert();
        }

        return $this->text;
    }

    private function convert(): void
    {
        $origEncoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');

        $this->linkList = [];

        $text = trim($this->html);

        $text = $this->converter($text);

        if ($this->linkList) {
            $linkText = "\n\nLinks:\n------\n";

            foreach ($this->linkList as $i => $url) {
                $linkText .= sprintf(
                    "[%d] %s\n",
                    $i + 1,
                    html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8')
                );
            }

            $text .= $linkText;
        }

        $this->text = $text;
        $this->converted = true;

        mb_internal_encoding($origEncoding);
    }

    private function converter(string $text): string
    {
        $text = $this->convertBlockquoteElements($text);
        $text = $this->convertPreformattedTextElements($text);
        $text = preg_replace($this->search, $this->replace, $text);
        $text = preg_replace_callback($this->callbackSearch, [$this, 'pregCallback'], $text);
        $text = strip_tags($text);
        $text = preg_replace($this->entSearch, $this->entReplace, $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove unknown/unhandled entities (this cannot be done in search-and-replace block)
        $text = preg_replace('/&([a-zA-Z0-9]{2,6}|#[0-9]{2,4});/', '', $text);

        // Convert "|+|amp|+|" into "&", need to be done after handling of unknown entities
        // This properly handles situation of "&amp;quot;" in input string
        $text = str_replace('|+|amp|+|', '&', $text);

        // Normalise empty lines
        $text = preg_replace("/\n\s+\n/", "\n\n", $text);
        $text = preg_replace("/[\n]{3,}/", "\n\n", $text);

        // Remove leading empty lines (can be produced by e.g. P tag on the beginning)
        $text = ltrim($text, "\n");
        $text = wordwrap($text, $this->textWidth);

        return $text;
    }

    /**
     * Converts a link element for display.
     *
     * Maintains an internal array of links to be displayed at the end of the
     * text, with numeric indices to the original point in the text they
     * appeared.
     */
    private function convertLinkElements(string $link, string $display): string
    {
        if (!$this->showLinks) {
            return $display;
        }

        // We don't track non-http links.
        if (preg_match('!^(javascript:|mailto:|#)!i', html_entity_decode($link))) {
            return $display;
        }

        if (($index = array_search($link, $this->linkList)) === false) {
            $index = count($this->linkList);
            $this->linkList[] = $link;
        }

        return $display . ' [' . ($index + 1) . ']';
    }

    private function convertPreformattedTextElements(string $text): string
    {
        // get the content of PRE element
        while (preg_match('/<pre[^>]*>(.*)<\/pre>/ismU', $text, $matches)) {
            // Replace br tags with newlines to prevent the search-and-replace callback from killing whitespace
            $this->preContent = preg_replace('/(<br\b[^>]*>)/i', "\n", $matches[1]);

            // Run our defined tags search-and-replace with callback
            $this->preContent = preg_replace_callback(
                $this->callbackSearch,
                [$this, 'pregCallback'],
                $this->preContent
            );

            // convert the content
            $this->preContent = sprintf(
                '<div><br>%s<br></div>',
                preg_replace($this->preSearch, $this->preReplace, $this->preContent)
            );

            // replace the content (use callback because content can contain $0 variable)
            $text = preg_replace_callback(
                '/<pre[^>]*>.*<\/pre>/ismU',
                [$this, 'pregPreCallback'],
                $text,
                1
            );

            // free memory
            $this->preContent = '';
        }

        return $text;
    }

    private function convertBlockquoteElements(string $text): string
    {
        if (preg_match_all('/<\/*blockquote[^>]*>/i', $text, $matches, PREG_OFFSET_CAPTURE)) {
            $originalText = $text;
            $start = 0;
            $taglen = 0;
            $level = 0;
            $diff = 0;
            foreach ($matches[0] as $m) {
                $m[1] = mb_strlen(substr($originalText, 0, $m[1]));
                if ($m[0][0] == '<' && $m[0][1] == '/') {
                    --$level;

                    if ($level < 0) {
                        $level = 0; // malformed HTML: go to next blockquote
                    } elseif ($level > 0) {
                        // skip inner blockquote
                    } else {
                        $end = $m[1];
                        $len = $end - $taglen - $start;
                        // Get blockquote content
                        $body = mb_substr($text, $start + $taglen - $diff, $len);

                        // Set text width
                        $pWidth = $this->textWidth;

                        $this->textWidth -= 2;

                        // Convert blockquote content
                        $body = trim($body);
                        $body = $this->converter($body);
                        // Add citation markers and create PRE block
                        $body = preg_replace('/((^|\n)>*)/', '\\1> ', trim($body));
                        $body = '<pre>' . htmlspecialchars($body, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</pre>';
                        // Re-set text width
                        $this->textWidth = $pWidth;
                        // Replace content
                        $text = mb_substr($text, 0, $start - $diff)
                            . $body
                            . mb_substr($text, $end + mb_strlen($m[0]) - $diff);

                        $diff += $len + $taglen + mb_strlen($m[0]) - mb_strlen($body);
                        unset($body);
                    }
                } else {
                    if ($level == 0) {
                        $start = $m[1];
                        $taglen = mb_strlen($m[0]);
                    }

                    ++$level;
                }
            }
        }

        return $text;
    }

    /**
     * Callback function for preg_replace_callback use.
     */
    private function pregCallback(array $matches): string
    {
        switch (mb_strtolower($matches[1])) {
            case 'p':
                // Replace newlines with spaces.
                $para = str_replace("\n", ' ', $matches[3]);

                // Trim trailing and leading whitespace within the tag.
                $para = trim($para);

                // Add trailing newlines for this para.
                return "\n" . $para . "\n";
            case 'br':
                return "\n";
            case 'b':
            case 'strong':
                return $this->htmlContentToUpperCase($matches[3]);
            case 'del':
                return $this->strikeThrough($matches[3]);
            case 'th':
                return $this->htmlContentToUpperCase("\t\t" . $matches[3] . "\n");
            case 'h':
                return $this->htmlContentToUpperCase("\n\n" . $matches[3] . "\n\n");
            case 'a':
                // Remove spaces in URL
                $url = str_replace(' ', '', $matches[3]);

                return $this->convertLinkElements($url, $matches[5]);
        }

        return '';
    }

    /**
     * Callback function for preg_replace_callback use in PRE content handler.
     */
    private function pregPreCallback(/* @noinspection PhpUnusedParameterInspection */ array $matches): string
    {
        return $this->preContent;
    }

    private function htmlContentToUpperCase(string $html): string
    {
        $chunks = preg_split(
            '/(<[^>]*>)/',
            $html,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        // Only convert the text between HTML tags to uppercase.
        foreach ($chunks as $i => $chunk) {
            if ($chunk[0] != '<') {
                $chunk = html_entity_decode(
                    $chunk,
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                $chunk = mb_strtoupper($chunk);

                $chunks[$i] = htmlspecialchars(
                    $chunk,
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );
            }
        }

        return implode($chunks);
    }

    /**
     * Helper function for DEL conversion.
     */
    private function strikeThrough(string $str): string
    {
        $rtn = '';

        for ($i = 0; $i < mb_strlen($str); ++$i) {
            $chr = mb_substr($str, $i, 1);
            $combiningChr = chr(0xC0 | 0x336 >> 6) . chr(0x80 | 0x336 & 0x3F);
            $rtn .= $chr . $combiningChr;
        }

        return $rtn;
    }
}
