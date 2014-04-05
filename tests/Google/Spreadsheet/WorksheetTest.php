<?php
namespace Google\Spreadsheet;

use DateTime;
use SimpleXMLElement;
use ReflectionMethod;

class WorksheetTest extends TestBase
{
    public function testGetXml()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $worksheet = new Worksheet($xml);

        $this->assertTrue($worksheet->getXml() instanceof SimpleXMLElement);
    }

    public function testGetId()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $worksheet = new Worksheet($xml);

        $this->assertEquals('https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8', $worksheet->getId());
    }

    public function testGetWorksheetId()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $worksheet = new Worksheet($xml);

        $this->assertEquals('tA3TdJ0RIVEem3xQZhG2Ceg', $worksheet->getWorksheetId());
    }

    /**
     * @expectedException Exception
     */
    public function testGetWorksheetIdException()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $xml = preg_replace('@<id>.*</id>@', '<id></id>', $xml);
        $worksheet = new Worksheet($xml);

        $worksheet->getWorksheetId();
    }

    // public function testGetCellEditUrl()
    // {
    //     $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');

    //     $method = new ReflectionMethod('Google\\Spreadsheet\\Worksheet', 'getCellEditUrl');
    //     $method->setAccessible(true);
         
    //     echo $method->invoke(new Worksheet($xml));exit;
    // }

    public function testGetUpdated()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $worksheet = new Worksheet($xml);

        $this->assertTrue($worksheet->getUpdated() instanceof DateTime);
        $this->assertEquals('2013-02-10 21:12:33', $worksheet->getUpdated()->format('Y-m-d H:i:s'));
    }

    public function testGetTitle()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet.xml');
        $worksheet = new Worksheet($xml);

        $this->assertEquals('Test', $worksheet->getTitle());
    }

}