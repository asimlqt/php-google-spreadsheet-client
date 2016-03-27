<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\Worksheet;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\ListFeed;
use Google\Spreadsheet\CellFeed;
use Google\Spreadsheet\DefaultServiceRequest;

class WorksheetTest extends TestBase
{
    private $worksheet;

    public function setUp()
    {
        $this->worksheet = new Worksheet(
            $this->getSimpleXMLElement("worksheet")
        );        
    }

    public function testGetId()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8",
            $this->worksheet->getId()
        );
    }

    public function testGetXml()
    {
        $this->assertTrue($this->worksheet->getXml() instanceof \SimpleXMLElement);
    }

    public function testGetGid()
    {
        $this->assertEquals(
            "0",
            $this->worksheet->getGid()
        );
    }

    public function testGetUpdated()
    {
        $worksheet = new Worksheet(
            $this->getSimpleXMLElement("worksheet")
        );

        $this->assertTrue($worksheet->getUpdated() instanceof \DateTime);
        $this->assertEquals("2013-02-10 21:12:33", $worksheet->getUpdated()->format("Y-m-d H:i:s"));
    }

    public function testGetTitle()
    {
        $worksheet = new Worksheet(
            $this->getSimpleXMLElement("worksheet")
        );

        $this->assertEquals("Test", $worksheet->getTitle());
    }

    public function testGetRowCount()
    {
        $this->assertEquals(100, $this->worksheet->getRowCount());
    }

    public function testGetColCount()
    {
        $this->assertEquals(10, $this->worksheet->getColCount());
    }

    public function testGetListFeed()
    {
        $feedUrl = "https://spreadsheets.google.com/feeds/list/tA3TdJ0RIVEem3xQZhG2Ceg/od8/private/full";
        
        $mockServiceRequest = $this->getMockBuilder(DefaultServiceRequest::class)
                ->setMethods(["get"])
                ->disableOriginalConstructor()
                ->getMock();
        
        $mockServiceRequest
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo($feedUrl))
            ->willReturn(file_get_contents(__DIR__."/xml/list-feed.xml"));
        
        ServiceRequestFactory::setInstance($mockServiceRequest);
        
        $worksheet = new Worksheet(
            $this->getSimpleXMLElement("worksheet")
        );

        $this->assertTrue($worksheet->getListFeed() instanceof ListFeed);
    }
    
    public function testGetListFeedWithQuery()
    {
        $feedUrl = "https://spreadsheets.google.com/feeds/list/tA3TdJ0RIVEem3xQZhG2Ceg/od8/private/full?reverse=true&sq=age+%3E+45";
        
        $mockServiceRequest = $this->getMockBuilder("Google\Spreadsheet\DefaultServiceRequest")
                ->setMethods(["get"])
                ->disableOriginalConstructor()
                ->getMock();
        
        $mockServiceRequest
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo($feedUrl))
            ->willReturn(file_get_contents(__DIR__."/xml/list-feed.xml"));
        
        ServiceRequestFactory::setInstance($mockServiceRequest);
        
        $listFeed = $this->worksheet->getListFeed(["reverse" => "true", "sq" => "age > 45"]);
        $this->assertTrue($listFeed instanceof ListFeed);
    }

    public function testGetCellFeed()
    {
        $feedUrl = "https://spreadsheets.google.com/feeds/cells/tA3TdJ0RIVEem3xQZhG2Ceg/od8/private/full";
        
        $mockServiceRequest = $this->getMockBuilder(DefaultServiceRequest::class)
                ->setMethods(["get"])
                ->disableOriginalConstructor()
                ->getMock();
        
        $mockServiceRequest
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo($feedUrl))
            ->willReturn(file_get_contents(__DIR__."/xml/cell-feed.xml"));
        
        ServiceRequestFactory::setInstance($mockServiceRequest);
        
        $this->assertTrue($this->worksheet->getCellFeed() instanceof CellFeed);
    }

    public function testGetCellFeedWithQuery()
    {
        $feedUrl = "https://spreadsheets.google.com/feeds/cells/tA3TdJ0RIVEem3xQZhG2Ceg/od8/private/full?sq=age+%3E+45";
        
        $mockServiceRequest = $this->getMockBuilder(DefaultServiceRequest::class)
                ->setMethods(["get"])
                ->disableOriginalConstructor()
                ->getMock();
        
        $mockServiceRequest
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo($feedUrl))
            ->willReturn(file_get_contents(__DIR__."/xml/cell-feed.xml"));
        
        ServiceRequestFactory::setInstance($mockServiceRequest);
        
        $this->assertTrue($this->worksheet->getCellFeed(["sq" => "age > 45"]) instanceof CellFeed);
    }

    public function testGetCsv()
    {
        $mockRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["get"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method("get")
            ->with($this->equalTo("https://docs.google.com/spreadsheets/d/15LRDjnN-VvhG2tYOoVLfCH40D6JoW5NfFiqSTc5U/export?gid=0&format=csv"));

        ServiceRequestFactory::setInstance($mockRequest);

        $this->worksheet->getCsv();
    }

    public function testUpdate()
    {
        $mockRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["put"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method("put")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8/0"),
                $this->logicalAnd(
                    $this->stringContains("<title>New WS</title>"),
                    $this->stringContains("<gs:rowcount>150</gs:rowcount>"),
                    $this->stringContains("<gs:colcount>7</gs:colcount>")
                )
            );

        ServiceRequestFactory::setInstance($mockRequest);

        $this->worksheet->update("New WS", 7, 150);
    }

    public function testDelete()
    {
        $mockRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["delete"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method("delete")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8/0")
            );

        ServiceRequestFactory::setInstance($mockRequest);

        $this->worksheet->delete();
    }

    public function testGetEditUrl()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/worksheets/tA3TdJ0RIVEem3xQZhG2Ceg/private/full/od8/0",
            $this->worksheet->getEditUrl()
        );
    }

    public function testGetListFeedUrl()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/list/tA3TdJ0RIVEem3xQZhG2Ceg/od8/private/full",
            $this->worksheet->getListFeedUrl()
        );
    }

    public function testGetCellFeedUrl()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/cells/tA3TdJ0RIVEem3xQZhG2Ceg/od8/private/full",
            $this->worksheet->getCellFeedUrl()
        );
    }

    public function testGetExportCsvUrl()
    {
        $this->assertEquals(
            "https://docs.google.com/spreadsheets/d/15LRDjnN-VvhG2tYOoVLfCH40D6JoW5NfFiqSTc5U/export?gid=0&format=csv",
            $this->worksheet->getExportCsvUrl()
        );
    }

}
