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

use DateTime;

/**
 * Worksheet.
 *
 * @package    Google
 * @subpackage Spreadsheet
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

    /**
     * Initializes the worksheet object.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $xml->registerXPathNamespace('gs', 'http://schemas.google.com/spreadsheets/2006');
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
     * Get the worksheet id. Returns the full url.
     *
     * @return string
     */
    public function getId()
    {
        return $this->xml->id->__toString();
    }
    
    /**
     * Get the worksheet GID
     *
     * @return int
     */
    public function getGid()
    {
        parse_str(
            parse_url($this->getExportCsvUrl(), PHP_URL_QUERY),
            $query
        );

        return (int) $query['gid'];
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
     * Get the title of the worksheet
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->xml->title->__toString();
    }

    /**
     * Get the number of rows in the worksheet
     *
     * @return int
     */
    public function getRowCount()
    {
        $result = $this->xml->xpath('gs:rowCount');
        return (int) $result[0]->__toString();
    }

    /**
     * Get the number of columns in the worksheet
     *
     * @return int
     */
    public function getColCount()
    {
        $result = $this->xml->xpath('gs:colCount');
        return (int) $result[0]->__toString();
    }

    /**
     * Get the list feed of this worksheet
     *
     * @param array $query add additional query params to the url to sort/filter the results
     * 
     * @return ListFeed
     */
    public function getListFeed(array $query = [])
    {
        $feedUrl = $this->getListFeedUrl();
        if(count($query) > 0) {
            $feedUrl .= "?" . http_build_query($query);
        }

        $res = ServiceRequestFactory::getInstance()->get($feedUrl);
        return new ListFeed(new \SimpleXMLElement($res));
    }

    /**
     * Get the cell feed of this worksheet
     * 
     * @return CellFeed
     */
    public function getCellFeed(array $query = [])
    {
        $feedUrl = $this->getCellFeedUrl();
        if(count($query) > 0) {
            $feedUrl .= "?" . http_build_query($query);
        }

        $res = ServiceRequestFactory::getInstance()->get($feedUrl);
        return new CellFeed(new \SimpleXMLElement($res));
    }

    /**
     * Get csv data of this worksheet
     *
     * @return string
     * 
     * @throws Exception
     */
    public function getCsv()
    {
        return ServiceRequestFactory::getInstance()->get($this->getExportCsvUrl());
    }

    /**
     * Update worksheet
     *
     * @param string $title
     * @param int    $colCount
     * @param int    $rowCount
     *
     * @return void
     */
    public function update($title = null, $colCount = null, $rowCount = null)
    {
        $title = $title ? $title : $this->getTitle();
        $colCount = $colCount ? $colCount : $this->getColCount();
        $rowCount = $rowCount ? $rowCount : $this->getRowCount();

        $entry = new \SimpleXMLElement("
            <entry
                xmlns=\"http://www.w3.org/2005/Atom\"
                xmlns:gs=\"http://schemas.google.com/spreadsheets/2006\">
            </entry>
        ");

        $entry->title = $title;
        $entry->addChild("xmlns:gs:rowCount", (int) $rowCount);
        $entry->addChild("xmlns:gs:colCount", (int) $colCount);

        ServiceRequestFactory::getInstance()->put($this->getEditUrl(), $entry->asXML());
    }

    /**
     * Delete this worksheet
     *
     * @return null
     */
    public function delete()
    {
        ServiceRequestFactory::getInstance()->delete($this->getEditUrl());
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
     * 
     * @throws Exception
     */
    public function getExportCsvUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#exportcsv');
    }

}
