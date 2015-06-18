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

use Google\Exception\GoogleException;
use Google\Exception\SpreadsheetException;

/**
 * List Entry
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class ListEntry
{
    /**
     * The xml representation of this list entry
     *
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * The data for this list entry
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param \SimpleXMLElement $xml
     * @param array             $data
     */
    public function __construct($xml, $data)
    {
        $this->xml  = $xml;
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
     * Update this entry
     *
     * @throws SpreadsheetException
     *
     * @param array $values
     */
    public function update($values)
    {
        $entry = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">';
        $entry .= '<id>' . $this->xml->id->__toString() . '</id>';

        foreach ($values as $colName => $value) {
            $entry .= sprintf(
                '<gsx:%s><![CDATA[%s]]></gsx:%s>',
                $colName,
                $value,
                $colName
            );
        }

        $entry .= '</entry>';
        try {
            ServiceRequestFactory::getInstance()->put($this->getEditUrl(), $entry);
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }
    }

    /**
     * Delete the current entry.
     *
     * @throws SpreadsheetException
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

    /**
     * Get the edit url
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
}