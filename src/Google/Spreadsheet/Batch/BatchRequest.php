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
namespace Google\Spreadsheet\Batch;

use Google\Spreadsheet\CellFeed;
use Google\Spreadsheet\CellEntry;
use Google\Spreadsheet\Exception\EmptyBatchException;

/**
 * BatchRequest.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class BatchRequest
{
    /**
     *
     * @var CellEntry[]
     */
    protected $entries;
    
    
    public function __construct()
    {
        $this->entries = array();
    }
    
    /**
     * 
     * @param CellEntry $cellEntry
     */
    public function addEntry(CellEntry $cellEntry)
    {
        $this->entries[] = $cellEntry;
    }
    
    /**
     * Get all entries in the batch
     * 
     * @return CellEntry[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * 
     * @param CellFeed $cellFeed
     * 
     * @return \SimpleXMLElement
     *
     * @throws EmptyBatchException
     */
    public function createRequestXml(CellFeed $cellFeed)
    {
        if(count($this->entries) === 0) {
            throw new EmptyBatchException();
            
        }
        
        $feed = new \SimpleXMLElement("
            <feed
                xmlns=\"http://www.w3.org/2005/Atom\"
                xmlns:batch=\"http://schemas.google.com/gdata/batch\"
                xmlns:gs=\"http://schemas.google.com/spreadsheets/2006\">
            </feed>
        ");

        $feed->id = $cellFeed->getPostUrl();

        $i = 1;
        foreach($this->entries as $cellEntry) {
            $entry = $feed->addChild("entry");

            $entry->addChild("xmlns:batch:id", "A".$i++);

            $op = $entry->addChild("xmlns:batch:operation");
            $op->addAttribute("type", "update");

            $entry->addChild("id", $cellFeed->getPostUrl() . "/" . $cellEntry->getCellIdString());

            $link = $entry->addChild("link");
            $link->addAttribute("rel", "edit");
            $link->addAttribute("type", "application/atom+xml");
            $link->addAttribute("href", $cellEntry->getEditUrl());

            $cell = $entry->addChild("xmlns:gs:cell");
            $cell->addAttribute("row", $cellEntry->getRow());
            $cell->addAttribute("col", $cellEntry->getColumn());
            $cell->addAttribute("inputValue", $cellEntry->getContent());
        }

        return $feed;
    }
    
}
