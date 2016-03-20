<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\ServiceRequestInterface;

class ServiceRequestFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetInstance()
    {
        ServiceRequestFactory::setInstance(new TestServiceRequest());
        $this->assertTrue(ServiceRequestFactory::getInstance() instanceof ServiceRequestInterface);
    }

    /**
     * @expectedException Exception
     */
    public function testGetInstanceException()
    {
        ServiceRequestFactory::setInstance(null);
        ServiceRequestFactory::getInstance();
    }
}