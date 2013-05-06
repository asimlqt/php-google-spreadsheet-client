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
 * Spreadsheet. Represents a single spreadsheet.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @version    0.1
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class Spreadsheet
{
    /**
     * The spreadsheet xml object
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * Initializes the spreadsheet object
     * 
     * @param string|\SimpleXMLElement $xml
     */
    public function __construct($xml) {
        if(is_string($xml))
            $this->xml = new \SimpleXMLElement($xml);
        else
            $this->xml = $xml;
    }

    /**
     * Get the spreadsheet xml
     * 
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Get all the worksheets which belong to this spreadsheet
     * 
     * @return \Google\Spreadsheet\WorksheetFeed
     */
    public function getWorksheets()
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setFullUrl($this->getFeedUrl());
        $res = $serviceRequest->execute();
        return new WorksheetFeed($res);
    }

    /**
     * Add a new worksheet to this spreadsheet
     * 
     * @param string $title
     *
     * @return \Google\Spreadsheet\Worksheet
     */
    public function addWorksheet($title, $rowCount=100, $colCount=10)
    {
        $entry = '
            <entry xmlns="http://www.w3.org/2005/Atom"
            xmlns:gs="http://schemas.google.com/spreadsheets/2006">
            <title>'. $title .'</title>
            <gs:rowCount>'. $rowCount .'</gs:rowCount>
            <gs:colCount>'. $colCount .'</gs:colCount>
            </entry>
        ';

        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setFullUrl($this->getFeedUrl());
        $serviceRequest->getRequest()->setMethod(Request::POST);
        $serviceRequest->getRequest()->setPost($entry);
        $serviceRequest->getRequest()->setHeaders(array('Content-Type'=>'application/atom+xml'));
        $res = $serviceRequest->execute();
        return new Worksheet($res);
    }

    /**
     * Returns the feed url of the spreadsheet
     * 
     * @return string
     */
    public function getFeedUrl() {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#worksheetsfeed');
    }

    /**
     * Returns the title (name) of the spreadsheet
     * 
     * @return string
     */
    public function getTitle() {
        return $this->xml->title->__toString();
    }

}