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

use Google\Spreadsheet\Exception\BadRequestException;
use Google\Spreadsheet\Exception\ResourceNotFoundException;

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
     * Fetches a list of spreadsheets from google drive.
     *
     * @return SpreadsheetFeed
     */
    public function getSpreadsheetFeed()
    {
        return new SpreadsheetFeed(
            new \SimpleXMLElement(
                ServiceRequestFactory::getInstance()->get(
                    "feeds/spreadsheets/private/full"
                )
            )
        );
    }

    /**
     * Fetch a resource directly with having to traverse the tree from
     * the top. This will provide a huge performance benefit to the
     * application if you already have the id.
     *
     * All classes which have a "getId()" method can be used. e.g.
     *     - SpreadsheetFeed
     *     - Spreadsheet
     *     - WorksheetFeed
     *     - Worksheet
     *     - ListFeed
     *     - CellFeed
     * 
     * @param string $resource the full path of the class
     * @param string $id       the id (full url) of the resource
     * 
     * @return Object
     *
     * @throws ResourceNotFoundException
     */
    public function getResourceById($resource, $id)
    {
        try {
            return new $resource(
                new \SimpleXMLElement(
                    ServiceRequestFactory::getInstance()->get($id)
                )
            );
        } catch (BadRequestException $e) {
            throw new ResourceNotFoundException($e->getMessage());
        }
    }

    /**
     * Get public spreadsheet
     * 
     * @param string $id Only the actual id and not the full url
     * 
     * @return WorksheetFeed
     */
    public function getPublicSpreadsheet($id)
    {
        $serviceRequest = ServiceRequestFactory::getInstance();

        $url = sprintf(
            "%sfeeds/worksheets/%s/public/full",
            $serviceRequest->getServiceUrl(),
            $id
        );

        return $this->getResourceById(WorksheetFeed::class, $url);
    }

}
