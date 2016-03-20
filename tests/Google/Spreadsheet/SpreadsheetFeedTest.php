<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\SpreadsheetFeed;
use Google\Spreadsheet\Spreadsheet;

class SpreadsheetFeedTest extends TestBase
{
    public function testGetByTitle()
    {
        $spreadsheetFeed = new SpreadsheetFeed(
            $this->getSimpleXMLElement("spreadsheet-feed")
        );

        $this->assertTrue($spreadsheetFeed->getByTitle("Test Spreadsheet") instanceof Spreadsheet);
        $this->assertNull($spreadsheetFeed->getByTitle("No Spreadsheet"));
    }

}