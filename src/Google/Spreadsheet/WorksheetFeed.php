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

use Google\Spreadsheet\Exception\WorksheetNotFoundException;

/**
 * Worksheet Feed.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class WorksheetFeed
{
    /**
     * Worksheet feed xml object
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * Initializes thie worksheet feed object
     * 
     * @param SimpleXMLElement $xml
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
     * Get all worksheets in this feed
     * 
     * @return Worksheet[]
     */
    public function getEntries()
    {
        $worksheets = [];
        foreach ($this->xml->entry as $entry) {
            $worksheets[] = new Worksheet($entry);
        }
        return $worksheets;
    }

    /**
     * Get the worksheet feed post url
     * 
     * @return string
     */
    public function getPostUrl()
    {
        return Util::getLinkHref($this->xml, 'http://schemas.google.com/g/2005#post');
    }

    /**
     * Get a worksheet by title (name)
     * 
     * @param string $title name of the worksheet
     * 
     * @return Worksheet
     *
     * @throws WorksheetNotFoundException
     */
    public function getByTitle($title)
    {
        foreach ($this->xml->entry as $entry) {
            if ($entry->title->__toString() == $title) {
                return new Worksheet($entry);
            }
        }
        
        throw new WorksheetNotFoundException();
    }
    
    /**
     * Get a worksheet by id
     *
     * @param string $id of the worksheet
     *
     * @return Worksheet
     *
     * @throws WorksheetNotFoundException
     */
    public function getById($id)
    {
        $feedId = $this->xml->id->__toString();

        foreach ($this->xml->entry as $entry) {
            if ($entry->id == $feedId . '/' . $id) {
                return new Worksheet($entry);
            }
        }

        throw new WorksheetNotFoundException();
    }

}
