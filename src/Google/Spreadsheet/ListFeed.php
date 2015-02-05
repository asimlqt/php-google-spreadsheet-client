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
     * @param string $xmlString
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
        $htmlspchrsFlags = defined('ENT_XML1') ? ENT_COMPAT | ENT_XML1 : ENT_COMPAT; // backward compatibility for PHP5.3
        foreach($row as $colName => $value) {
            $value = htmlspecialchars($value, $htmlspchrsFlags, ini_get("default_charset"), false);
            $colName = htmlspecialchars($colName, $htmlspchrsFlags, ini_get("default_charset"), false);
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

}
