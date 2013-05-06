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
 * @version    0.1
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class ListFeed
{
    /**
     * Xml representation of this feed
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * Constructor
     * 
     * @param string $xmlStr
     */
    public function __construct($xmlStr)
    {
        $xml = new \SimpleXMLElement($xmlStr);
        $xml->registerXPathNamespace('gsx', 'http://schemas.google.com/spreadsheets/2006/extended');
        $this->xml = $xml;
    }

    /**
     * Get the list feed xml
     * 
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
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
        foreach($row as $col => $val) {
            $entry .= '<gsx:'. $col .'>'. $val .'</gsx:'. $col .'>';
        }
        $entry .= '</entry>';

        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setPost($entry);
        $serviceRequest->getRequest()->setMethod(Request::POST);
        $serviceRequest->getRequest()->setHeaders(array('Content-Type'=>'application/atom+xml'));
        $serviceRequest->getRequest()->setFullUrl($this->getPostUrl());
        $serviceRequest->execute();
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

            foreach ($this->xml->entry as $entry) {
                $cols = $entry->xpath('gsx:*');
                $vals = array();
                foreach($cols as $col) {
                    $vals[] = $col->__toString();
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
    private function getColumnNames($xml)
    {
        $ret = array();
        $entry = $xml->entry;
        $cols = $entry->xpath('gsx:*');
        foreach ($cols as $col) {
            $ret[] = $col->getName();
        }
        return $ret;
    }
}