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
     * @param \Google\Spreadsheet\CellEntry $cellEntry
     */
    public function addEntry(CellEntry $cellEntry)
    {
        $this->entries[] = $cellEntry;
    }
    
    /**
     * 
     * @param \Google\Spreadsheet\CellFeed $cellFeed
     * 
     * @return string|null
     */
    public function createRequestXml(CellFeed $cellFeed)
    {
        if(count($this->entries) === 0) {
            return null;
        }
        
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
            <feed xmlns="http://www.w3.org/2005/Atom"
            xmlns:batch="http://schemas.google.com/gdata/batch"
            xmlns:gs="http://schemas.google.com/spreadsheets/2006">';
        
        $xml .= '<id>'. $cellFeed->getPostUrl() .'/batch</id>';
        
        $i = 1;
        foreach($this->entries as $cellEntry) {
            $xml .= $this->createEntry($cellEntry, $i++, $cellFeed);
        }
        
        $xml .= '</feed>';
        
        return $xml;
    }

    /**
     * 
     * @param \Google\Spreadsheet\CellEntry $cellEntry
     * @param string                        $index
     * @param \Google\Spreadsheet\CellFeed  $cellFeed
     * 
     * @return string
     */
    protected function createEntry(CellEntry $cellEntry, $index, CellFeed $cellFeed)
    {
        return sprintf(
            '<entry>
                <batch:id>%s</batch:id>
                <batch:operation type="update"/>
                <id>%s</id>
                <link rel="edit" type="application/atom+xml"
                  href="%s"/>
                <gs:cell row="%s" col="%s" inputValue="%s"/>
            </entry>',
            'A'.$index,
            $cellFeed->getPostUrl() . "/" . $cellEntry->getCellIdString(),
            $cellEntry->getEditUrl(),
            $cellEntry->getRow(),
            $cellEntry->getColumn(),
            $cellEntry->getContent()
        );
    }
    
}
