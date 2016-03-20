<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\WorksheetFeed;
use Google\Spreadsheet\Worksheet;

class WorksheetFeedTest extends TestBase
{
    public function testGetByTitle()
    {
        $worksheetFeed = new WorksheetFeed(
            $this->getSimpleXMLElement("worksheet-feed")
        );

        $this->assertTrue($worksheetFeed->getByTitle("Sheet1") instanceof Worksheet);
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
