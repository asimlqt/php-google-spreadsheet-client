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
     * @param string $xmlStr
     */
    public function __construct($xmlString)
    {
        $xml = new SimpleXMLElement($xmlString);
        $xml->registerXPathNamespace('gsx', 'http://schemas.google.com/spreadsheets/2006/extended');
        $this->xml = $xml;
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
        $entry = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">';
        foreach($row as $colName => $value) {
            $entry .= sprintf(
                '<gsx:%s><![CDATA[%s]]></gsx:%s>',
                $colName,
                $value,
                $colName
            );
        }
        $entry .= '</entry>';

        ServiceRequestFactory::getInstance()->post($this->getPostUrl(), $entry);
    }

    /**
     * Get the entries of this feed
     * 
     * @return array \Google\Spreadsheet\ListEntry
     */
    public function getEntries()
    {
        $rows = array();

        if(count($this->xml->entry) > 0) {
            $colNames = $this->getColumnNames($this->xml);
            $colNamesCount = count($colNames);
            
            foreach ($this->xml->entry as $entry) {
                $cols = $entry->xpath('gsx:*');
                $vals = array();
                
                foreach($cols as $col) {
                    $vals[] = $col->__toString();
                }
                
                if(count($vals) < $colNamesCount) {
                    $vals = array_pad($vals, $colNamesCount, null);
                }
                
                $rows[] = new ListEntry($entry, array_combine($colNames, $vals));
            }
        }
        return $rows;
    }

    /**
     * Get the column names
     * 
     * @param \SimpleXMLElement $xml
     * 
     * @return array
     */
    public function getColumnNames(SimpleXMLElement $xml = null)
    {
        if($xml === null) {
            $xml = $this->xml;
        }
		
        $ret = array();
        foreach($xml->entry->xpath('gsx:*') as $col) {
            $ret[] = $col->getName();
        }
        return $ret;
    }
}