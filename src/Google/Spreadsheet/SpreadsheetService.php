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
 * @version    0.1
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class SpreadsheetService
{
    /**
     * Fetches a list of spreadhsheet spreadsheets from google drive.
     *
     * @return \Google\Spreadsheet\SpreadsheetFeed
     */
    public function getSpreadsheets()
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setEndpoint('feeds/spreadsheets/private/full');
        $res = $serviceRequest->execute();
        return new SpreadsheetFeed($res);
    }

    /**
     * Fetches a single spreadsheet from google drive by id if you decide
     * to store the id locally. This can help reduce api calls.
     *
     * @param  string $id the id of the spreadsheet
     *
     * @return \Google\Spreadsheet\Spreadsheet
     */
    public function getSpreadsheetById($id)
    {
        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setEndpoint('feeds/spreadsheets/private/full/'. $id);
        $res = $serviceRequest->execute();
        return new Spreadsheet($res);
    }
}
