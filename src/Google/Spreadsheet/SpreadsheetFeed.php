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

use Google\Spreadsheet\Exception\SpreadsheetNotFoundException;

/**
 * Spreadsheet feed. 
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class SpreadsheetFeed
{
    /**
     * The spreadsheet feed xml object
     * 
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * Initializes the spreadsheet feed object
     * 
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
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
     * Get all spreadsheets in the feed
     * 
     * @return Spreadsheet[]
     */
    public function getEntries()
    {
        $spreadsheets = [];
        foreach ($this->xml->entry as $entry) {
            $spreadsheets[] = new Spreadsheet($entry);
        }
        return $spreadsheets;
    }

    /**
     * Gets a spreadhseet from the feed by its title. i.e. the name of 
     * the spreadsheet in google drive. This method will return only the
     * first spreadsheet found with the specified title.
     * 
     * @param string $title
     * 
     * @return Spreadsheet
     *
     * @throws SpreadsheetNotFoundException
     */
    public function getByTitle($title)
    {
        foreach($this->xml->entry as $entry) {
            if($entry->title->__toString() == $title) {
                return new Spreadsheet($entry);
            }
        }

        throw new SpreadsheetNotFoundException();
    }

    /**
     * Gets a spreadhseet from the feed by its ID in google drive.
     * 
     * @param string $id
     * 
     * @return Spreadsheet
     *
     * @throws SpreadsheetNotFoundException
     */
    public function getById($id)
    {
        foreach($this->xml->entry as $entry) {
            if($entry->id->__toString() == $id) {
                return new Spreadsheet($entry);
            }
        }

        throw new SpreadsheetNotFoundException();
    }

}