<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\CellFeed;
use Google\Spreadsheet\CellEntry;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\Batch\BatchRequest;
use Google\Spreadsheet\Batch\BatchResponse;

class CellFeedTest extends TestBase
{
    public function testGetXml()
    {
        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));
        $this->assertTrue($feed->getXml() instanceof \SimpleXMLElement);
    }

    public function testGetId()
    {
        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/cells/15L06yklgflGRDjnN-VvhGYOoVLCH40DJoW5fFiqSTc5U/od6/private/full",
            $feed->getId()
        );
    }

    public function testGetEntries()
    {
        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));
        $this->assertEquals(6, count($feed->getEntries()));
        // The same call needs to be made again to test returning the
        // cached version of the entries. required for 100% coverage
        $this->assertEquals(6, count($feed->getEntries()));
    }

    public function testToArray()
    {
        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));
        
        $expected = [
            1 => [
                1 => "Name",
                2 => "Age"
            ],
            2 => [
                1 => "Asim",
                2 => "99"
            ],
            3 => [
                1 => "Other",
                2 => "18"
            ],
        ];

        $this->assertEquals($expected, $feed->toArray());
    }

    public function testGetCell()
    {
        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));

        $this->assertTrue($feed->getCell(1, 1) instanceof CellEntry);
        $this->assertNull($feed->getCell(5, 3));
    }

    public function testEditCell()
    {
        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));

        $mockServiceRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(array("post"))
            ->disableOriginalConstructor()
            ->getMock();
        
        $mockServiceRequest->expects($this->once())
            ->method("post")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/feeds/cells/15L06yklgflGRDjnN-VvhGYOoVLCH40DJoW5fFiqSTc5U/od6/private/full"),
                $this->stringContains("<entry")
            );
        
        ServiceRequestFactory::setInstance($mockServiceRequest);

        $feed->editCell(2, 1, "Test");
    }

    public function testUpdateBatch()
    {
        $mockBatchRequest = $this->getMockBuilder(BatchRequest::class)
            ->setMethods(["createRequestXml"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockBatchRequest->expects($this->once())
            ->method("createRequestXml")
            ->will($this->returnValue("<batch/>"));

        $mockCellFeed = $this->getMockBuilder(CellFeed::class)
            ->setMethods(["getBatchUrl"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockCellFeed->expects($this->once())
            ->method("getBatchUrl")
            ->will($this->returnValue("https://spreadsheets.google.com/"));

        $mockServiceRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(array("post"))
            ->disableOriginalConstructor()
            ->getMock();
        $mockServiceRequest->expects($this->once())
            ->method("post")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/"),
                $this->stringContains("<batch/>")
            )
            ->will($this->returnValue("<response/>"));
        ServiceRequestFactory::setInstance($mockServiceRequest);

        $this->assertTrue($mockCellFeed->updateBatch($mockBatchRequest) instanceof BatchResponse);
    }

    public function testInsertBatch()
    {
        $mockBatchRequest = $this->getMockBuilder(BatchRequest::class)
            ->setMethods(["createRequestXml"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockBatchRequest->expects($this->once())
            ->method("createRequestXml")
            ->will($this->returnValue(new \SimpleXMLElement("<batch/>")));

        $mockCellFeed = $this->getMockBuilder(CellFeed::class)
            ->setMethods(["getBatchUrl"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockCellFeed->expects($this->once())
            ->method("getBatchUrl")
            ->will($this->returnValue("https://spreadsheets.google.com/"));

        $mockServiceRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(array("post"))
            ->disableOriginalConstructor()
            ->getMock();
        $mockServiceRequest->expects($this->once())
            ->method("post")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/"),
                $this->stringContains("<batch/>")
            )
            ->will($this->returnValue("<response/>"));
        ServiceRequestFactory::setInstance($mockServiceRequest);

        $this->assertTrue($mockCellFeed->insertBatch($mockBatchRequest) instanceof BatchResponse);
    }

    public function testGetPostUrl()
    {
        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/cells/15L06yklgflGRDjnN-VvhGYOoVLCH40DJoW5fFiqSTc5U/od6/private/full",
            $feed->getPostUrl()
        );
    }

    public function testGetBatchUrl()
    {
        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/cells/15L06yklgflGRDjnN-VvhGYOoVLCH40DJoW5fFiqSTc5U/od6/private/full/batch",
            $feed->getBatchUrl()
        );
    }

    public function testCreateCell()
    {
        $mockCellFeed = $this->getMockBuilder(CellFeed::class)
            ->setMethods(["getPostUrl"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockCellFeed->expects($this->any())
            ->method("getPostUrl")
            ->will($this->returnValue("https://spreadsheets.google.com/"));

        $actual = $mockCellFeed->createCell(2, 1, "Someone");
        $this->assertTrue($actual instanceof CellEntry);
    }

    public function testCreateCellWithAmpersand()
    {
        $mockCellFeed = $this->getMockBuilder(CellFeed::class)
            ->setMethods(["getPostUrl"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockCellFeed->expects($this->any())
            ->method("getPostUrl")
            ->will($this->returnValue("https://spreadsheets.google.com/"));

        $actual = $mockCellFeed->createCell(2, 1, "a &  b < c");

        $this->assertTrue($actual instanceof CellEntry);

        $expectedXML = file_get_contents(__DIR__."/xml/cell-feed-with-ampersand.xml");
        $actualXML = $actual->getXML()->asXML();
        $this->assertXmlStringEqualsXmlString($actualXML,$expectedXML);
    }

}