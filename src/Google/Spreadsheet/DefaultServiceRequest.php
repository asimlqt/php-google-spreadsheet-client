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
     * Access token
     * 
     * @var string
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
     * Initializes the service request object.
     * 
     * @param string $accessToken
     * @param string $tokenType
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
     * {@inheritdoc}
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
     * @return DefaultServiceRequest
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($url)
    {
        $ch = $this->initRequest($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        return $this->execute($ch);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $postData)
    {   
        $headers = array(
            'Content-Type: application/atom+xml',
            'Content-Length: ' . strlen($postData),
        );
        $ch = $this->initRequest($url, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        return $this->execute($ch);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $postData)
    {
        $headers = array(
            'Content-Type: application/atom+xml',
            'Content-Length: ' . strlen($postData),
        );
        $ch = $this->initRequest($url, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        return $this->execute($ch);
    }

    /**
     * {@inheritdoc}
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
            CURLOPT_FOLLOWLOCATION => true,
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
     * @param  resource $ch
     * @return string the xml response
     *
     * @throws Exception If the was a problem with the request.
     *                   Will throw an exception if the response
     *                   code is 300 or greater
     *
     * @throws UnauthorizedException
     */
    protected function execute($ch)
    {
        $ret = curl_exec($ch);

        $info = curl_getinfo($ch);
        $httpCode = (int)$info['http_code'];

        if ($httpCode > 299) {
            switch ($httpCode) {
                case 401:
                    throw new UnauthorizedException('Access token is invalid', 401);
                    break;
                case 404:
                    throw new UnauthorizedException('You need permission', 404);
                    break;
                default:
                    throw new Exception('Error in Google Request', $info['http_code']);
            }
        }

        return $ret;
    }

}
