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
 * Worksheet Data.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class CellEntry extends ArrayIterator
{
    /**
     * Xml element for a cell entry
     * 
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * The url for making a post request
     * 
     * @var string
     */
    protected $postUrl;

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
        if(strlen($this->cellValue) == 0 && $this->xml instanceof SimpleXMLElement)
            $this->cellValue = $this->xml->content->__toString();

        return $this->cellValue;
    }

    /**
     * Update the cell value
     *
     * @param string $value
     * 
     * @return null
     */
    public function update($value)
    {
        $location = $this->getCellLocation();

        $entry = sprintf('
            <entry xmlns="http://www.w3.org/2005/Atom"
                xmlns:gs="http://schemas.google.com/spreadsheets/2006">
              <gs:cell row="%u" col="%u" inputValue="%s"/>
            </entry>',
            $location['row'],
            $location['col'],
            $value
        );

        $res = ServiceRequestFactory::getInstance()->post($this->postUrl, $entry);
        $this->xml = new SimpleXMLElement($res);
    }

    /**
     * Get the location of the cell.
     * 
     * @return array
     */
    protected function getCellLocation()
    {
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