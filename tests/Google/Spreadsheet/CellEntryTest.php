<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\CellFeed;
use Google\Spreadsheet\CellEntry;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\Batch\BatchRequest;
use Google\Spreadsheet\Batch\BatchResponse;

class CellEntryTest extends TestBase
{

    private $cellEntry;

    public function setUp()
    {
        $cellFeed = new CellFeed($this->getSimpleXMLElement("cell-feed"));
        $this->cellEntry = current($cellFeed->getEntries());
    }

    public function tearDown()
    {
        $this->cellEntry = null;
    }

    public function testGetCellIdString()
    {
        $this->assertEquals("R1C1", $this->cellEntry->getCellIdString());
    }

    public function testGetXml()
    {
        $this->assertTrue($this->cellEntry->getXml() instanceof \SimpleXMLElement);
    }

    public function testGetRow()
    {
        $this->assertEquals(1, $this->cellEntry->getRow());
    }

    public function testGetColumn()
    {
        $this->assertEquals(1, $this->cellEntry->getColumn());
    }

    public function testGetSetPostUrl()
    {
        $url = "http://google/";
        $this->cellEntry->setPostUrl($url);
        $this->assertEquals($url, $this->cellEntry->getPostUrl());
    }

    public function testGetInputValue()
    {
        $this->assertEquals("Name", $this->cellEntry->getInputValue());
    }

    public function testSetInputValue()
    {
        $this->cellEntry->setInputValue("Test");
        $this->assertEquals("Test", $this->cellEntry->getInputValue());
    }

    public function testGetTitle()
    {
        $this->assertEquals("A1", $this->cellEntry->getTitle());
    }

    public function testGetSetContent()
    {
        $this->assertEquals("Name", $this->cellEntry->getContent());
    }

    public function testUpdate()
    {
        $mockRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["post"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method("post")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/feeds/cells/15L06yklgflGRDjnN-VvhGYOoVLCH40DJoW5fFiqSTc5U/od6/private/full"),
                $this->stringContains("MyVal")
            )
            ->willReturn("<entry/>");

        ServiceRequestFactory::setInstance($mockRequest);

        $this->cellEntry->update("MyVal");
    }

    public function testGetEditUrl()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/cells/15L06yklgflGRDjnN-VvhGYOoVLCH40DJoW5fFiqSTc5U/od6/private/full/R1C1/1fvl7",
            $this->cellEntry->getEditUrl()
        );
    }
}