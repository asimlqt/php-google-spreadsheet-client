<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\WorksheetFeed;
use Google\Spreadsheet\Worksheet;

class WorksheetFeedTest extends TestBase
{
    private $worksheetFeed;

    public function setUp()
    {
        $this->worksheetFeed = new WorksheetFeed(
            $this->getSimpleXMLElement("worksheet-feed")
        );        
    }

    public function testGetXml()
    {
        $this->assertTrue($this->worksheetFeed->getXml() instanceof \SimpleXMLElement);
    }

    public function testGetId()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/worksheets/tFEgU8ywJkkjcZjGsXV/private/full",
            $this->worksheetFeed->getId()
        );
    }

    public function testGetEntries()
    {
        $this->assertEquals(2, count($this->worksheetFeed->getEntries()));
    }

    public function testGetPostUrl()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/worksheets/tFEgU8ywJkkjcZjGsXV/private/full",
            $this->worksheetFeed->getPostUrl()
        );
    }

    public function testGetByTitle()
    {
        $this->assertTrue($this->worksheetFeed->getByTitle("Sheet1") instanceof Worksheet);
    }

    /**
     * @expectedException Google\Spreadsheet\Exception\WorksheetNotFoundException
     */
    public function testGetByTitleException()
    {
        $worksheetFeed = new WorksheetFeed(
            $this->getSimpleXMLElement("worksheet-feed")
        );

        $this->assertNull($worksheetFeed->getByTitle("Sheet3"));
    }

    public function testGetById()
    {
        $worksheetFeed = new WorksheetFeed(
            $this->getSimpleXMLElement("worksheet-feed")
        );

        $this->assertTrue($worksheetFeed->getById("od6") instanceof Worksheet);
    }

    /**
     * @expectedException Google\Spreadsheet\Exception\WorksheetNotFoundException
     */
    public function testGetByIdException()
    {
        $worksheetFeed = new WorksheetFeed(
            $this->getSimpleXMLElement("worksheet-feed")
        );

        $this->assertTrue(is_null($worksheetFeed->getById("od7")));
    }

}
