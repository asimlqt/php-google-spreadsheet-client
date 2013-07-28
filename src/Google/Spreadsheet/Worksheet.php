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
 * Worksheet.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @version    0.1
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class Worksheet
{
    /**
     * A worksheet xml object
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    private $postUrl;

    private $editCellPostUrl;

    /**
     * Initializes the worksheet object.
     * 
     * @param string $xml
     */
    public function __construct($xml)
    {
        if(is_string($xml))
            $this->xml = new \SimpleXMLElement($xml);
        else
            $this->xml = $xml;
    }

    /**
     * Get the worksheet xml
     * 
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }

    public function getId()
    {
        $id = $this->xml->id->__toString();
        return $id;
    }

    public function getWorksheetId()
    {
        preg_match('@worksheets/([a-zA-z0-9]+)/@', $this->getId(), $match);
        if(is_array($match) && count($match) === 2) {
            return $match[1];
        }
        throw new Exception('Could not extract worksheet id');
    }


    /**
     * Get the title of the worksheet
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->xml->title->__toString();
    }

    private function getCellEditUrl()
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setFullUrl($this->getCellFeedUrl());
        $res = $serviceRequest->execute();
        $xml = new \SimpleXMLElement($res);
        return Util::getLinkHref($xml, 'http://schemas.google.com/g/2005#post');
    }

    /**
     * Get the list feed of this worksheet
     * 
     * @return \Google\Spreadsheet\ListFeed
     */
    public function getListFeed()
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setFullUrl($this->getListFeedUrl());
        $res = $serviceRequest->execute();
        return new ListFeed($res);
    }

    /**
     * Get the cell feed of this worksheet
     * 
     * @return \Google\Spreadsheet\CellFeed
     */
    public function getCellFeed()
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setFullUrl($this->getCellFeedUrl());
        $res = $serviceRequest->execute();
        return new CellFeed($res);
    }

    /**
     * Create the header row for this worksheet
     * 
     * @param array $headings
     * 
     * @return void
     */
    public function editCell($row, $col, $value)
    {
        $entry = '
            <entry xmlns="http://www.w3.org/2005/Atom"
                xmlns:gs="http://schemas.google.com/spreadsheets/2006">
              <gs:cell row="'.$row.'" col="'.$col.'" inputValue="'.$value.'"/>
            </entry>
        ';

        if(is_null($this->editCellPostUrl)) {
            $this->editCellPostUrl = $this->getCellFeed()->getPostUrl();
        }

        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setFullUrl($this->editCellPostUrl);
        $serviceRequest->getRequest()->setMethod(Request::POST);
        $serviceRequest->getRequest()->setHeaders(array('Content-Type'=>'application/atom+xml'));
        $serviceRequest->getRequest()->setPost($entry);
        $serviceRequest->execute();
    }

    /**
     * Create the header row for this worksheet
     * 
     * @param array $headings
     * 
     * @return void
     */
    public function createHeader(array $headings)
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $row = 1;
        $col = 1;

        foreach($headings as $heading) {

            $entry = '
                <entry xmlns="http://www.w3.org/2005/Atom"
                    xmlns:gs="http://schemas.google.com/spreadsheets/2006">
                  <gs:cell row="'.$row.'" col="'.$col.'" inputValue="'.$heading.'"/>
                </entry>
            ';

            $serviceRequest->getRequest()->setFullUrl($this->getPostUrl());
            $serviceRequest->getRequest()->setMethod(Request::POST);
            $serviceRequest->getRequest()->setHeaders(array('Content-Type'=>'application/atom+xml'));
            $serviceRequest->getRequest()->setPost($entry);
            $serviceRequest->execute();
            $col++;
        }
    }

    /**
     * Delete this worksheet
     * 
     * @return void
     */
    public function delete()
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setFullUrl($this->getEditUrl());
        $serviceRequest->getRequest()->setMethod(Request::DELETE);
        $serviceRequest->execute();
    }

    public function setPostUrl($url)
    {
        $this->postUrl = $url;
    }

    public function getPostUrl()
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setFullUrl($this->getCellFeedUrl());
        $res = $serviceRequest->execute();
        $xml = new \SimpleXMLElement($res);
        $postUrl = Util::getLinkHref($xml, 'http://schemas.google.com/g/2005#post');
        return $postUrl;
    }

    /**
     * Get the edit url of the worksheet
     * 
     * @return string
     */
    public function getEditUrl()
    {
        return Util::getLinkHref($this->xml, 'edit');
    }

    /**
     * The url which is used to fetch the data of a worksheet as a list
     * 
     * @return string
     */
    public function getListFeedUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#listfeed');
    }

    /**
     * Get the cell feed url
     * 
     * @return stirng
     */
    public function getCellFeedUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#cellsfeed');
    }
}