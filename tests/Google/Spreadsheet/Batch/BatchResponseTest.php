<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\Batch\BatchResponse;

class BatchResponseTest extends TestBase
{
    private $batchResponse;
    
    public function setUp()
    {
        $this->batchResponse = new BatchResponse($this->getSimpleXMLElement("batch-response"));
    }

    public function testGetXml()
    {
        $this->assertTrue($this->batchResponse->getXml() instanceof \SimpleXMLElement);
    }
    
    public function testHasErrors()
    {
        $this->assertFalse($this->batchResponse->hasErrors());

        $batchResponse = new BatchResponse($this->getSimpleXMLElement("batch-response-error"));
        $this->assertTrue($batchResponse->hasErrors());
    }
}