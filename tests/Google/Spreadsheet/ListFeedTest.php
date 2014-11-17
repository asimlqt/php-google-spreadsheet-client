<?php
namespace Google\Spreadsheet;

use PHPUnit_Framework_TestCase;

class ListFeedTest extends PHPUnit_Framework_TestCase
{
    
    public function testGetPostUrl()
    {
        $xml = file_get_contents(__DIR__.'/xml/list-feed.xml');
        $listFeed = new ListFeed($xml);

        $this->assertEquals(
            'https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full',
            $listFeed->getPostUrl()
        );
    }
    
    public function testInsert()
    {
        $mockServiceRequest = $this->getMockBuilder('Google\Spreadsheet\DefaultServiceRequest')
                ->setMethods(array("post"))
                ->disableOriginalConstructor()
                ->getMock();
        
        $mockServiceRequest->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('https://spreadsheets.google.com/feeds/list/G3345eEsfsk60/od6/private/full'),
                $this->stringContains('<gsx:occupation><![CDATA[software engineer]]></gsx:occupation>')
            );
        
        ServiceRequestFactory::setInstance($mockServiceRequest);
        
        $listFeed = new ListFeed(file_get_contents(__DIR__.'/xml/list-feed.xml'));
        $listFeed->insert(["name" => "asim", "occupation" => "software engineer"]);
    }
    
    public function testGetEntries()
    {
        $xml = file_get_contents(__DIR__.'/xml/list-feed.xml');
        $listFeed = new ListFeed($xml);

        $this->assertEquals(4, count($listFeed->getEntries()));
    }

}