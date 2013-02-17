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
 * List Entry
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @version    0.1
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class ListEntry
{
    /**
     * The xml representation of this list entry
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * The data for this list entry
     * 
     * @var array
     */
    private $data;

    /**
     * Constructor
     * 
     * @param \SimpleXMLElement $xml
     * @param array             $data
     */
    public function __construct($xml, $data)
    {
        $this->xml = $xml;
        $this->data = $data;
    }

    /**
     * Get the values of this list entry
     * 
     * @return array
     */
    public function getValues()
    {
        return $this->data;
    }

    /**
     * Get the edit url
     * 
     * @return string
     */
    public function getEditUrl()
    {
        return Util::getLinkHref($this->xml, 'edit');
    }
}