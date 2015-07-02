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

/**
 * Worksheet Data.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class CellEntry
{
    /**
     * Xml element for a cell entry
     * 
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * The url for making a post request
     * 
     * @var string
     */
    protected $postUrl;

    /**
     * The row number of this cell
     *
     * @var int
     */
    protected $row;
    
    /**
     * The row number of this cell
     *
     * @var int
     */
    protected $column;
    
    /**
     * The contents of this cell
     *
     * @var string
     */
    protected $content;
    
    /**
     * Constructor
     * 
     * @param SimpleXMLElement  $xml
     * @param string            $postUrl
     */
    public function __construct($xml, $postUrl)
    {
        $this->xml = $xml;
        $this->postUrl = $postUrl;
        $this->setCellLocation();
        /** @noinspection PhpUndefinedFieldInspection */
        $this->content = $this->xml->content->__toString();
    }

    /**
     * 
     * @return string
     */
    public function getCellIdString()
    {
        return sprintf(
            "R%sC%s",
            $this->row,
            $this->column
        );
    }
        
    /**
     * Get the row number fo this cell
     * 
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }
    
    /**
     * Get the column number fo this cell
     * 
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
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
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->xml->title->__toString();
    }

    /**
     * Get the contents of the cell
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the contents of this cell
     * 
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
        $entry = sprintf('
            <entry xmlns="http://www.w3.org/2005/Atom"
                xmlns:gs="http://schemas.google.com/spreadsheets/2006">
              <gs:cell row="%u" col="%u" inputValue="%s"/>
            </entry>',
            $this->row,
            $this->column,
            $value
        );

        $res = ServiceRequestFactory::getInstance()->post($this->postUrl, $entry);
        $this->xml = new SimpleXMLElement($res);
    }

    /**
     * Get the location of the cell.
     *
     * @throws Exception
     */
    protected function setCellLocation()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $id = $this->xml->id->__toString();
        preg_match('@/R(\d+)C(\d+)@', $id, $matches);

        if(count($matches) !== 3) {
            throw new Exception('Filed to get the location of the cell');
        }

        $this->row = (int) $matches[1];
        $this->column = (int) $matches[2];
    }

    /**
     * Get the edit url of the cell
     * 
     * @return string
     */
    public function getEditUrl()
    {
        return Util::getLinkHref($this->xml, 'edit');
    }
    
}
