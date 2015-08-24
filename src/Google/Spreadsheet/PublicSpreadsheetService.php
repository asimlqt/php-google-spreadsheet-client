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
 * Spreadsheet Service.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class PublicSpreadsheetService
{
    /**
     * Fetches a list of spreadhsheet spreadsheets from google drive.
     *
     * @return \Google\Spreadsheet\SpreadsheetFeed
     */
    public function getSpreadsheets()
    {
        $instance = ServiceRequestFactory::getInstance();
        $key = $instance->getAccessToken();
        return new SpreadsheetFeed(
            $instance->get("feeds/worksheets/{$key}/public/full")
        );
    }

    /**
     * Returns a list feed of the specified worksheet.
     *
     * @see \Google\Spreadsheet\Worksheet::getWorksheetId()
     *
     * @param string $worksheetId
     *
     * @return \Google\Spreadsheet\ListFeed
     */
    public function getListFeed($worksheetId)
    {
        $instance = ServiceRequestFactory::getInstance();
        $key = $instance->getAccessToken();
        return new ListFeed(
            $instance->get("feeds/list/{$key}/{$worksheetId}/public/full")
        );
    }

    /**
     * Returns a cell feed of the specified worksheet.
     *
     * @see \Google\Spreadsheet\Worksheet::getWorksheetId()
     *
     * @param string $worksheetId
     *
     * @return \Google\Spreadsheet\CellFeed
     */
    public function getCellFeed($worksheetId)
    {
        $instance = ServiceRequestFactory::getInstance();
        $key = $instance->getAccessToken();
        return new CellFeed(
            $instance->get("feeds/cells/{$key}/{$worksheetId}/public/full")
        );
    }
}
