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
 * Spreadsheet feed. 
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @version    0.1
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class SpreadsheetFeed extends \ArrayIterator
{
    /**
     * The spreadsheet feed xml object
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * Initializes the the spreadsheet feed object
     * 
     * @param string $xml the raw xml string of a spreadsheet feed
     */
    public function __construct($xml)
    {
        $this->xml = new \SimpleXMLElement($xml);

        $spreadsheets = array();
        foreach ($this->xml->entry as $entry) {
            $spreadsheets[] = new Spreadsheet($entry);
        }
        parent::__construct($spreadsheets);
    }

    /**
     * Gets a spreadhseet from the feed by its title. i.e. the name of the spreadsheet
     * in google drive
     * 
     * @param  string $title
     * 
     * @return \Google\Spreadsheet\Spreadsheet will return null if no spreadhseet found with the specified title
     */
    public function getByTitle($title)
    {
        foreach($this->xml->entry as $entry) {
            if($entry->title->__toString() == $title) {
                return new Spreadsheet($entry);
            }
        }
        return null;
    }

}