<?php
namespace Google\Spreadsheet;

use PHPUnit_Framework_TestCase;

class SpreadsheetFeedTest extends PHPUnit_Framework_TestCase
{
    public function testGetByTitle()
    {
        $xml = file_get_contents(__DIR__.'/xml/spreadsheet-feed.xml');
        $spreadsheetFeed = new SpreadsheetFeed($xml);

        $this->assertTrue($spreadsheetFeed->getByTitle('Test Spreadsheet') instanceof Spreadsheet);
        $this->assertTrue(is_null($spreadsheetFeed->getByTitle('No Spreadsheet')));
    }

}