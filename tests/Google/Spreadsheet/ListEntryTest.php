<?php
namespace Google\Spreadsheet;

use PHPUnit_Framework_TestCase;

class ListEntryTest extends PHPUnit_Framework_TestCase
{
    
    public function testGetEditUrl()
    {
        $xml = file_get_contents(__DIR__.'/xml/list-feed.xml');
        $listFeed = new ListFeed($xml);
        $listEntry = current($listFeed->getEntries());
        
        $this->assertEquals(
            'https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full/cokwr/bnkj8i7jo6c',
            $listEntry->getEditUrl()
        );
    }
    
    public function testUpdate()
    {
        $mockServiceRequest = $this->getMockBuilder('Google\Spreadsheet\DefaultServiceRequest')
                ->setMethods(array("put"))
                ->disableOriginalConstructor()
                ->getMock();
        
        $mockServiceRequest->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full/cokwr/bnkj8i7jo6c'),
                $this->stringContains('<gsx:nname><![CDATA[Asim]]></gsx:nname>')
            );
        
        ServiceRequestFactory::setInstance($mockServiceRequest);
        
        $listFeed = new ListFeed(file_get_contents(__DIR__.'/xml/list-feed.xml'));
        $entry = current($listFeed->getEntries());
        $data = $entry->getValues();
        $data["nname"] = "Asim";
        $entry->update($data);
    }

}