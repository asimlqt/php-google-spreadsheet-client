<?php

namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use PHPUnit_Framework_TestCase;
use SimpleXMLElement;
use Google\Spreadsheet\ServiceRequestFactory;

class TestBase extends PHPUnit_Framework_TestCase
{
    protected $serviceUrl = "https://spreadsheets.google.com/feeds/spreadsheets/private/full/";
    
    protected function setServiceRequest($return, $simpleXml = false)
    {
        $serviceRequest = new TestServiceRequest(new Request("accesstoken"));
        
        $xml = file_get_contents(__DIR__."/xml/".$return);
        if($simpleXml) {
            $xml = new SimpleXMLElement($xml);
        }
        
        $serviceRequest->setExecuteReturn($xml);
        ServiceRequestFactory::setInstance($serviceRequest);
    }

    /**
     * Read an xml file from the filessytem and return it as a
     * SimpleXMLElement object.
     * 
     * @param string $file
     * 
     * @return \SimpleXMLElement
     */
    protected function getSimpleXMLElement($file)
    {
        return new \SimpleXMLElement(
            file_get_contents(__DIR__ . "/xml/${file}.xml")
        );
    }
}