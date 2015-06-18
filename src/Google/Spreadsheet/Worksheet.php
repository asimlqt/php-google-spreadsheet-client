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
use Google\Exception\GoogleException;
use Google\Exception\SpreadsheetException;
use SimpleXMLElement;

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

    private $postUrl;

    /**
     * Initializes the worksheet object.
     *
     * @param SimpleXMLElement $xml
     */
    public function __construct(SimpleXMLElement $xml)
    {
        $xml->registerXPathNamespace('gs', 'http://schemas.google.com/spreadsheets/2006');
        $this->xml = $xml;
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
     * Get the worksheet id. Extracts the actual string id of the worksheet
     * as opposed to the full url as in getId().
     *
     * @return string
     */
    public function getWorksheetId()
    {
        $parts = explode("/", $this->xml->id->__toString());
        if (count($parts) === 9) {
            return $parts[5];
        }
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
     * @throws SpreadsheetException
     *
     * @return \Google\Spreadsheet\ListFeed
     */
    public function getListFeed(array $query = array())
    {
        $feedUrl = $this->getListFeedUrl();
        if (count($query) > 0) {
            $feedUrl .= "?" . http_build_query($query);
        }

        try {
            $res = ServiceRequestFactory::getInstance()->get($feedUrl);
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }

        return new ListFeed($res);
    }

    /**
     * Get the cell feed of this worksheet
     *
     * @throws SpreadsheetException
     *
     * @return \Google\Spreadsheet\CellFeed
     */
    public function getCellFeed(array $query = array())
    {
        $feedUrl = $this->getCellFeedUrl();
        if (count($query) > 0) {
            $feedUrl .= "?" . http_build_query($query);
        }

        try {
            $res = ServiceRequestFactory::getInstance()->get($feedUrl);
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }

        return new CellFeed($res);
    }

    /**
     * Get csv data of this worksheet
     *
     * @return string
     *
     * @throws SpreadsheetException
     */
    public function getCsv()
    {
        try {
            return ServiceRequestFactory::getInstance()->get($this->getExportCsvUrl());
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }
    }

    /**
     * Update worksheet
     *
     * @param string $title    will not be updated if null or omitted.
     * @param int    $colCount will not be updated if null or omitted.
     * @paramthrows Spre int $rowCount will not be updated if null or omitted.
     *
     * @throws SpreadsheetException
     *
     * @return void
     */
    public function update($title = null, $colCount = null, $rowCount = null)
    {
        $title    = $title ? $title : $this->getTitle();
        $colCount = $colCount ? $colCount : $this->getColCount();
        $rowCount = $rowCount ? $rowCount : $this->getRowCount();

        $entry = sprintf('
            <entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
                <title type="text">%s</title>
                <gs:colCount>%s</gs:colCount>
                <gs:rowCount>%s</gs:rowCount>
            </entry>',
            $title,
            $colCount,
            $rowCount
        );

        try {
            ServiceRequestFactory::getInstance()->put($this->getEditUrl(), $entry);
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }
    }

    /**
     * Delete this worksheet
     *
     * @throws SpreadsheetException
     *
     * @return null
     */
    public function delete()
    {
        try {
            ServiceRequestFactory::getInstance()->delete($this->getEditUrl());
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }
    }

    public function setPostUrl($url)
    {
        $this->postUrl = $url;
    }

    /**
     * Get the edit url of the worksheet
     *
     * @throws SpreadsheetException
     *
     * @return string
     */
    public function getEditUrl()
    {
        try {
            return Util::getLinkHref($this->xml, 'edit');
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error occurred while retrieving url.', 0, $exception);
        }
    }

    /**
     * The url which is used to fetch the data of a worksheet as a list
     *
     * @throws SpreadsheetException
     *
     * @return string
     */
    public function getListFeedUrl()
    {
        try {
            return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#listfeed');
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error occurred while retrieving url.', 0, $exception);
        }
    }

    /**
     * Get the cell feed url
     *
     * @throws SpreadsheetException
     *
     * @return string
     */
    public function getCellFeedUrl()
    {
        try {
            return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#cellsfeed');
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error occurred while retrieving url.', 0, $exception);
        }
    }

    /**
     * Get the export csv url
     *
     * @throws SpreadsheetException
     *
     * @return string
     */
    public function getExportCsvUrl()
    {
        try {
            return Util::getLinkHref($this->xml, 'http://schemas.google.com/spreadsheets/2006#exportcsv');
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error occurred while retrieving url.', 0, $exception);
        }
    }

}
