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
class CellFeed
{
    /**
     * The xml representation of the feed
     * 
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * Constructor
     * 
     * @param string $xml
     */
    public function __construct($xml)
    {
        $this->xml = new SimpleXMLElement($xml);
    }

    /**
     * Get the feed entries
     * 
     * @return array \Google\Spreadsheet\CellEntry
     */
    public function getEntries()
    {
        $entries = array();
        $postUrl = $this->getPostUrl();

        foreach ($this->xml->entry as $entry) {
            $entries[] = new CellEntry($entry, $postUrl);
        }

        return $entries;
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
     * Get the feed post url
     * 
     * @return string
     */
    public function getPostUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/g/2005#post');
    }

}