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
use SimpleXMLElement;

/**
 * Spreadsheet Service.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class SpreadsheetService
{
    /**
     * Fetches a list of spreadhsheet spreadsheets from google drive.
     *
     * @throws SpreadsheetException
     *
     * @return \Google\Spreadsheet\SpreadsheetFeed
     */
    public function getSpreadsheets()
    {
        try {
            return new SpreadsheetFeed(
                ServiceRequestFactory::getInstance()->get('feeds/spreadsheets/private/full')
            );
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }
    }

    /**
     * Fetches a single spreadsheet from google drive by id if you decide
     * to store the id locally. This can help reduce api calls.
     *
     * @param string $id the url of the spreadsheet
     *
     * @throws SpreadsheetException
     *
     * @return \Google\Spreadsheet\Spreadsheet
     */
    public function getSpreadsheetById($id)
    {
        try {
            return new Spreadsheet(
                new SimpleXMLElement(
                    ServiceRequestFactory::getInstance()->get('feeds/spreadsheets/private/full/' . $id)
                )
            );
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }
    }

    /**
     * Returns a list feed of the specified worksheet.
     *
     * @see \Google\Spreadsheet\Worksheet::getWorksheetId()
     *
     * @param string $worksheetId
     *
     * @throws SpreadsheetException
     *
     * @return \Google\Spreadsheet\ListFeed
     */
    public function getListFeed($worksheetId)
    {
        try {
            return new ListFeed(
                ServiceRequestFactory::getInstance()->get("feeds/list/{$worksheetId}/od6/private/full")
            );
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }
    }

    /**
     * Returns a cell feed of the specified worksheet.
     *
     * @see \Google\Spreadsheet\Worksheet::getWorksheetId()
     *
     * @param string $worksheetId
     *
     * @throws SpreadsheetException
     *
     * @return \Google\Spreadsheet\CellFeed
     */
    public function getCellFeed($worksheetId)
    {
        try {
            return new CellFeed(
                ServiceRequestFactory::getInstance()->get("feeds/cells/{$worksheetId}/od6/private/full")
            );
        }
        catch (GoogleException $exception) {
            throw new SpreadsheetException('Error while getting instance of ServiceRequestFactory.', 0, $exception);
        }
    }
}
