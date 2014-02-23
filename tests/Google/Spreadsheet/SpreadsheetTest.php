<?php
namespace Google\Spreadsheet;

use PHPUnit_Framework_TestCase;
use DateTime;

class SpreadsheetTest extends PHPUnit_Framework_TestCase
{
    public function testGetId()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet($xml);

        $this->assertEquals('tFEgU8ywJkkjcZjG', $spreadsheet->getId());
    }

    public function testGetUpdated()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet($xml);

        $this->assertTrue($spreadsheet->getUpdated() instanceof DateTime);
        $this->assertEquals('2014-02-07 18:33:44', $spreadsheet->getUpdated()->format('Y-m-d H:i:s'));
    }

    public function testGetTitle()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet($xml);

        $this->assertEquals('Test Spreadsheet', $spreadsheet->getTitle());
    }

    public function testGetWorksheets()
    {
        $serviceRequest = new TestServiceRequest(new Request(''));
        $serviceRequest->setExecuteReturn(file_get_contents(__DIR__.'/xml/worksheet-feed.xml'));
        ServiceRequestFactory::setInstance($serviceRequest);

        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet($xml);

        $this->assertTrue($spreadsheet->getWorksheets() instanceof WorksheetFeed);
    }

    public function testAddWorksheet()
    {
        $serviceRequest = new TestServiceRequest(new Request(''));
        $serviceRequest->setExecuteReturn(file_get_contents(__DIR__.'/xml/worksheet.xml'));
        ServiceRequestFactory::setInstance($serviceRequest);

        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet($xml);

        $this->assertTrue($spreadsheet->addWorksheet('Sheet 3') instanceof Worksheet);
    }

    public function testGetWorksheetsFeedUrl()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet.xml');
        $spreadsheet = new Spreadsheet($xml);

        $this->assertEquals('https://spreadsheets.google.com/feeds/worksheets/tFEgU8ywJkkjcZjG/private/full', $spreadsheet->getWorksheetsFeedUrl());
    }
}