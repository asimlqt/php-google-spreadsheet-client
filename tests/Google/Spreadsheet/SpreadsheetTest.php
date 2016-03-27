<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\Spreadsheet;
use Google\Spreadsheet\WorksheetFeed;
use Google\Spreadsheet\Exception\WorksheetNotFoundException;
use Google\Spreadsheet\Worksheet;

class SpreadsheetTest extends TestBase
{
    public function testGetXml()
    {
        $spreadsheet = new Spreadsheet($this->getSimpleXMLElement("spreadsheet"));
        $this->assertTrue($spreadsheet->getXml() instanceof \SimpleXMLElement);
    }

    public function testGetId()
    {
        $spreadsheet = new Spreadsheet($this->getSimpleXMLElement("spreadsheet"));
        $this->assertEquals($this->serviceUrl . "tFEgU8ywJkkjcZjG", $spreadsheet->getId());
    }

    public function testGetUpdated()
    {
        $spreadsheet = new Spreadsheet($this->getSimpleXMLElement("spreadsheet"));

        $this->assertTrue($spreadsheet->getUpdated() instanceof \DateTime);
        $this->assertEquals(
            "2014-02-07 18:33:44",
            $spreadsheet->getUpdated()->format("Y-m-d H:i:s")
        );
    }

    public function testGetTitle()
    {
        $spreadsheet = new Spreadsheet($this->getSimpleXMLElement("spreadsheet"));
        $this->assertEquals("Test Spreadsheet", $spreadsheet->getTitle());
    }

    public function testGetWorksheetFeed()
    {
        $this->setServiceRequest("worksheet-feed.xml");
        $spreadsheet = new Spreadsheet($this->getSimpleXMLElement("spreadsheet"));

        $this->assertTrue($spreadsheet->getWorksheetFeed() instanceof WorksheetFeed);
    }

    public function testGetWorksheetByTitle()
    {
        $spreadsheetMock = $this->getMockBuilder(Spreadsheet::class)
                ->setMethods(["getWorksheetFeed"])
                ->disableOriginalConstructor()
                ->getMock();

        $spreadsheetMock->expects($this->any())
            ->method("getWorksheetFeed")
            ->will($this->returnValue(new WorksheetFeed(
                $this->getSimpleXMLElement("worksheet-feed")
            )));

        $this->assertTrue($spreadsheetMock->getWorksheetByTitle("Sheet2") instanceof Worksheet);
    }

    /**
     * @expectedException Google\Spreadsheet\Exception\WorksheetNotFoundException
     */
    public function testGetWorksheetByTitleNotFound()
    {
        $spreadsheetMock = $this->getMockBuilder(Spreadsheet::class)
                ->setMethods(["getWorksheetFeed"])
                ->disableOriginalConstructor()
                ->getMock();

        $spreadsheetMock->expects($this->any())
            ->method("getWorksheetFeed")
            ->will($this->returnValue(new WorksheetFeed(
                $this->getSimpleXMLElement("worksheet-feed")
            )));

        $this->assertTrue($spreadsheetMock->getWorksheetByTitle("Sheet10") instanceof Worksheet);
    }

    public function testAddWorksheet()
    {
        $this->setServiceRequest("worksheet.xml");
        $spreadsheet = new Spreadsheet($this->getSimpleXMLElement("spreadsheet"));

        $this->assertTrue($spreadsheet->addWorksheet("Sheet 3") instanceof Worksheet);
    }

    public function testGetWorksheetsFeedUrl()
    {
        $spreadsheet = new Spreadsheet($this->getSimpleXMLElement("spreadsheet"));
        $this->assertEquals("https://spreadsheets.google.com/feeds/worksheets/tFEgU8ywJkkjcZjG/private/full", $spreadsheet->getWorksheetsFeedUrl());
    }
}