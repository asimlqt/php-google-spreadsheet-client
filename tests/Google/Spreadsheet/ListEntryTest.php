<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\ListFeed;
use Google\Spreadsheet\ServiceRequestFactory;

class ListEntryTest extends TestBase
{
    
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
        $mockServiceRequest = $this->getMockBuilder("Google\Spreadsheet\DefaultServiceRequest")
                ->setMethods(array("put"))
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

}