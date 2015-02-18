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
     * Max retries of redirected request
     *
     * @var int
     */
    protected $retryRedirectsLimit = 3;



    /**
     * Initializes the service request object.
     * 
     * @param \Google\Spreadsheet\Request $request
     */
    public function __construct($accessToken, $tokenType = 'OAuth')
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
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
     * get retryRedirectsLimit
     *
     * @return int
     */
    public function getRetryRedirectsLimit() {
        return $this->retryRedirectsLimit;
    }

    /**
     * Sometimes Google returns a mysterious 302.
     * If so, the request will be retried. Use this method to define the maximum number of retries.
     *
     * @param int $retryRedirectsLimit
     *
     * @return Google\Spreadsheet\DefaultServiceRequest
     */
    public function setRetryRedirectsLimit($retryRedirectsLimit) {
        $this->retryRedirectsLimit = $retryRedirectsLimit;
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
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
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
        $ch = $this->initRequest($url, array('Content-Type: application/atom+xml'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
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
        $ch = $this->initRequest($url, array('Content-Type: application/atom+xml'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
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
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
        $curlParams = array (
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_FAILONERROR => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_VERBOSE => false,
        );

        if(substr($url, 0, 4) !== 'http') {
            $url = $this->serviceUrl . $url;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlParams);
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
     * @throws \Google\Spreadsheet\Exception If the was a problem with the request.
     *                                       Will throw an exception if the response
     *                                       code is 300 or greater
     *                                       
     * @throws \Google\Spreadsheet\UnauthorizedException
     */
    protected function execute($ch)
    {
        $retry_count = 0;
        $response_success = false;

        while ($retry_count < $this->retryRedirectsLimit && $response_success == false) {
            $ret = curl_exec($ch);
            $info = curl_getinfo($ch);
            $httpCode = (int) $info['http_code'];

            if ($httpCode > 299) {
                $retry_count ++;
            } else {
                $response_success = true;
            }
        }

        if ($httpCode > 299) {
            if ($httpCode === 401) {
                throw new UnauthorizedException('Access token is invalid', 401);
            } else {
                throw new Exception('Error in Google Request', $info['http_code']);
            }
        }

        return $ret;
    }

}