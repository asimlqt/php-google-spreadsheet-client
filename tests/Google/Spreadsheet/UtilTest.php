<?php

namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\Util;

class UtilTest extends TestBase
{
    public function testExtractEndpoint()
    {
        $url = "https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8";
        $expected = "/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8";
        $actual = Util::extractEndpoint($url);
        
        $this->assertEquals($expected, $actual);        
    }

    public function testGetLinkHref()
    {
        $xml = $this->getSimpleXMLElement("worksheet");
        $expected = "https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8";
        $actual = Util::getLinkHref($xml, "self");

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException Google\Spreadsheet\Exception\Exception
     */
    public function testGetLinkHrefException()
    {
        $xml = $this->getSimpleXMLElement("worksheet");
        $expected = "https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8";
        Util::getLinkHref($xml, "selfie");
    }
}