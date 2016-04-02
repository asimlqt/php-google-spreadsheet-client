<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\Batch\BatchRequest;
use Google\Spreadsheet\CellFeed;
use Google\Spreadsheet\CellEntry;
use Google\Spreadsheet\Exception\EmptyBatchException;


class BatchRequestTest extends TestBase
{
    private $cellEntry;
    private $batchRequest;
    private $cellFeed;

    public function setUp()
    {
        $this->batchRequest = new BatchRequest();

        $feed = new CellFeed($this->getSimpleXMLElement("cell-feed"));
        $this->cellEntry = current($feed->getEntries());
        $this->cellFeed = $feed;
    }

    public function testAddEntry()
    {
        $this->batchRequest->addEntry($this->cellEntry);

        $this->assertEquals(1, count($this->batchRequest->getEntries()));
    }
    
    public function testCreateRequestXml()
    {
        $cellEntry = $this->cellFeed->createCell(2, 1, "one", true);
        $this->batchRequest->addEntry($cellEntry);

        $cellEntry = $this->cellFeed->createCell(2, 2, "two");
        $this->batchRequest->addEntry($cellEntry);

        $xml = $this->batchRequest->createRequestXml($this->cellFeed);

        $this->assertTrue($xml instanceof \SimpleXMLElement);
        $this->assertEquals(2, count($xml->entry));

        $sxe = new \SimpleXMLElement($xml->asXML());

        $entry1 = $sxe->entry[0]->children('gs', true)->attributes();
        $this->assertEquals("2", $entry1["row"]->__toString());
        $this->assertEquals("1", $entry1["col"]->__toString());
        $this->assertEquals("one", $entry1["inputValue"]->__toString());

        $entry2 = $sxe->entry[1]->children('gs', true)->attributes();
        $this->assertEquals("2", $entry2["row"]->__toString());
        $this->assertEquals("2", $entry2["col"]->__toString());
        $this->assertEquals("two", $entry2["inputValue"]->__toString());
    }

    /**
     * @expectedException Google\Spreadsheet\Exception\EmptyBatchException
     */
    public function testCreateRequestXmlNoEntries()
    {
        $this->batchRequest->createRequestXml($this->cellFeed);
    }

}