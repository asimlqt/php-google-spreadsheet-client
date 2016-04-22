<?php
/**
 * Copyright 2013 Asim Liaquat
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Spreadsheet;

use Google\Spreadsheet\Batch\BatchRequest;
use Google\Spreadsheet\Batch\BatchResponse;

/**
 * Worksheet Data.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class CellFeed
{
    /**
     * The xml representation of the feed
     * 
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     *
     * @var array
     */
    protected $entries;
    
    /**
     * Constructor
     * 
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
        $this->entries = array();
    }

    /**
     * Get the raw XML
     * 
     * @return int
     */
    public function getXml()
    {
        return $this->xml;
    }
    
    /**
     * Get the feed id. Returns the full url.
     *
     * @return string
     */
    public function getId()
    {
        return $this->xml->id->__toString();
    }

    /**
     * Get the feed entries
     * 
     * @return array \Google\Spreadsheet\CellEntry
     */
    public function getEntries()
    {
        if(count($this->entries) > 0) {
            return $this->entries;
        }
            
        $postUrl = $this->getPostUrl();

        foreach ($this->xml->entry as $entry) {
            $cell = new CellEntry($entry, $postUrl);
            $this->entries[$cell->getCellIdString()] = $cell;
        }

        return $this->entries;
    }

    /**
     * Returns the feed entries as a two-dimensional array, indexed by row/column
     * number.  Array may be sparse, if returned cell data is sparse.
     *
     * @return array
     */
    public function toArray()
    {
        $entries = $this->getEntries();

        $result = array();
        foreach ($entries as $entry) {
            $result[$entry->getRow()][$entry->getColumn()] = $entry->getContent();
        }

        return $result;
    }

    /**
     *
     * @param int $row
     * @param int $col
     * 
     * @return CellEntry|null
     */
    public function getCell($row, $col)
    {
        if(count($this->entries) === 0) {
            $this->getEntries();
        }
        
        $id = sprintf(
            "R%sC%s",
            $row,
            $col
        );
        
        if(isset($this->entries[$id])) {
            return $this->entries[$id];
        }
        
        return null;
    }
    
    /**
     * Edit a single cell. the row and column indexing start at 1.
     * So the first column of the first row will be (1,1).
     * 
     * @param int    $rowNum Row number
     * @param int    $colNum Column number
     * @param string $value  Can also be a formula
     * 
     * @return void
     */
    public function editCell($rowNum, $colNum, $value)
    {
        $entry = new \SimpleXMLElement("
            <entry
                xmlns=\"http://www.w3.org/2005/Atom\"
                xmlns:gs=\"http://schemas.google.com/spreadsheets/2006\">
            </entry>
        ");

        $child = $entry->addChild("xmlns:gs:cell");
        $child->addAttribute("row", $rowNum);
        $child->addAttribute("col", $colNum);
        $child->addAttribute("inputValue", $value);

        ServiceRequestFactory::getInstance()->post($this->getPostUrl(), $entry->asXML());
    }

    /**
     * 
     * @param \Google\Spreadsheet\Batch\BatchRequest $batchRequest
     * 
     * @return \Google\Spreadsheet\Batch\BatchResponse
     */
    public function updateBatch(BatchRequest $batchRequest)
    {
        $xml = $batchRequest->createRequestXml($this);
        $response = ServiceRequestFactory::getInstance()->post($this->getBatchUrl(), $xml);
        return new BatchResponse(new \SimpleXMLElement($response));
    }

    /**
     *
     * @param \Google\Spreadsheet\Batch\BatchRequest $batchRequest
     *
     * @return \Google\Spreadsheet\Batch\BatchResponse
     */
    public function insertBatch(BatchRequest $batchRequest)
    {
        $xml = $batchRequest->createRequestXml($this);

        $response = ServiceRequestFactory::getInstance()
            ->addHeader("If-Match", "*")
            ->post($this->getBatchUrl(), $xml->asXML());
        
        ServiceRequestFactory::getInstance()->removeHeader("If-Match");

        return new BatchResponse(new \SimpleXMLElement($response));
    }
    
    /**
     * Get the feed post url
     * 
     * @return string
     */
    public function getPostUrl()
    {
        return Util::getLinkHref($this->xml, "http://schemas.google.com/g/2005#post");
    }

    /**
     * 
     * @return string
     */
    public function getBatchUrl()
    {
        return Util::getLinkHref($this->xml, "http://schemas.google.com/g/2005#batch");
    }

    /**
     * Create a entry to insert data
     *
     * @param int    $row
     * @param int    $col
     * @param string $value
     * 
     * @return CellEntry
     */
    public function createCell($row, $col, $value)
    {
        $entry = new \SimpleXMLElement("
            <entry
                xmlns=\"http://www.w3.org/2005/Atom\"
                xmlns:gs=\"http://schemas.google.com/spreadsheets/2006\">
            </entry>
        ");

        // use ->content instead of addChild('content', $value)
        // due to addChild not escaping & properly.
        // http://php.net/manual/en/simplexmlelement.addchild.php#112204
        $entry->content = $value;
        $child = $entry->content;
        $child->addAttribute("type", "text");
        $child = $entry->addChild("title");
        $child->addAttribute("type", "text");
        $entry->addChild("id", $this->getPostUrl() . "/R" . $row . "C" . $col);
        $link = $entry->addChild("link");
        $link->addAttribute("rel", "edit");
        $link->addAttribute("type", "application/atom+xml");
        $link->addAttribute("href", $this->getPostUrl() . "/R" . $row . "C" . $col);

        $elementType = "gs:cell";
        $entry->{$elementType} = $value;
        $child = $entry->{$elementType};
        $child->addAttribute("row", $row);
        $child->addAttribute("col", $col);
        $child->addAttribute("inputValue", $value);

        return new CellEntry(
            new \SimpleXMLElement($entry->asXML()),
            $this->getPostUrl()
        );
    }
    
}
