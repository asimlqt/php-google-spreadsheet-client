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
use Google\Spreadsheet\Exception\UnauthorizedException;

/**
 * Service Request. The parent class of all services.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class DefaultServiceRequest implements ServiceRequestInterface
{
    /**
     * Request object
     * 
     * @var \Google\Spreadsheet\Request
     */
    protected $accessToken;

    /**
     * Token type (defaults to OAuth for BC)
     *
     * @var string
     */
    protected $tokenType;

    /**
     * Request headers
     * 
     * @var array
     */
    protected $headers = [];

    /**
     * Service url
     * 
     * @var string
     */
    protected $serviceUrl = "https://spreadsheets.google.com/";

    /**
     * User agent
     * 
     * @var string
     */
    protected $userAgent = "PHP Google Spreadsheet Api";

    /**
     * SSL verify peer
     * 
     * @var boolean
     */
    protected $sslVerifyPeer = true;

    /**
     * cURL parameters
     * 
     * @var array
     */
    protected $curlParams = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_FAILONERROR => false,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_VERBOSE => false,
    ];

    /**
     * Initializes the service request object.
     * 
     * @param string $accessToken
     * @param string $tokenType
     */
    public function __construct($accessToken, $tokenType = "OAuth")
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
    }

    /**
     * Get the hostname of the spreadsheet service
     * 
     * @return string
     */
    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    /**
     * Get request headers
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * Set optional request headers. 
     * 
     * @param array $headers associative array of key value pairs
     *
     * @return Google\Spreadsheet\DefaultServiceRequest
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Add a header to the headers array
     * 
     * @param string $name
     * @param string $value
     *
     * @return Google\Spreadsheet\DefaultServiceRequest
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * 
     * @param string $name
     * 
     * @return Google\Spreadsheet\DefaultServiceRequest
     */
    public function removeHeader($name)
    {
        if(array_key_exists($name, $this->headers)) {
            unset($this->headers[$name]);
        }
        
        return $this;
    }

    /**
     * Get the user agent
     * 
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }
    
    /**
     * Set the user agent. It is a good ides to leave this as is.
     * 
     * @param string $userAgent
     *
     * @return Google\Spreadsheet\DefaultServiceRequest
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Get the value for verifying the peers ssl certificate.
     * 
     * @return bool
     */
    public function getSslVerifyPeer()
    {
        return $this->curlParams[CURLOPT_SSL_VERIFYPEER];
    }
    
    /**
     * Verify the peer"s ssl certificate
     * 
     * @param bool $sslVerifyPeer
     * 
     * @return DefaultServiceRequest
     */
    public function setSslVerifyPeer($sslVerifyPeer)
    {
        $this->curlParams[CURLOPT_SSL_VERIFYPEER] = (bool) $sslVerifyPeer;
        return $this;
    }

    /**
     * Get currently set curl params
     * 
     * @return array
     */
    public function getCurlParams()
    {
        return $this->curlParams;
    }

    /**
     * Add an extra curl parameter or override an existing one
     * 
     * @param string $name  'CURLOPT_*' constant
     * @param mixed  $value
     *
     * @return DefaultServiceRequest
     */
    public function addCurlParam($name, $value)
    {
        $this->curlParams[$name] = $value;
        return $this;
    }

    /**
     * Perform a get request
     * 
     * @param string $url
     * 
     * @return string
     */
    public function get($url)
    {
        $ch = $this->initRequest($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        return $this->execute($ch);
    }

    /**
     * Perform a post request
     *
     * @param string $url
     * @param mixed  $postData
     * 
     * @return string
     */
    public function post($url, $postData)
    {   
        $headers = array(
            "Content-Type: application/atom+xml",
            "Content-Length: " . strlen($postData),
        );
        $ch = $this->initRequest($url, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        return $this->execute($ch);
    }

    /**
     * Perform a put request
     * 
     * @param string $url
     * @param mixed  $postData
     * 
     * @return string
     */
    public function put($url, $postData)
    {
        $headers = array(
            "Content-Type: application/atom+xml",
            "Content-Length: " . strlen($postData),
        );
        $ch = $this->initRequest($url, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        return $this->execute($ch);
    }

    /**
     * Perform a delete request
     * 
     * @param string $url
     * 
     * @return string
     */
    public function delete($url)
    {
        $ch = $this->initRequest($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this->execute($ch);
    }

    /**
     * Initialize the curl session
     * 
     * @param string $url           
     * @param array  $requestHeaders
     * 
     * @return resource
     */
    protected function initRequest($url, $requestHeaders = array())
    {
        if(substr($url, 0, 4) !== "http") {
            $url = $this->serviceUrl . $url;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $this->curlParams);
        curl_setopt($ch, CURLOPT_URL, $url);

        $headers = array();
        if (count($this->getHeaders()) > 0) {
            foreach ($this->getHeaders() as $k => $v) {
                $headers[] = "$k: $v";
            }
        }
        $headers[] = "Authorization: " . $this->tokenType . " " . $this->accessToken;
        $headers = array_merge($headers, $requestHeaders);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        return $ch;       
    }

    /**
     * Executes the api request.
     * 
     * @return string the xml response
     *
     * @throws UnauthorizedException
     * @throws BadRequestException
     *
     * @codeCoverageIgnore
     */
    protected function execute($ch)
    {
        $ret = curl_exec($ch);

        $info = curl_getinfo($ch);
        $httpCode = (int)$info["http_code"];

        if ($httpCode > 299) {
            switch ($httpCode) {
                case 401:
                    throw new UnauthorizedException("Access token is invalid", 401);
                    break;
                case 403:
                    throw new UnauthorizedException($ret, 403);
                    break;
                case 404:
                    throw new UnauthorizedException("You need permission", 404);
                    break;
                default:
                    throw new BadRequestException($ret, $info["http_code"]);
            }
        }
        curl_close($ch);
        return $ret;
    }

}