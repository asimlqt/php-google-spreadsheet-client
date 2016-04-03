<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\ListFeed;
use Google\Spreadsheet\ServiceRequestFactory;

class ListFeedTest extends TestBase
{
    private $listFeed;

    public function setUp()
    {
        $this->listFeed = new ListFeed($this->getSimpleXMLElement("list-feed"));
    }

    public function tearDown()
    {
        $this->listFeed = null;
    }

    public function testGetXml()
    {
        $this->assertTrue($this->listFeed->getXml() instanceof \SimpleXMLElement);
    }

    public function testGetId()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full",
            $this->listFeed->getId()
        );
    }

    public function testGetPostUrl()
    {
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full",
            $this->listFeed->getPostUrl()
        );
    }
    
    public function testInsert()
    {
        $mockServiceRequest = $this->getMockBuilder("Google\Spreadsheet\DefaultServiceRequest")
                ->setMethods(array("post"))
                ->disableOriginalConstructor()
                ->getMock();
        
        $mockServiceRequest->expects($this->once())
            ->method("post")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full"),
                $this->stringContains("<gsx:occupation>software engineer</gsx:occupation>")
            );
        
        ServiceRequestFactory::setInstance($mockServiceRequest);
        
        $this->listFeed->insert(["name" => "asim", "occupation" => "software engineer"]);
    }
    
    public function testGetEntries()
    {
        $this->assertEquals(4, count($this->listFeed->getEntries()));
    }

    public function testGetTotalResults()
    {
        $this->assertEquals(4, $this->listFeed->getTotalResults());
    }

    public function testGetStartIndex()
    {
        $this->assertEquals(1, $this->listFeed->getStartIndex());
    }

}