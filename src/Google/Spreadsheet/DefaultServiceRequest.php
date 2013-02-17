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
 * @version    0.1
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class DefaultServiceRequest implements ServiceRequestInterface
{
    /**
     * Request object
     * 
     * @var \Google\Spreadsheet\Request
     */
    private $request;

    /**
     * Initializes the service request object.
     * 
     * @param \Google\Spreadsheet\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the request object
     * 
     * @return \Google\Spreadsheet\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Executes the api request.
     * 
     * @return string the xml response
     *
     * @throws \Google\Spreadsheet\Exception If the was a problem with the request.
     *                                       Will throw an exception if the response
     *                                       code is 300 or greater
     */
    public function execute()
    {
        $curlParams = array (
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_FAILONERROR => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_VERBOSE => false,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $curlParams);
        curl_setopt($ch, CURLOPT_URL, $this->request->getUrl());

        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'PUT') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request->getPost());
        }

        $headers = array();
        if (count($this->request->getHeaders()) > 0) {
            foreach ($this->request->getHeaders() as $k => $v) {
                $headers[] = "$k: $v";
            }
        }
        $headers[] = "Authorization: OAuth " . $this->request->getAccessToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->request->getMethod());
        curl_setopt($ch, CURLOPT_USERAGENT, $this->request->getUserAgent());
        $ret = curl_exec($ch);

        $info = curl_getinfo($ch);
        if((int)$info['http_code'] > 299) {
            $exception = new Exception('Error in Google Request: '. $ret, $info['http_code']);
            $exception->setRequest($this->request);
            $this->resetRequestParams();
            throw $exception;
        }

        $this->resetRequestParams();
        return $ret;
    }

    /**
     * Resets the properties of the request object to avoid unexpected behaviour
     * when making more than one request using the same request object.
     * 
     * @return void
     */
    private function resetRequestParams()
    {
        $this->request->setMethod(Request::GET);
        $this->request->setPost('');
        $this->request->setFullUrl(null);
        $this->request->setEndpoint('');
        $this->request->setHeaders(array());
    }
}