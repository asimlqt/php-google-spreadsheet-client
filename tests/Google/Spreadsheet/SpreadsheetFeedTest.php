<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\SpreadsheetFeed;
use Google\Spreadsheet\Spreadsheet;

class SpreadsheetFeedTest extends TestBase
{
    private $spreadsheetFeed;

    public function setUp()
    {
        $this->spreadsheetFeed = new SpreadsheetFeed(
            $this->getSimpleXMLElement("spreadsheet-feed")
        );
    }

    public function tearDown()
    {
        $this->spreadsheetFeed = null;
    }

    public function testGetXml()
    {
        $this->assertTrue(
            $this->spreadsheetFeed->getXml() instanceof \SimpleXMLElement
        );
    }

    public function testGetId()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/spreadsheets/private/full",
            $this->spreadsheetFeed->getId()
        );
    }

    public function testGetEntries()
    {
        $this->assertEquals(2, count($this->spreadsheetFeed->getEntries()));
    }

    public function testGetByTitle()
    {
        $this->assertTrue(
            $this->spreadsheetFeed->getByTitle("Test Spreadsheet") instanceof Spreadsheet
        );
    }

    /**
     * @expectedException Google\Spreadsheet\Exception\SpreadsheetNotFoundException
     */
    public function testGetByTitleException()
    {
        $this->assertNull(
            $this->spreadsheetFeed->getByTitle("No Spreadsheet")
        );
    }

}