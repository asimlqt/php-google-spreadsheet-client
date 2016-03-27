<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\ListFeed;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\DefaultServiceRequest;

class ListEntryTest extends TestBase
{

    public function testGetXml()
    {
        $listFeed = new ListFeed($this->getSimpleXMLElement("list-feed"));
        $entry = current($listFeed->getEntries());
        
        $this->assertTrue($entry->getXml() instanceof \SimpleXMLElement);
    }
    
    public function testGetEditUrl()
    {
        $listFeed = new ListFeed($this->getSimpleXMLElement("list-feed"));
        $listEntry = current($listFeed->getEntries());
        
        $this->assertEquals(
            "https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full/cokwr/bnkj8i7jo6c",
            $listEntry->getEditUrl()
        );
    }
    
    public function testUpdate()
    {
        $mockServiceRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["put"])
            ->disableOriginalConstructor()
            ->getMock();
        
        $mockServiceRequest->expects($this->once())
            ->method("put")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full/cokwr/bnkj8i7jo6c"),
                $this->stringContains("<gsx:nname>Asim</gsx:nname>")
            );
        
        ServiceRequestFactory::setInstance($mockServiceRequest);
        
        $listFeed = new ListFeed($this->getSimpleXMLElement("list-feed"));
        $entry = current($listFeed->getEntries());
        $data = $entry->getValues();
        $data["nname"] = "Asim";
        $entry->update($data);
    }

    public function testDelete()
    {
        $mockServiceRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["delete"])
            ->disableOriginalConstructor()
            ->getMock();

        $mockServiceRequest->expects($this->once())
            ->method("delete")
            ->with(
                $this->equalTo("https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full/cokwr/bnkj8i7jo6c")
            );

        ServiceRequestFactory::setInstance($mockServiceRequest);

        $listFeed = new ListFeed($this->getSimpleXMLElement("list-feed"));
        $entry = current($listFeed->getEntries());
        $entry->delete();
    }

}