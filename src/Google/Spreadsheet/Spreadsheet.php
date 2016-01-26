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
use DateTime;

/**
 * Spreadsheet. Represents a single spreadsheet.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class Spreadsheet
{
    const REL_WORKSHEETS_FEED = 'http://schemas.google.com/spreadsheets/2006#worksheetsfeed';

    /**
     * The spreadsheet xml object
     * 
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * Initializes the spreadsheet object
     * 
     * @param SimpleXMLElement $xml
     */
    public function __construct(SimpleXMLElement $xml)
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
     * Get the spreadsheet id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->xml->id->__toString();
    }

    /**
     * Get the updated date
     * 
     * @return DateTime
     */
    public function getUpdated()
    {
        return new DateTime($this->xml->updated->__toString());
    }

    /**
     * Returns the title (name) of the spreadsheet
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->xml->title->__toString();
    }

    /**
     * Get all the worksheets which belong to this spreadsheet
     * 
     * @return \Google\Spreadsheet\WorksheetFeed
     */
    public function getWorksheets()
    {
        $res = ServiceRequestFactory::getInstance()->get($this->getWorksheetsFeedUrl());
        return new WorksheetFeed($res);
    }

    /**
     * Add a new worksheet to this spreadsheet
     * 
     * @param string $title
     * @param int    $rowCount default is 100
     * @param int    $colCount default is 10
     *
     * @return \Google\Spreadsheet\Worksheet
     */
    public function addWorksheet($title, $rowCount=100, $colCount=10)
    {
        $entry = sprintf('
            <entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
                <title>%s</title>
                <gs:rowCount>%u</gs:rowCount>
                <gs:colCount>%u</gs:colCount>
            </entry>',
            $title,
            $rowCount,
            $colCount
        );

        $response = ServiceRequestFactory::getInstance()->post($this->getWorksheetsFeedUrl(), $entry);
        return new Worksheet(new SimpleXMLElement($response));
    }

    /**
     * Returns the feed url of the spreadsheet
     * 
     * @return string
     */
    public function getWorksheetsFeedUrl()
    {
        return Util::getLinkHref($this->xml, self::REL_WORKSHEETS_FEED);
    }

}