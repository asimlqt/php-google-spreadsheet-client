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

use SimpleXMLElement;
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
     * @param string $xml
     */
    public function __construct($xml)
    {
        $this->xml = new SimpleXMLElement($xml);
        $this->entries = array();
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
     * 
     * @param type $row
     * @param type $col
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
     * @param int    $rowNum
     * @param int    $colNum
     * @param string $value
     * 
     * @return null
     */
    public function editCell($rowNum, $colNum, $value)
    {
        $entry = sprintf('
            <entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
              <gs:cell row="%u" col="%u" inputValue="%s"/>
            </entry>',
            $rowNum,
            $colNum,
            $value
        );

        ServiceRequestFactory::getInstance()->post($this->getPostUrl(), $entry);
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
        return new BatchResponse(new SimpleXMLElement($response));
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
            ->setHeaders(array("If-Match" => "*"))
            ->post($this->getBatchUrl(), $xml);
        return new BatchResponse(new SimpleXMLElement($response));
    }
    
    /**
     * Get the feed post url
     * 
     * @return string
     */
    public function getPostUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/g/2005#post');
    }

    /**
     * 
     * @return string
     */
    public function getBatchUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/g/2005#batch');
    }

    /**
     * Create a entry to insert data
     *
     * @param int    $row
     * @param int    $col
     * @param string $content
     * 
     * @return CellEntry
     */
    public function createInsertionCell($row, $col, $content)
    {
        $xml = new SimpleXMLElement('<entry></entry>');
        $child = $xml->addChild('content', $content);
        $child->addAttribute('type', 'text');
        $child = $xml->addChild('title');
        $child->addAttribute('type', 'text');
        $xml->addChild('id', $this->getPostUrl() . '/R' . $row . 'C' . $col);
        $link = $xml->addChild('link');
        $link->addAttribute('rel', 'edit');
        $link->addAttribute('type', 'application/atom+xml');
        $link->addAttribute('href', $this->getPostUrl() . '/R' . $row . 'C' . $col);

        return new CellEntry($xml, $this->getPostUrl());
    }
    
}
