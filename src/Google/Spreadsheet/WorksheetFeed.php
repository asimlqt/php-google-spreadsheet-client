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

use ArrayIterator;
use SimpleXMLElement;

/**
 * Worksheet Feed.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class WorksheetFeed extends ArrayIterator
{
    /**
     * Worksheet feed xml object
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * Initializes thie worksheet feed object
     * 
     * @param string $xml
     */
    public function __construct($xml)
    {
        $this->xml = new SimpleXMLElement($xml);

        $worksheets = array();
        foreach ($this->xml->entry as $entry) {
            $worksheet = new Worksheet($entry);
            $worksheet->setPostUrl($this->getPostUrl());
            $worksheets[] = $worksheet;
        }
        parent::__construct($worksheets);
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
     * Get the worksheet feed post url
     * 
     * @return string
     */
    private function getPostUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/g/2005#post');
    }

    /**
     * Get the cell feed url
     *
     * @return string
     */
    public function getCellFeedUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#cellsfeed');
    }

    /**
     * Get the export csv url
     *
     * @return string
     */
    public function getExportCsvUrl() {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#exportcsv');
    }

    /**
     * Get a worksheet by title (name)
     * 
     * @param string $title name of the worksheet
     * 
     * @return \Google\Spreadsheet\Worksheet
     */
    public function getByTitle($title)
    {
        foreach ($this->xml->entry as $entry) {
            if ($entry->title->__toString() == $title) {
                $worksheet = new Worksheet($entry);
                $worksheet->setPostUrl($this->getPostUrl());
                return $worksheet;
            }
        }
        return null;
    }

}