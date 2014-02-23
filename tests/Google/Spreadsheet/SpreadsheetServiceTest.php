<?php
namespace Google\Spreadsheet;

use PHPUnit_Framework_TestCase;

class SpreadsheetServiceTest extends PHPUnit_Framework_TestCase
{
    public function testGetSpreadsheets()
    {
        $serviceRequest = new TestServiceRequest(new Request(''));
        $serviceRequest->setExecuteReturn(file_get_contents(__DIR__.'/xml/spreadsheet-feed.xml'));
        ServiceRequestFactory::setInstance($serviceRequest);

        $spreadsheetService = new SpreadsheetService();
        $feed = $spreadsheetService->getSpreadsheets();

        $this->assertTrue($feed instanceof SpreadsheetFeed);
    }

    public function testGetSpreadsheetById()
    {
        $serviceRequest = new TestServiceRequest(new Request(''));
        $serviceRequest->setExecuteReturn(file_get_contents(__DIR__.'/xml/spreadsheet.xml'));
        ServiceRequestFactory::setInstance($serviceRequest);

        $spreadsheetService = new SpreadsheetService();
        $spreadsheet = $spreadsheetService->getSpreadsheetById('spreadsheet-id');

        $this->assertTrue($spreadsheet instanceof Spreadsheet);
    }
}