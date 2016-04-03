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

/**
 * Worksheet Data.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class ListFeed
{
    /**
     * Xml representation of this feed
     * 
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * Constructor
     * 
     * @param string $xmlString
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $xml->registerXPathNamespace('gsx', 'http://schemas.google.com/spreadsheets/2006/extended');
        $this->xml = $xml;
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
     * Get the post url for this feed
     * 
     * @return string
     */
    public function getPostUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/g/2005#post');
    }

    /**
     * Insert a new row into this feed
     * 
     * @param array $row
     * 
     * @return void
     */
    public function insert($row)
    {
        $entry = new \SimpleXMLElement("
            <entry
                xmlns=\"http://www.w3.org/2005/Atom\"
                xmlns:gsx=\"http://schemas.google.com/spreadsheets/2006/extended\">
            </entry>
        ");

        foreach($row as $colName => $value) {
            $entry->addChild("xmlns:gsx:$colName", $value);
        }

        ServiceRequestFactory::getInstance()->post($this->getPostUrl(), $entry->asXML());
    }

    /**
     * Get the entries of this feed
     * 
     * @return ListEntry[]
     */
    public function getEntries()
    {
        $rows = array();

        if(count($this->xml->entry) > 0) {
            
            foreach ($this->xml->entry as $entry) {
                $data = array();
                foreach($entry->xpath('gsx:*') as $col) {
                    $data[$col->getName()] = $col->__toString();
                }
                
                $rows[] = new ListEntry($entry, $data);
            }
        }
        
        return $rows;
    }

    /**
     * Get open search total results
     * 
     * @return int
     */
    public function getTotalResults()
    {
        $xml = $this->xml->children('openSearch', true);
        return intval($xml->totalResults);
    }

    /**
     * Get open search start index
     * 
     * @return int
     */
    public function getStartIndex()
    {
        $xml = $this->xml->children('openSearch', true);
        return intval($xml->startIndex);
    }

}
