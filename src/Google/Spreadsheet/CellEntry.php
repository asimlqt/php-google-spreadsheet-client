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
class CellEntry extends \ArrayIterator
{
    /**
     * Xml element for a cell entry
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * The url for making a post request
     * 
     * @var string
     */
    private $postUrl;

    /**
     * The contents of a cell
     * 
     * @var string
     */
    private $cellValue = '';

    /**
     * The cell this entry refers to
     * 
     * @var array
     */
    private $cell;

    /**
     * Constructor
     * 
     * @param \SimpleXMLElement $xml
     * @param string            $postUrl
     */
    public function __construct($xml, $postUrl)
    {
        $this->xml = $xml;
        $this->postUrl = $postUrl;
    }

    /**
     * Get the cell entry xml
     * 
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Set the post url
     * 
     * @param string
     *
     * @return void
     */
    public function setPostUrl($url)
    {
        $this->postUrl = $url;
    }

    /**
     * Get the cell identifier e.g. A1
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->xml->title->__toString();
    }

    /**
     * Get the contents of the cell
     * 
     * @return string
     */
    public function getContent()
    {
        if(strlen($this->cellValue) == 0 && $this->xml instanceof \SimpleXMLElement)
            $this->cellValue = $this->xml->content->__toString();

        return $this->cellValue;
    }

    /**
     * Set the contents of the cell
     * 
     * @param string $value
     */
    public function setContent($value)
    {
        if(!is_string($value))
            throw new Exception('value must be a string');

        $this->cellValue = $value;
    }

    /**
     * Update the cell value
     * 
     * @return void
     */
    public function update()
    {
        $loc = $this->getCell();
        $serviceRequest = ServiceRequestFactory::getInstance();

        $entry = '
            <entry xmlns="http://www.w3.org/2005/Atom"
                xmlns:gs="http://schemas.google.com/spreadsheets/2006">
              <gs:cell row="'.$loc['row'].'" col="'.$loc['col'].'" inputValue="'.$this->cellValue.'"/>
            </entry>
        ';

        $serviceRequest->getRequest()->setFullUrl($this->postUrl);
        $serviceRequest->getRequest()->setMethod(Request::POST);
        $serviceRequest->getRequest()->setHeaders(array('Content-Type'=>'application/atom+xml'));
        $serviceRequest->getRequest()->setPost($entry);
        $ret = $serviceRequest->execute();
        $this->xml = new \SimpleXMLElement($ret);
    }

    /**
     * Set the cell location
     * 
     * @param int $row row number
     * @param int $col column number
     */
    public function setCell($row, $col)
    {
        $this->cell = array(
            'row' => $row,
            'col' => $col,
        );
    }

    /**
     * Get the location of the cell.
     * 
     * @return array
     */
    private function getCell()
    {
        if(!is_null($this->cell))
            return $this->cell;

        $id = $this->xml->id->__toString();
        preg_match('@/R(\d)C(\d)@', $id, $matches);

        if(count($matches) !== 3)
            throw new Exception('Filed to get the location of the cell');

        return array(
            'row' => $matches[1],
            'col' => $matches[2],
        );
    }

}