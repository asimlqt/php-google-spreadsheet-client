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
class CellFeed
{
    /**
     * The xml representation of the feed
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * Constructor
     * 
     * @param string $xml
     */
    public function __construct($xml)
    {
        $this->xml = new \SimpleXMLElement($xml);
    }

    /**
     * Get the cell feed xml
     * 
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
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
     * Get the feed post url
     * 
     * @return string
     */
    public function getPostUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/g/2005#post');
    }

}