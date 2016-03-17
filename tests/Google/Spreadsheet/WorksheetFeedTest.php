<?php
namespace Google\Spreadsheet;

use PHPUnit_Framework_TestCase;

class WorksheetFeedTest extends PHPUnit_Framework_TestCase
{
    public function testGetByTitle()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet-feed.xml');
        $worksheetFeed = new WorksheetFeed($xml);

        $this->assertTrue($worksheetFeed->getByTitle('Sheet1') instanceof Worksheet);
        $this->assertTrue(is_null($worksheetFeed->getByTitle('Sheet3')));
    }
    
    public function testGetById()
    {
        $xml = file_get_contents(__DIR__.'/xml/worksheet-feed.xml');
        $worksheetFeed = new WorksheetFeed($xml);

        $this->assertTrue($worksheetFeed->getById('od6') instanceof Worksheet);
        $this->assertTrue(is_null($worksheetFeed->getById('od7')));
    }

}
