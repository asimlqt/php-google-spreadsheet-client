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
 * Service Request. The parent class of all services.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class StreamServiceRequest implements ServiceRequestInterface
{
    /**
     * Request object
     * 
     * @var \Google\Spreadsheet\Request
     */
    protected $accessToken;

    /**
     * Request headers
     * 
     * @var array
     */
    protected $headers = array();

    /**
     * Service url
     * 
     * @var string
     */
    protected $serviceUrl = 'https://spreadsheets.google.com/';

    /**
     * User agent
     * 
     * @var string
     */
    protected $userAgent = 'PHP Google Spreadsheet Api';

    /**
     * Initializes the service request object.
     * 
     * @param \Google\Spreadsheet\Request $request
     */
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
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
     * Perform a get request
     * 
     * @param string $url
     * 
     * @return string
     */
    public function get($url)
    {
        $contextParams = $this->getContextParams("GET");
        return $this->execute($url, $contextParams);
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
        $contextParams = $this->getContextParams("POST", $postData);
        return $this->execute($url, $contextParams);
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
        $contextParams = $this->getContextParams("PUT", $postData);
        return $this->execute($url, $contextParams);
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
        $contextParams = $this->getContextParams("DELETE");
        return $this->execute($url, $contextParams);
    }

    /**
     * 
     * @param string $method
     * @param array  $postData
     * 
     * @return array
     */
    protected function getContextParams($method, array $postData = array())
    {
        $headers = array(
            "Authorization: OAuth " . $this->accessToken,
            "User-Agent: " . $this->getUserAgent()
        );
        
        $content = null;
        if(count($postData) > 0) {
            $headers[] = "Content-Type: application/atom+xml";
            $content = http_build_query($postData);
        }
        
        $params = array(
            "http" => array(
                "method" => $method,
                "header" => implode("\r\n", $headers) . "\r\n"
            )
        );
        
        if($content !== null) {
            $params["http"]["content"] = $content;
        }
        
        return $params;
    }
    
    /**
     * Executes the api request.
     * 
     * @return string the xml response
     *
     * @throws \Google\Spreadsheet\Exception If the was a problem with the request.
     *                                       Will throw an exception if the response
     *                                       code is 300 or greater
     *                                       
     * @throws \Google\Spreadsheet\UnauthorizedException
     */
    protected function execute($url, $contextParams)
    {
        if(substr($url, 0, 4) !== 'http') {
            $url = $this->serviceUrl . $url;
        }
        
        $context = stream_context_create($contextParams);
        $ret = file_get_contents($url, false, $context);

        if(count($http_response_header) === 0) {
            throw new Exception("Error ");
        }
        
        preg_match("@^HTTP/1.0 (\d{3})@", $http_response_header[0], $matches);
        if(count($matches) !== 2) {
            throw new Exception("Unkown Error");
        }
        
        $httpCode = (int) $matches[1];

        if($httpCode > 299) {
            if($httpCode === 401) {
                throw new UnauthorizedException('Access token is invalid', 401);
            } else {
                throw new Exception('Error in Google Request', $httpCode);
            }
        }

        return $ret;
    }

}