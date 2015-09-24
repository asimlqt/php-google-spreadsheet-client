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

use Google\Exception\RequestException;
use SimpleXMLElement;

/**
 * Utility class. Provides several methods which are common to multiple classes.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class Util
{
    /**
     * Extracts the endpoint from a full google spreadsheet url.
     *
     * @param string $url
     *
     * @return string
     */
    public static function extractEndpoint($url)
    {
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * Extracts the href for a specific rel from an xml object.
     *
     * @param  \SimpleXMLElement $xml
     * @param  string            $rel the value of the rel attribute whose href you want
     *
     * @throws RequestException
     *
     * @return string
     */
    public static function getLinkHref(SimpleXMLElement $xml, $rel)
    {
        foreach ($xml->link as $link) {
            $attributes = $link->attributes();
            if ($attributes['rel']->__toString() === $rel) {
                return $attributes['href']->__toString();
            }
        }
        throw new RequestException(sprintf('No link found with rel "%s".', $rel));
    }

}