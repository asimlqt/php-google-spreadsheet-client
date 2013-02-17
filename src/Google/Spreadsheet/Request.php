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
 * Request
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @version    0.1
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class Request
{
    /**
     * Http method
     * 
     * @var string
     */
    private $method = 'GET';

    /**
     * Request headers
     * 
     * @var array
     */
    private $headers = array();

    /**
     * Service url
     * 
     * @var string
     */
    private $serviceUrl = 'https://spreadsheets.google.com/';

    /**
     * Post body. Only used for POST and PUT requests
     * 
     * @var string
     */
    private $post = '';

    /**
     * Google OAuth access token
     * 
     * @var string
     */
    private $accessToken;

    /**
     * Url endpoint
     * 
     * @var string
     */
    private $endpoint;

    /**
     * User agent
     * 
     * @var string
     */
    private $userAgent = 'Byng Drive';

    /**
     * Full url of the api resource
     * 
     * @var string
     */
    private $fullUrl;

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    /**
     * Initializes the request object.
     * 
     * @param string $accessToken Oauth access token
     */
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Returns the complete request url.
     * 
     * @return string
     */
    public function getUrl()
    {
        if(!is_null($this->fullUrl))
            $url = $this->fullUrl;
        else
            $url = $this->serviceUrl . $this->endpoint;
        //return  $url . '?access_token=' . $this->accessToken;
        return $url;
    }

    /**
     * Set the full url of the request. If this is set then the get url
     * method will ignore the serviceUrl and endpoint properties. This is useful
     * when extracting the url from an xml feed.
     * 
     * @param string $url
     *
     * @return Google\Spreadsheet\Request
     */
    public function setFullUrl($url)
    {
        $this->fullUrl = $url;
        return $this;
    }

    /**
     * Returns the endpoint
     * 
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
    
    /**
     * Set the end point
     * 
     * @param string $endpoint
     *
     * @return Google\Spreadsheet\Request
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }
    
    /**
     * Get the http request method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Set the http request method. Use one of the class constants provided.
     * 
     * @param string $method
     *
     * @return Google\Spreadsheet\Request
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }
    
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * Set optional request headers. 
     * 
     * @param array $headers associative array of key value pairs
     *
     * @return Google\Spreadsheet\Request
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }
    
    /**
     * Get the post body
     * 
     * @return string
     */
    public function getPost()
    {
        return $this->post;
    }
    
    /**
     * Set the post body. Must be called if it is a POST or a PUT request
     * 
     * @param string $post
     *
     * @return Google\Spreadsheet\Request
     */
    public function setPost($post)
    {
        $this->post = $post;
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
     * @return Google\Spreadsheet\Request
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }
    
}