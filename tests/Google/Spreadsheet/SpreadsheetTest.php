<?php
namespace Google\Spreadsheet;

use DateTime;
use SimpleXMLElement;

class SpreadsheetTest extends TestBase
{
    public function testGetId()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet(new SimpleXMLElement($xml));

        $this->assertEquals($this->serviceUrl . 'tFEgU8ywJkkjcZjG', $spreadsheet->getId());
    }

    public function testGetUpdated()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet(new SimpleXMLElement($xml));

        $this->assertTrue($spreadsheet->getUpdated() instanceof DateTime);
        $this->assertEquals('2014-02-07 18:33:44', $spreadsheet->getUpdated()->format('Y-m-d H:i:s'));
    }

    public function testGetTitle()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet(new SimpleXMLElement($xml));

        $this->assertEquals('Test Spreadsheet', $spreadsheet->getTitle());
    }

    public function testGetWorksheets()
    {
        $this->setServiceRequest('worksheet-feed.xml');

        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet(new SimpleXMLElement($xml));

        $this->assertTrue($spreadsheet->getWorksheets() instanceof WorksheetFeed);
    }

    public function testAddWorksheet()
    {
        $this->setServiceRequest('worksheet.xml');

        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet(new SimpleXMLElement($xml));

        $this->assertTrue($spreadsheet->addWorksheet('Sheet 3') instanceof Worksheet);
    }

    public function testGetWorksheetsFeedUrl()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet(new SimpleXMLElement($xml));

        $this->assertEquals('https://spreadsheets.google.com/feeds/worksheets/tFEgU8ywJkkjcZjG/private/full', $spreadsheet->getWorksheetsFeedUrl());
    }
}