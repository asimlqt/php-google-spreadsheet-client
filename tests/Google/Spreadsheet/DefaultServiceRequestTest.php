<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\DefaultServiceRequest;

class DefaultServiceRequestTest extends TestBase
{
    public function testGetHeaders()
    {
        $request = new DefaultServiceRequest("token");
        $this->assertEquals([], $request->getHeaders());
    }

    public function testSetHeaders()
    {
        $request = new DefaultServiceRequest("token");
        $headers = ["k" => "v"];
        $this->assertTrue($request->setHeaders($headers) instanceof DefaultServiceRequest);
        $this->assertEquals($headers, $request->getHeaders());
    }

    public function testAddHeader()
    {
        $request = new DefaultServiceRequest("token");
        $headers = ["k" => "v"];
        $this->assertTrue($request->addHeader("k", "v") instanceof DefaultServiceRequest);
        $this->assertEquals($headers, $request->getHeaders());
    }

    public function testRemoveHeader()
    {
        $request = new DefaultServiceRequest("token");
        $headers = ["k" => "v"];
        $request->addHeader("k", "v");
        $this->assertEquals($headers, $request->getHeaders());
        $this->assertTrue($request->removeHeader("k") instanceof DefaultServiceRequest);
        $this->assertEquals([], $request->getHeaders());
    }

    public function testGetUserAgent()
    {
        $request = new DefaultServiceRequest("token");
        $this->assertEquals("PHP Google Spreadsheet Api", $request->getUserAgent());
    }

    public function testSetUserAgent()
    {
        $request = new DefaultServiceRequest("token");
        $this->assertTrue($request->setUserAgent("my user agent") instanceof DefaultServiceRequest);
        $this->assertEquals("my user agent", $request->getUserAgent());
    }

    public function testGetSslVerifyPeer()
    {
        $request = new DefaultServiceRequest("token");
        $this->assertTrue($request->getSslVerifyPeer());
    }

    public function testSetSslVerifyPeer()
    {
        $request = new DefaultServiceRequest("token");
        $this->assertTrue($request->setSslVerifyPeer(false) instanceof DefaultServiceRequest);
        $this->assertFalse($request->getSslVerifyPeer());
    }

    public function testGetCurlParams()
    {
        $request = new DefaultServiceRequest("token");
        $this->assertTrue(count($request->getCurlParams()) === 5);
    }

    public function testAddCurlParam()
    {
        $request = new DefaultServiceRequest("token");
        $request->addCurlParam(CURLOPT_SSL_VERIFYPEER, false);

        $params = $request->getCurlParams();

        $this->assertFalse($params[CURLOPT_SSL_VERIFYPEER]);
    }

    public function testGet()
    {
        $mockRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["execute"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method("execute")
            ->will($this->returnValue("<entry/>"));

        $this->assertEquals("<entry/>", $mockRequest->get("http://test"));
    }

    public function testPost()
    {
        $mockRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["execute"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method("execute");

        $mockRequest->post("http://test", "");
    }

    public function testPut()
    {
        $mockRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["execute"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method("execute");

        $mockRequest->put("http://test", "");
    }

    public function testDelete()
    {
        $mockRequest = $this->getMockBuilder(DefaultServiceRequest::class)
            ->setMethods(["execute"])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method("execute");

        $mockRequest->delete("http://test");
    }

    public function testInitRequest()
    {
        $method = new \ReflectionMethod(
            DefaultServiceRequest::class,
            "initRequest"
        );
 
        $method->setAccessible(true);
 
        $request = new DefaultServiceRequest("token");
        $request->addHeader("k", "v");

        $result = $method->invoke($request, "http://test");
        $this->assertTrue(is_resource($result));

        $result = $method->invoke($request, "spreadsheet");
        $this->assertTrue(is_resource($result));
    }

}