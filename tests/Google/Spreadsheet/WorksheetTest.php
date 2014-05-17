<?php
namespace Google\Spreadsheet;

use DateTime;
use SimpleXMLElement;

class WorksheetTest extends TestBase
{
    public function testGetId()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $worksheet = new Worksheet(new SimpleXMLElement($xml));

        $this->assertEquals('https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8', $worksheet->getId());
    }

    public function testGetUpdated()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $worksheet = new Worksheet(new SimpleXMLElement($xml));

        $this->assertTrue($worksheet->getUpdated() instanceof DateTime);
        $this->assertEquals('2013-02-10 21:12:33', $worksheet->getUpdated()->format('Y-m-d H:i:s'));
    }

    public function testGetTitle()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $worksheet = new Worksheet(new SimpleXMLElement($xml));

        $this->assertEquals('Test', $worksheet->getTitle());
    }

}