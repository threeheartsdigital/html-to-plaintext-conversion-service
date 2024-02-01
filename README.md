# Html to plaintext conversion service

A PHP library for converting HTML to formatted plain text.


## Basic Usage

```php
$html = new \Html2Text\HtmlToPlaintextConversionService('Hello, &quot;<b>world</b>&quot;');

echo $html->getText();  // Hello, "WORLD"
```

## History

This library started life on the blog of Jon Abernathy http://www.chuggnutt.com/html2text

A number of projects picked up the library and started using it - among those was RoundCube mail. They made a number of updates to it over time to suit their webmail client.

Now it has been extracted as a standalone library. Hopefully it can be of use to others.
