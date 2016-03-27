<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\ListFeed;
use Google\Spreadsheet\ServiceRequestFactory;

class ListFeedTest extends TestBase
{
    public function testGetXml()
    {
        $feed = new ListFeed($this->getSimpleXMLElement("list-feed"));
        $this->assertTrue($feed->getXml() instanceof \SimpleXMLElement);
    }

    public function testGetId()
    {
        $feed = new ListFeed($this->getSimpleXMLElement("list-feed"));
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full",
            $feed->getId()
        );
    }

    public function testGetPostUrl()
    {
        $listFeed = new ListFeed($this->getSimpleXMLElement("list-feed"));

        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full",
            $listFeed->getPostUrl()
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
        
        $listFeed = new ListFeed($this->getSimpleXMLElement("list-feed"));
        $listFeed->insert(["name" => "asim", "occupation" => "software engineer"]);
    }
    
    public function testGetEntries()
    {
        $listFeed = new ListFeed($this->getSimpleXMLElement("list-feed"));

        $this->assertEquals(4, count($listFeed->getEntries()));
    }

}