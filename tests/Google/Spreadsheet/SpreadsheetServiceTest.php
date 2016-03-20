<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\SpreadsheetService;
use Google\Spreadsheet\Spreadsheet;
use Google\Spreadsheet\SpreadsheetFeed;

class SpreadsheetServiceTest extends TestBase
{
    public function testGetSpreadsheets()
    {
        $this->setServiceRequest('spreadsheet-feed.xml');

        $spreadsheetService = new SpreadsheetService();
        $feed = $spreadsheetService->getSpreadsheets();

        $this->assertTrue($feed instanceof SpreadsheetFeed);
    }

    public function testGetSpreadsheetById()
    {
        $this->setServiceRequest('spreadsheet.xml', false);

        $spreadsheetService = new SpreadsheetService();
        $spreadsheet = $spreadsheetService->getSpreadsheetById('spreadsheet-id');

        $this->assertTrue($spreadsheet instanceof Spreadsheet);
    }
}