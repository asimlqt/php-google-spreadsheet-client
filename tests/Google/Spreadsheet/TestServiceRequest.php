<?php
namespace Google\Spreadsheet;

class TestServiceRequest implements ServiceRequestInterface
{
    private $request;
    private $retVal;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setExecuteReturn($retVal)
    {
        $this->retVal = $retVal;
    }

    public function execute()
    {
        if($this->retVal instanceof \Exception) {
            throw new $this->retVal;
        }
        
        return $this->retVal;
    }

}