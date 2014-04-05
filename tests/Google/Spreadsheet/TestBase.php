<?php

namespace Google\Spreadsheet;

use PHPUnit_Framework_TestCase;

class TestBase extends PHPUnit_Framework_TestCase
{
    protected function setServiceRequest($return)
    {
        $serviceRequest = new TestServiceRequest(new Request('accesstoken'));
        $serviceRequest->setExecuteReturn(file_get_contents(__DIR__.'/xml/'.$return));
        ServiceRequestFactory::setInstance($serviceRequest);
    }
}