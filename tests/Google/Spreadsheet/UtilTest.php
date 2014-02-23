<?php

namespace Google\Spreadsheet;

use PHPUnit_Framework_TestCase;
use SimpleXMLElement;

class UtilTest extends PHPUnit_Framework_TestCase
{
    public function testExtractEndpoint()
    {
        $url = 'https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8';
        $expected = '/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8';
        $actual = Util::extractEndpoint($url);
        
        $this->assertEquals($expected, $actual);        
    }

    public function testGetLinkHref()
    {
        $xml = new SimpleXMLElement(file_get_contents(__DIR__.'/xml/worksheet.xml'));
        $expected = 'https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8';
        $actual = Util::getLinkHref($xml, 'self');

        $this->assertEquals($expected, $actual);
    }
}